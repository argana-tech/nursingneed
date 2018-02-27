<?php
/**
 * AItemController class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AItem as AItemRequest;
use App\AItem;

class AItemController extends Controller
{
    public function index()
    {
        $items = AItem::All();

        return view('master.a_item.index', compact(
            'items'
        ));
    }

    public function edit($id)
    {
        $item = AItem::findOrFail($id);

        return view('master.a_item.edit', compact(
            'item'
        ));
    }

    public function update(AItemRequest\UpdateRequest $request, $id)
    {
        $item = AItem::findOrFail($id);

        $itemData = $request->only([
            'name',
            'code',
            'remark',
        ]);

        if ($item->update($itemData)) {
            return redirect()
                ->route('a_items.index')
                ->with(['info' => 'A項目を変更しました。'])
            ;
        }

        return redirect()
            ->back()
            ->withInput($itemData)
            ->with(['error' => '保存に失敗しました。'])
        ;
    }

    public function upload(AItemRequest\UploadRequest $request)
    {
        $item = new AItem();

        if ($item->insertTsvdata($request->file('tsv_file'))) {
            return redirect()
                ->route('a_items.index')
                ->with(['info' => 'A項目を変更しました。'])
            ;
        }

        return redirect()
            ->back()
            ->with(['error' => '保存に失敗しました。'])
        ;
    }

    public function download()
    {
        $csv = AItem::getCsvdata();

        // response
        $filename = 'a_master.txt';
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
