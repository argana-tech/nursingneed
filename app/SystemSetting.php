<?php
/**
 * SystemSetting class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SystemSetting extends Model
{
    public static $id = 1;

    protected $fillable = [
        'intensive_ward', 'obstetrics_ward', 'child_operation_name',
    ];
}
