<?php
/**
 * UpdateRequest class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Requests\System;
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
            'intensive_ward' => [
            ],
        ];
    }

    public function messages()
    {
        return [
            'intensive_ward.required' => '集中病棟名を入力してください',
        ];
    }
}
