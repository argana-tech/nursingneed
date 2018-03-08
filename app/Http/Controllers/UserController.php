<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;
use Carbon\Carbon;
use DB;

use App\Http\Requests\User as UserRequest;
use App\User;

class UserController extends Controller
{
    public function create()
    {
        return response()
            ->view('user.create')
            ->header('Cache-Control', 'no-cache, no-store')
            ;
    }

    public function store(UserRequest\StoreRequest $request)
    {
        $userData = $request->only([
            'name','email', 'password',
        ]);

        $userData['password'] = bcrypt($userData['password']);

        $errors = [];
        \DB::beginTransaction();

        if ($user = User::create($userData)) {
            if (empty($errors)) {
                \DB::commit();

                // insert default master
                $user->firstImport();

                // ログイン状態にしてリダイレクト
                auth()->guard('web')->loginUsingId($user->getKey());
                return redirect()
                    ->route('root.index')
                    ->with(['info' => '会員登録が完了しました。'])
                ;
            }
        }

        \DB::rollBack();

        return redirect()
            ->back()
            ->withInput($request->all())
            ->withErrors($errors);
            ;
    }
}
