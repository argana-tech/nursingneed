<?php
/**
 * UploadRequest class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Requests\Dpc;
use App\Http\Requests\Request;

class UploadRequest extends Request
{
    public function authorize()
    {
        return \Auth::guard('web')->check();
    }

    public function rules()
    {
        return [
            'ef_file' => [
                'required',
            ],
            'h_file' => [
                'required',
            ],
            'code' => [
                'integer',
                'max:999999',
            ],
            'end_date' => [
                'date',
            ],
        ];
    }

    public function messages()
    {
        return [
            'ef_file.required' => 'EFファイルを選択してください',
            'h_file.required' => 'Hファイルを選択してください',
            'code.integer' => '暗号化コードには数値を入力してください',
            'code.max' => '暗号化コードは:max以下の数値を入力してください',
            'end_date.date' => '最終日には日付を入力してください',
        ];
    }
}
