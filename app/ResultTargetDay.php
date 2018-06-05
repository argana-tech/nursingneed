<?php
/**
 * ResultTargetDay class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResultTargetDay extends Model
{
    protected $fillable = [
        'user_id', 'result_id', 'date', 'c_master_days', 'count_days', 'status', 'remark', 'h_ward', 'h_name', 'ef_ward', 'ef_name', 'content_type', 'is_syutyu'
    ];

    public function result()
    {
        return $this->belongsTo('App\Result');
    }

    public static function getCreatedWards()
    {
        $wards = [];

        $resultTargetDays = self::query()->select('h_ward')->where('h_ward', '!=', '')->groupBy('h_ward')->orderBy('h_ward')->get();
        if ($resultTargetDays->count()) {
            foreach($resultTargetDays as $resultTargetDay) {
                $wards[] = ltrim_zero($resultTargetDay->h_ward);
            }
        }

        $resultTargetDays = self::query()->select('ef_ward')->where('ef_ward', '!=', '')->groupBy('ef_ward')->orderBy('ef_ward')->get();
        if ($resultTargetDays->count()) {
            foreach($resultTargetDays as $resultTargetDay) {
                if (!in_array($resultTargetDay->ef_ward, $wards)) {
                    $wards[] = ltrim_zero($resultTargetDay->ef_ward);
                }
            }
        }

        sort($wards);

        return $wards;
    }
}
