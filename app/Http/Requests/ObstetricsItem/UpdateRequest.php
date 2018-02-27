<?php
/**
 * UpdateRequest class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Requests\ObstetricsItem;
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
            'name' => [
                'required',
            ],
            'code' => [
                'required',
                'integer',
            ],
            'kcode' => [
                //'required',
            ],
            'remark' => [
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '名称を入力してください',
            'code.required' => 'コードを入力してください',
            'code.integer' => 'コードは数値を入力してください',
            'kcode.required' => 'Kコードを入力してください',
        ];
    }
}
