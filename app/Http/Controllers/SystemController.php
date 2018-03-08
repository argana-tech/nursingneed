<?php
/**
 * SystemController class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Controllers;

use App\Http\Requests\System as SystemRequest;
use App\SystemSetting;

class SystemController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $system = SystemSetting::firstOrNew(['user_id' => $user->id]);

        return view('system.index', compact(
            'system'
        ));
    }

    public function edit()
    {
        $user = auth()->user();
        $system = SystemSetting::firstOrNew(['user_id' => $user->id]);

        return view('system.edit', compact(
            'system'
        ));
    }

    public function update(SystemRequest\UpdateRequest $request)
    {
        $user = auth()->user();
        $system = SystemSetting::firstOrCreate(['user_id' => $user->id]);

        $systemData = $request->only([
            'intensive_ward',
            'obstetrics_ward',
            'child_operation_name',
        ]);

        if ($system->update($systemData)) {
            return redirect()
                ->route('system.index')
                ->with(['info' => 'システム設定を変更しました。'])
            ;
        }

        return redirect()
            ->back()
            ->withInput($systemData)
            ->with(['error' => '保存に失敗しました。'])
        ;
    }

}
