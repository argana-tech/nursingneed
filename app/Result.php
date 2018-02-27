<?php
/**
 * Result class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Result extends Model
{
    protected $fillable = [
        'identification_id', 'target_days', 'unchecked_days', 'is_obstetrics', 'is_child', 'remark'
    ];

    public function resultDays($month, $firstDay, $endDay) {
        // 対象期間
        $firstDate = Carbon::parse($month.'-'.$firstDay);
        $endDate = Carbon::parse($month.'-'.$endDay);

        $targetDays = [];
        $targetDate = Carbon::parse($month.'-'.$firstDay);

        while($targetDate->format('Y-m-d') <= $endDate->format('Y-m-d')) {
            $targetDays[$targetDate->format('d')] = [];
            $targetDate->addDay();
        }

        return $targetDays;
    }

    public function resultADays($month, $firstDay, $endDay) {
        // 対象期間
        $firstDate = Carbon::parse($month.'-'.$firstDay);
        $endDate = Carbon::parse($month.'-'.$endDay);

        // 対象データ
        $resultTargetDays = $this->resultTargetADays
          ->where('date', ">=", $firstDate->format('Y-m-d'))
          ->where('date', "<=", $endDate->format('Y-m-d'))
          ;

        // 取得
        $targetDays = [];
        $targetDate = Carbon::parse($month.'-'.$firstDay);

        foreach($resultTargetDays as $resultTargetDay) {
            // 対象日になるまで [] を格納
            while($targetDate->format('Y-m-d') < $resultTargetDay->date) {
                $targetDays[$targetDate->format('d')] = [];
                $targetDate = $targetDate->addDay();
            }

            if ($resultTargetDay->date > $endDate->format('Y-m-d'))
                break;

            $targetDays[$targetDate->format('d')] = $resultTargetDay;


            $targetDate = $targetDate->addDay();
        }

        if ($targetDate->format('Y-m-d') < $endDate->format('Y-m-d')) {
            // 対象日になるまで [] を格納
            while($targetDate->format('Y-m-d') <= $endDate->format('Y-m-d')) {
                $targetDays[$targetDate->format('d')] = [];
                $targetDate = $targetDate->addDay();
            }

            $targetDate = $targetDate->addDay();
        }

        return $targetDays;
    }

    public function resultCDays($month, $firstDay, $endDay) {
        // 対象期間
        $firstDate = Carbon::parse($month.'-'.$firstDay);
        $endDate = Carbon::parse($month.'-'.$endDay);

        // 対象データ
        $resultTargetDays = $this->resultTargetCDays
          ->where('date', ">=", $firstDate->format('Y-m-d'))
          ->where('date', "<=", $endDate->format('Y-m-d'))
          ;

        // 取得
        $targetDays = [];
        $targetDate = Carbon::parse($month.'-'.$firstDay);

        foreach($resultTargetDays as $resultTargetDay) {
            // 対象日になるまで [] を格納
            while($targetDate->format('Y-m-d') < $resultTargetDay->date) {
                $targetDays[$targetDate->format('d')] = [];
                $targetDate = $targetDate->addDay();
            }

            if ($resultTargetDay->date > $endDate->format('Y-m-d'))
                break;

            $targetDays[$targetDate->format('d')] = $resultTargetDay;


            $targetDate = $targetDate->addDay();
        }

        if ($targetDate->format('Y-m-d') < $endDate->format('Y-m-d')) {
            // 対象日になるまで [] を格納
            while($targetDate->format('Y-m-d') <= $endDate->format('Y-m-d')) {
                $targetDays[$targetDate->format('d')] = [];
                $targetDate = $targetDate->addDay();
            }

            $targetDate = $targetDate->addDay();
        }

        return $targetDays;
    }

    public function inWardDay() {
        $inWardDay = null;
        $inAWardDay = ($inAWardDay = $this->resultTargetADays()->min('date'))? Carbon::parse($inAWardDay) : null;
        $inCWardDay = ($inCWardDay = $this->resultTargetCDays()->min('date'))? Carbon::parse($inCWardDay) : null;

        switch (true) {
            case !$inAWardDay && !$inCWardDay:
                $inWardDay = ($inAWardDay < $inCWardDay)? $inAWardDay : $inCWardDay;
                break;
            case $inAWardDay:
                $inWardDay = $inAWardDay;
                break;
            case $inCWardDay:
                $inWardDay = $inCWardDay;
                break;
        }

        return $inWardDay;
    }

    public function outWardDay() {
        $outWardDay = null;
        $outAWardDay = ($outAWardDay = $this->resultTargetADays()->max('date'))? \Carbon\Carbon::parse($outAWardDay) : null;
        $outCWardDay = ($outCWardDay = $this->resultTargetCDays()->max('date'))? \Carbon\Carbon::parse($outCWardDay) : null;

        switch (true) {
            case !$outAWardDay && !$outCWardDay:
                $outWardDay = ($outAWardDay < $outCWardDay)? $outCWardDay : $outAWardDay;
                break;
            case $outAWardDay:
                $outWardDay = $outAWardDay;
                break;
            case $outCWardDay:
                $outWardDay = $outCWardDay;
                break;
        }

        return $outWardDay;
    }

    public function resultTargetADays() {
        return $this->hasMany('App\ResultTargetDay')
            ->where('content_type', '=', 'A')
            ->orderBy('date', 'asc');
    }

    public function resultTargetCDays() {
        return $this->hasMany('App\ResultTargetDay')
            ->where('content_type', '=', 'C')
            ->orderBy('date', 'asc');
    }

    public function resultTargetDays() {
        return $this->hasMany('App\ResultTargetDay')->orderBy('date', 'asc');
    }

    public function resultTargetOperationData() {
        return $this->hasMany('App\ResultTargetOperationData');
    }

    public function resultInIntensiveWardDays() {
        return $this->hasMany('App\ResultInIntensiveWardDay');
    }

    public function resultReferenceOperationData() {
        return $this->hasMany('App\ResultReferenceOperationData');
    }

    public function resultUsedHFileCData() {
        return $this->hasMany('App\ResultUsedHFileCData');
    }

    public function resultUnusedHFileCData() {
        return $this->hasMany('App\ResultUnusedHFileCData');
    }
}
