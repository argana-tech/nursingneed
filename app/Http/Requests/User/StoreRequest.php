<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use App\User;

class StoreRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'max:100',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,NULL,id',
            ],
            'password' => [
                'required',
                'between:6,20',
                'ascii',
            ],
        ];
    }

    /**
     * Get the validation error messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => '名前は必ず入力してください',
            'name.max' => '名前は:max文字以内で入力してください',
            'email.required' => 'メールアドレスは必ず入力してください',
            'email.email' => 'メールアドレスを正しく入力してください',
            'email.max' => 'メールアドレスは:max文字以内で入力してください',
            'email.unique' => '入力したメールアドレスは既に登録されています',
            'password.required' => 'パスワードは必ず入力してください',
            'password.between' => 'パスワードは:min〜:max文字で入力してください',
            'password.ascii' => 'パスワードを正しく入力してください',
        ];
    }
}
