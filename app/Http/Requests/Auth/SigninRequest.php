<?php
/**
 * SigninRequest class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Requests\Auth;
use App\Http\Requests\Request;

class SigninRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
            ],
            'password' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'E-Mailを入力してください',
            'password.required' => 'パスワードを入力してください',
        ];
    }
}
