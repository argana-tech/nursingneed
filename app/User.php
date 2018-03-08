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
        'email', 'password', 'name',
    ];

    protected $hidden = [
        'is_dpc_loading', 'dpc_import_status',
    ];

    public function isDpcLoading()
    {
        return $this->is_dpc_loading;
    }

    public function strageKey()
    {
        return sprintf("%s_%d_%s",
            'argana',
            $this->id,
            $this->dpc_imported_at ? Carbon::parse($this->dpc_imported_at)->format('YmdHis') : ""
        );
    }

    public function results() {
        return $this->hasMany('App\Result')
            ->orderBy('id', 'asc');
    }

    public function resultTargetDays() {
        return $this->hasMany('App\ResultTargetDay');
    }

    public function resultInIntensiveWardDays() {
        return $this->hasMany('App\ResultInIntensiveWardDay');
    }

    public function resultTargetOperationData() {
        return $this->hasMany('App\ResultTargetOperationData');
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
