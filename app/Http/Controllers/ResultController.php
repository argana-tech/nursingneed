<?php
/**
 * ResultController class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Result;
use App\ResultTargetDay;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->guard('web')->user();

        $search = $request->only([
            'identification_id',
            'select',
        ]);

        $results = $user->results();

        if (isset($search['identification_id']) && $search['identification_id'] != ''){
            $results = $results->where('identification_id', '=', $search['identification_id']);
        }
        if (isset($search['select']) && $search['select'] == 'child'){
            $results = $results->where('is_child', '=', 1);
        } elseif (isset($search['select']) && $search['select'] == 'obstetrics'){
            $results = $results->where('is_obstetrics', '=', 1);
        } else {
            $results = $results->where('is_child', '=', 0)->where('is_obstetrics', '=', 0);
        }

        $results = $results->paginate(50);

        // 最小、最大月
        $resultMinDate = $user->resultTargetDays->min('date');

        $resultMaxDate = $user->resultTargetDays->max('date');

        if (!$resultMinDate)
            $resultMinDate = Carbon::today()->format('Y-m-d');

        // 対象月
        if (!($month = $request->input('month'))){
            $month = Carbon::parse($resultMinDate)->format('Y-m');
        }

        // 更新日時
        $updatedResult = $user->dpc_imported_at;

        return view('result.index', compact(
            'results',
            'search',
            'month',
            'resultMinDate',
            'resultMaxDate',
            'updatedResult'
        ));
    }

    public function show(Request $request, $id)
    {
        $result = Result::findOrFail($id);

        // 対象月
        if (!($month = $request->input('month'))){
            $month = Carbon::parse($resultMinDate)->format('Y-m');
        }

        return view('result.show', compact(
            'month',
            'result'
        ));
    }

}
