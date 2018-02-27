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
        $system = SystemSetting::firstOrNew(['id' => SystemSetting::$id]);

        return view('system.index', compact(
            'system'
        ));
    }

    public function edit()
    {
        $system = SystemSetting::firstOrNew(['id' => SystemSetting::$id]);

        return view('system.edit', compact(
            'system'
        ));
    }

    public function update(SystemRequest\UpdateRequest $request)
    {
        $system = SystemSetting::firstOrCreate(['id' => SystemSetting::$id]);

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
