#!/usr/bin/perl

binmode(STDOUT, ":utf8");

use strict;

use lib qw(./extlib);

use CGI::Carp qw(fatalsToBrowser);
use CGI;
use Template::Provider::Encoding;
use Template::Stash::ForceUTF8;
use Template;
use YAML;
use Mail::Mailer;
use Jcode;
use Email::Valid::Loose;
use Digest::MD5;
use FileHandle;
use CGI::Cookie;
use Unicode::Normalize;
use Encode;

our $cgi = CGI->new;
our %config = %{ YAML::LoadFile('config.yml') };
our %cookie;
our %param;

if ($cgi->param('mode') eq '') {
	show_form();
}
elsif ($cgi->param('mode') eq 'token') {
	show_token();
}
elsif ($cgi->param('mode') eq 'submit') {
	submit_form();
}
else {
	die "Can't handle " . $cgi->param('mode') . "\n";
}


sub show_form {
	$cookie{token} = check_token() || generate_token();
	output('form.tt');
}

sub show_token {
	my $token = check_token() || generate_token();

	print "Content-Type: text/plain\n\n";
	print $token;
}

sub submit_form {
	my $token_ok = check_token(raise_cookie_error => 1);
	my $input_ok = check_input();

	if ($token_ok && $input_ok) {
		send_email();
		show_thanks();
	}
	else {
		show_form();
	}
}

sub show_thanks {
	destroy_token();
	output('thanks.tt');
}

sub generate_token {
	my $digest = Digest::MD5->new;
	$digest->add($^T.$$.rand());

	my $token = $digest->hexdigest;

	my $fh = FileHandle->new("$config{tokens_dir}/$token", O_RDWR|O_CREAT)
		or die "Can't create token: $token\n";
	undef $fh;

	$token;
}

sub destroy_token {
	my $token = $cookie{token};
	unlink "$config{tokens_dir}/$token";

	unlink $_ for
		grep { time - (stat($_))[9] >= $config{token_expires} }
			glob "$config{tokens_dir}/*";

	%cookie = ();
}

sub check_token {
	my %arg = @_;

	my $cookie_name = $config{cookie}->{name};

	my %jar = CGI::Cookie->fetch;
	my $c = $jar{$cookie_name};

	if ($c) {
		%cookie = (%cookie, $c->value);
	}
	else {
		$param{error} = 'クッキーを取得できません。'
			if $arg{raise_cookie_error};
		return;
	}

	my $file = "$config{tokens_dir}/$cookie{token}";

	if (-e $file && time - (stat($file))[9] < $config{token_expires}) {
		return $cookie{token};
	}
	else {
		$param{error} = '期限切れです。';
	}

	return;
}

sub check_input {
	my $icode = $config{icode};
	my $ocode = $config{ocode};

	for my $field (@{ $config{form}->{fields} }) {
		my $value = $cgi->param($field->{key});
		$value = Jcode->new($value, $ocode)->$icode if $icode ne $ocode;

		if ($icode eq 'utf8') {
			$value = Encode::decode('utf8', $value);
			$value = Unicode::Normalize::NFKC($value);
			#$value = Encode::encode('utf8', $value);
		}

		$field->{value} = $value;

		if ($field->{required} && !$value) {
			set_error();
			#$field->{error} = '必須項目です。',
			$field->{error} = '※入力してください。';
		}
		elsif ($field->{length} && length $value > $field->{length}) {
			set_error();
			$field->{error}
				= sprintf '%s文字程度以内で入力してください。', (
					$field->{is_ascii} ? $field->{length}
						               : int($field->{length} / 2)
				);
		}
		elsif ($field->{is_email} && !check_is_email($value)) {
			set_error();
			$field->{error} = '正しく入力してください。',
		}

		if ($field->{is_email}) {
			$param{mail}->{from} = $value;
		}
		elsif ($field->{key} eq 'subject') {
			$param{mail}->{subject} = $value;
		}

		$param{mail}->{body} .= $field->{label} . ': ' . $value . "\n";
	}

	$param{mail}->{body} =~ s/\n+/\n/g;
	$param{mail}->{body} =~ s/\n$//g;

	return $param{error} ? 0 : 1;
}

sub check_is_email {
	my $addr = shift;
	return Email::Valid::Loose->address($addr);
}

sub set_error {
	my %arg = @_;

	$param{error} .= "$arg{message}<br />\n"
		if $arg{message};

	$param{error} = '入力内容を確認してください。'
		if !$param{error};
}

sub send_email {
	my %header = (
		'Content-Type' => 'text/plain; charset="ISO-2022-JP"',
		From => $param{mail}->{from} || $config{mail}->{from},
		To => $config{mail}->{to},
		Subject
			#=> join ' - ', $config{mail}->{subject}, $param{mail}->{subject},
			=> $config{mail}->{subject},
	);
	$header{Subject}
		= Jcode->new($header{Subject}, $config{icode})->mime_encode;

	my $body = join "\n\n",
		$config{mail}->{body_pre},
		$param{mail}->{body},
		$config{mail}->{body_post},
		;

	#$body = Encode::decode('utf8', $body);
	#$body =~ tr/[\x{ff5e}\x{2225}\x{ff0d}\x{ffe0}\x{ffe1}\x{ffe2}]/[\x{301c}\x{2016}\x{2212}\x{00a2}\x{00a3}\x{00ac}]/;
	#$body = Encode::encode('utf8', $body);

	$body = Jcode->new($body, $config{icode})->jis;

	my $mailer = Mail::Mailer->new;
	$mailer->open(\%header);
	print $mailer $body;
	$mailer->close;
}

sub output {
	my $template = shift;

	$config{template}{STASH} = Template::Stash::ForceUTF8->new;

	my $tt = Template->new($config{template})
		or die $Template::ERROR . "\n";

	$tt->process(
		$template,
		{ r => $cgi, c => \%config, p => \%param },
		\my $output,
	) or die $tt->error;

	my $icode = $config{icode};
	my $ocode = $config{ocode};

	my $cookie = CGI::Cookie->new(
		-name => $config{cookie}->{name},
		-value => \%cookie,
		(%cookie ? () : (-expires => 'now')),
	);

	print "Set-Cookie: $cookie\n";
	print "Content-Type: text/html\n\n";
	print
		$icode ne $ocode ? Jcode->new($output, $icode)->$ocode
		                 : $output
		;
}

exit;
__END__
