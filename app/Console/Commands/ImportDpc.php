<?php
/**
 * ImportDpc class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

use App\Dpc;
use App\User;

/*
 *
 * php artisan import:dpc
 */

class ImportDpc extends Command
{
    protected $signature = 'import:dpc';
    protected $description = 'This is test';

    private $userId = null;
    private $efFilePath = null;
    private $hFilePath = null;
    private $code = null;
    private $endDate = null;

    public function __construct()
    {
        parent::__construct();

        $this->userId = 1;
        $this->efFilePath = '/vagrant/storage/uploads/dpc/ef_file.tsv';
        $this->hFilePath = '/vagrant/storage/uploads/dpc/h_file.tsv';
        $this->code = 1;
        $this->endDate = Carbon::today()->format('Y-m-d');
    }

    public function handle()
    {
        $res = Dpc::import(
            $this->userId,
            $this->efFilePath,
            $this->hFilePath,
            $this->code,
            $this->endDate
        );

        // 処理中フラグオフ
        $status = ($res)? User::$dpc_status_successfully : User::$dpc_status_failed;
        $user = User::findOrFail($this->userId);
        $user->is_dpc_loading = 0;
        $user->dpc_import_status = $status;
        $user->save();

        return true;
    }
}
