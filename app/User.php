<?php
/**
 * User class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class User extends Authenticatable
{
    use SoftDeletes;

    public static $dpc_status_successfully = 'OK';
    public static $dpc_status_failed = 'NG';

    protected $fillable = [
        'email', 'password',
    ];

    protected $hidden = [
        'is_dpc_loading', 'dpc_import_status',
    ];

    public function isDpcLoading()
    {
        return $this->is_dpc_loading;
    }

}
