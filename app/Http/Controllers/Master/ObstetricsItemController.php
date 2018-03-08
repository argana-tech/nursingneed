<?php
/**
 * ObstetricsItemController class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ObstetricsItem as ObstetricsItemRequest;
use App\ObstetricsItem;

class ObstetricsItemController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $items = $user->obstetricsItems;

        return view('master.obstetrics_item.index', compact(
            'items'
        ));
    }

    public function edit($id)
    {
        $user = auth()->user();
        $item = ObstetricsItem::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        return view('master.obstetrics_item.edit', compact(
            'item'
        ));
    }

    public function update(ObstetricsItemRequest\UpdateRequest $request, $id)
    {
        $user = auth()->user();
        $item = ObstetricsItem::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $itemData = $request->only([
            'name',
            'code',
            'kcode',
            'remark',
        ]);

        if ($item->update($itemData)) {
            return redirect()
                ->route('obstetrics_items.index')
                ->with(['info' => '産科項目を変更しました。'])
            ;
        }

        return redirect()
            ->back()
            ->withInput($itemData)
            ->with(['error' => '保存に失敗しました。'])
        ;
    }

    public function upload(ObstetricsItemRequest\UploadRequest $request)
    {
        $item = new ObstetricsItem();

        if ($item->insertTsvdata($request->file('tsv_file'))) {
            return redirect()
                ->route('obstetrics_items.index')
                ->with(['info' => '産科項目を変更しました。'])
            ;
        }

        return redirect()
            ->back()
            ->with(['error' => '保存に失敗しました。'])
        ;
    }

    public function download()
    {
        $csv = ObstetricsItem::getCsvdata();

        // response
        $filename = 'sanka_master.txt';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Length' => strlen($csv),
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return \Response::make(
            $csv,
            200,
            $headers
        );
    }
}
