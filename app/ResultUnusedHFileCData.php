<?php
/**
 * ResultUnusedHFileCData class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ResultUnusedHFileCData extends Model
{
    protected $table = 'result_unused_h_file_c_data';

    protected $fillable = [
        'result_id', 'payload_check', 'target_days', 'date', 'ward_code', 'ward_name', 'payload1', 'payload2', 'payload3', 'payload4', 'payload5', 'payload6', 'payload7', 'remark'
    ];

    public function result()
    {
        return $this->belongsTo('App\Result');
    }
}
