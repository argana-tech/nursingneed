<?php
/**
 * ResultInIntensiveWardDay class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResultInIntensiveWardDay extends Model
{
    protected $fillable = [
        'result_id', 'date', 'name'
    ];

    public function result()
    {
        return $this->belongsTo('App\Result');
    }
}
