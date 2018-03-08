<?php
/**
 * ResultTargetOperationData class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResultTargetOperationData extends Model
{
    protected $table = 'result_target_operation_data';

    protected $fillable = [
        'user_id', 'result_id', 'date', 'tensu_code', 'densan_code', 'ef_name', 'c_master_name', 'c_master_days', 'start_date', 'end_date', 'remark', 'ward'
    ];

    public function result()
    {
        return $this->belongsTo('App\Result');
    }
}
