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
use App\WardCsv;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->guard('web')->user();

        $search = $request->only([
            'identification_id',
            'select',
            'ward',
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
        // 病棟 EF, Hに存在するか
        if (isset($search['ward']) && $search['ward'] != ''){
            $results = $results->whereIn('id', function ($query) use ($search)
                {
                    $query->select('result_id')
                        ->from('result_target_days')
                        ->orWhere('h_ward', $search['ward'])
                        ->orWhere('ef_ward', $search['ward']);
                }
            );
        }

        $results = $results->paginate(50);

        // 最小月
        $resultMinDate = $user->resultTargetDays();
        if (isset($search['ward']) && $search['ward'] != ''){
            $resultMinDate = $resultMinDate
                ->orWhere('h_ward', $search['ward'])
                ->orWhere('ef_ward', $search['ward']);
        }
        $resultMinDate = $resultMinDate->min('date');

        // 最大月
        $resultMaxDate = $user->resultTargetDays();
        if (isset($search['ward']) && $search['ward'] != ''){
            $resultMaxDate = $resultMaxDate
                ->orWhere('h_ward', $search['ward'])
                ->orWhere('ef_ward', $search['ward']);
        }
        $resultMaxDate = $resultMaxDate->max('date');


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

    /**
     * 病棟別入力割合CSVダウンロード
     *
     * @param void
     *
     * @return \Illuminate\View\View
     */
    public function download()
    {
        $csv = WardCsv::download();

        // response
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Length' => strlen($csv),
            'Content-Disposition' => 'attachment; filename="wards.csv"',
        ];

        return \Response::make(
            $csv,
            200,
            $headers
        );
    }

}
