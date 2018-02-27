<?php
/**
 * UploadRequest class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Requests\AItem;
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
            'tsv_file' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'tsv_file.required' => 'ファイルを選択してください',
        ];
    }
}
