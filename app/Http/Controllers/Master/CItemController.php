<?php
/**
 * CItemController class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Controllers\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CItem as CItemRequest;
use App\CItem;

class CItemController extends Controller
{
    public function index()
    {
        $items = CItem::All();

        return view('master.c_item.index', compact(
            'items'
        ));
    }

    public function edit($id)
    {
        $item = CItem::findOrFail($id);

        return view('master.c_item.edit', compact(
            'item'
        ));
    }

    public function update(CItemRequest\UpdateRequest $request, $id)
    {
        $item = CItem::findOrFail($id);

        $itemData = $request->only([
            'days',
            'name',
            'code',
            'remark',
        ]);

        if ($item->update($itemData)) {
            return redirect()
                ->route('c_items.index')
                ->with(['info' => 'C項目を変更しました。'])
            ;
        }

        return redirect()
            ->back()
            ->withInput($itemData)
            ->with(['error' => '保存に失敗しました。'])
        ;
    }

    public function upload(CItemRequest\UploadRequest $request)
    {
        $item = new CItem();

        if ($item->insertTsvdata($request->file('tsv_file'))) {
            return redirect()
                ->route('c_items.index')
                ->with(['info' => 'C項目を変更しました。'])
            ;
        }

        return redirect()
            ->back()
            ->with(['error' => '保存に失敗しました。'])
        ;
    }

    public function download()
    {
        $csv = CItem::getCsvdata();

        // response
        $filename = 'c_master.txt';
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
