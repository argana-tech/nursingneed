<?php
/**
 * DpcController class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Dpc as DpcRequest;
use Validator;
use Illuminate\Support\Facades\Log;
use App\SystemSetting;
use App\Dpc;
use App\User;
use App\Jobs\DpcImportJob;

class DpcController extends Controller
{
    public function index()
    {
        return view('dpc.index');
    }

    public function upload(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'ef_file' => 'required',
                'h_file' => 'required',
                'code' => [
                    'integer',
                    'max:999999',
                ],
                'end_date' => 'date',
            ],
            [
                'ef_file.required' => 'EFファイルを選択してください',
                'h_file.required' => 'Hファイルを選択してください',
                'code.integer' => '暗号化コードには数値を入力してください',
                'code.max' => '暗号化コードは:max以下の数値を入力してください',
                'end_date.date' => '最終日には日付を入力してください',
            ]
        );

        if ($validator->fails()) {
            return response()
                ->view('dpc.upload', [
                    'errorMessages' => $validator->errors(),
                ])
                ;
        }

        // code
        $code = ($request->input('chk_code'))? $request->input('code') : 0;

        // upload and get path
        $dpc = new Dpc();
        $efFile = $dpc->efUpload($request->file('ef_file'));
        $hFile = $dpc->hUpload($request->file('h_file'));

        // 処理中フラグオン
        $user = auth()->user();
        $user->is_dpc_loading = 1;
        $user->dpc_import_status = User::$dpc_status_successfully;
        $user->save();

        // 非同期処理
        Log::error('dispatch DpcImportJob start');
        dispatch(new DpcImportJob(
            $user->id,
            $efFile,
            $hFile,
            $code,
            $request->input('end_date')
        ));
        Log::error('dispatch DpcImportJob end');

        $request->session()->flash('info', '取込を開始しました。');

        return response()
            ->view('dpc.upload', [
                'errorMessages' => false,
            ])
            ;
    }
}
