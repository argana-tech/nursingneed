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
        'result_id', 'date', 'c_master_days', 'count_days', 'status', 'remark', 'h_ward', 'ef_ward', 'content_type', 'is_syutyu'
    ];

    public static function getMinDate() {
        return self::min('date');
    }

    public static function getMaxDate() {
        return self::max('date');
    }

    public function result()
    {
        return $this->belongsTo('App\Result');
    }
}
