<?php
/**
 * UpdateRequest class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Requests\CItem;
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
            'days' => [
                'required',
                'integer',
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
            'days.required' => '日数を入力してください',
            'days.integer' => '日数は数値を入力してください',
            'name.required' => '名称を入力してください',
            'code.required' => 'コードを入力してください',
        ];
    }
}
