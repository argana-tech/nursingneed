<?php
/**
 * ResultReferenceOperationData class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResultReferenceOperationData extends Model
{
    protected $table = 'result_reference_operation_data';

    protected $fillable = [
        'result_id', 'date', 'tensu_code', 'densan_code', 'ef_name', 'start_date', 'end_date', 'ward'
    ];

    public function result()
    {
        return $this->belongsTo('App\Result');
    }
}
