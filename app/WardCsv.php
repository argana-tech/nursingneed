<?php

namespace App;

use Carbon\Carbon;

use App\Result;
use App\ResultTargetDay;

class WardCsv
{
    public static function download()
    {
        $user = auth()->guard('web')->user();
        $wards = ResultTargetDay::getCreatedWards();

        // 最小月
        $resultMinDate = $user->resultTargetDays();
        $resultMinDate = $resultMinDate->min('date');

        // 最大月
        $resultMaxDate = $user->resultTargetDays();
        $resultMaxDate = $resultMaxDate->max('date');

        $firstDate = Carbon::parse($resultMinDate);
        $endDate = Carbon::parse($resultMaxDate);

        $targetDays = [];
        $targetDate = $firstDate;

        while($targetDate->format('Y-m-d') <= $endDate->format('Y-m-d')) {
            $date = $targetDate->format('Y-m-d');
            $targetWard = [];
            foreach($wards as $ward) {
                $hCount = $user->resultTargetDayHCount($ward, $date);
                $efCount = $user->resultTargetDayEFCount($ward, $date);
                $matchCount = $user->resultTargetDayMatchCount($ward, $date);

                $hRatio = ($hCount)? floor($matchCount / $hCount * 10000) / 100 : 0;
                $efRatio = ($efCount)? floor($matchCount / $efCount * 10000) / 100 : 0;

                $targetWard[$ward] = [
                    'h_count' => $hCount,
                    'ef_count' => $efCount,
                    'h_ratio' => $hRatio . '%',
                    'ef_ratio' => $efRatio . '%',
                ];
            }

            $targetDays[$date] = $targetWard;
            $targetDate->addDay();
        }

        // ラベル
        $labels = ['日付'];
        foreach($wards as $ward) {
            $labels[] = $ward . ' Hファイル入力件数';
            $labels[] = $ward . ' EFファイル入力件数';
            $labels[] = $ward . ' HファイルからみたEFファイル入力割合';
            $labels[] = $ward . ' EFファイルからみたHファイル入力割合';
        }

        $stream = fopen('php://temp', 'r+b');

        fputcsv(
            $stream,
            $labels
        );

        foreach($targetDays as $date => $wards) {
            $data = [$date];
            foreach($wards as $ward => $ratio) {
                $data[] = $ratio['h_count'];
                $data[] = $ratio['ef_count'];
                $data[] = $ratio['h_ratio'];
                $data[] = $ratio['ef_ratio'];
            }

            fputcsv(
                $stream,
                $data,
                ",",
                '"'
            );
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        // convert
        $csv = str_replace(
            PHP_EOL,
            "\r\n",
            $csv
        );

        $csv = mb_convert_encoding(
            $csv,
            'SJIS-win',
            'UTF-8'
        );

        return $csv;
    }
}
