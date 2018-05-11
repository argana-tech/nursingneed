<?php
/**
 * UpdateRequest class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Requests\AItem;
use App\Http\Requests\Request;

class UpdateRequest extends Request
{
    public function authorize()
    {
        return \Auth::guard('web')->check();
    }

    public function rules()
    {
        return [
            'payload' => [
                'required',
            ],
            'name' => [
                'required',
            ],
            'code' => [
                'required',
            ],
            'remark' => [
            ],
        ];
    }

    public function messages()
    {
        return [
            'payload.required' => 'ペイロード番号を入力してください',
            'name.required' => '名称を入力してください',
            'code.required' => 'コードを入力してください',
        ];
    }
}
