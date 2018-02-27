<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

use App\Estimate;
use App\Observers\EstimateCreatedAccountObserver;
use App\Services\CustomValidator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // バリデーションルール ascii の追加
        // 英数字記号のみかどうかチェックする
        Validator::extend(
            'ascii',
            function ($attribute, $value, $parameter, $validator) {
                return preg_match('/^[a-zA-Z0-9\s\x21-\x2f\x3a-\x40\x5b\x5D-\x60\x7b-\x7e]+$/', $value);
            }
        );

        Validator::extend(
            'authorizer_not_in',
            function ($attribute, $value, $parameter, $validator) {
                return !in_array(array_get($validator->getData(), 'basic.account_id'), $value);
            }
        );

        // バリデーションルールの追加
        // 半角相当で指定された文字数以内かどうかチェック
        Validator::extend(
            'ascii_max',
            function ($attribute, $value, $parameter, $validator) {
                $max = array_shift($parameter);
                $value = \Normalizer::normalize($value, \Normalizer::FORM_C); /* 全角は全角のまま */

                // 文字エンコーディングを SJIS-Win に変換してからstrlenでカウントする
                $value = mb_convert_encoding($value, 'Sjis-win', 'UTF-8');

                return strlen($value) <= $max;
            }
        );

        // カタカナ
        Validator::extend(
            'kana',
            function ($attribute, $value, $parameter, $validator) {
                if(preg_match('/^[ァ-ヶー]+$/u', $value)) 
                    return true;

                return false;
            }
        );

        // カスタム
        Validator::resolver(function($translator,$data,$rules,$messages){
            return new CustomValidator($translator,$data,$rules,$messages);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
