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

use App\AItem;
use App\CItem;
use App\ObstetricsItem;

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

    public function firstImport()
    {
        DB::beginTransaction();

        //a items
        $this->aItems()->delete();
        $fp = fopen($this->storageFilePath('a_master.txt'), 'r');
        while (($row = fgetcsv($fp, 0, "\t")) !== FALSE) {
            $data = [
                'user_id' => $this->id,
                'payload' => isset($row[0])? trim_space($row[0]) : '',
                'name' => isset($row[1])? trim_space($row[1]) : '',
                'code' => isset($row[2])? trim_space($row[2]) : '',
                'remark' => isset($row[3])? trim_space($row[3]) : '',
            ];

            if (
                empty($data['payload']) || !is_numeric($data['payload'])
                || empty($data['name'])
                || empty($data['code']) || !is_numeric($data['code'])
            ) continue;

            if (!AItem::create($data)) {
                //DB::rollBack();
            }
        }
        fclose($fp);

        //c items
        $this->cItems()->delete();
        $fp = fopen($this->storageFilePath('c_master.txt'), 'r');
        while (($row = fgetcsv($fp, 0, "\t")) !== FALSE) {
            $data = [
                'user_id' => $this->id,
                'payload' => isset($row[0])? trim_space($row[0]) : '',
                'days' => isset($row[1])? trim_space($row[1]) : '',
                'name' => isset($row[2])? trim_space($row[2]) : '',
                'code' => isset($row[3])? trim_space($row[3]) : '',
                'remark' => isset($row[4])? trim_space($row[4]) : '',
            ];

            if (
                empty($data['payload']) || !is_numeric($data['payload'])
                || empty($data['days']) || !is_numeric($data['days'])
                || empty($data['name'])
                || empty($data['code']) || !is_numeric($data['code'])
            ) continue;

            if (!CItem::create($data)) {
                //DB::rollBack();
            }
        }
        fclose($fp);

        //obstetrics items
        $this->obstetricsItems()->delete();
        $fp = fopen($this->storageFilePath('obstetrics_master.txt'), 'r');
        while (($row = fgetcsv($fp, 0, "\t")) !== FALSE) {
            $data = [
                'user_id' => $this->id,
                'name' => isset($row[0])? trim_space($row[0]) : '',
                'code' => isset($row[1])? trim_space($row[1]) : '',
                'kcode' => isset($row[2])? trim_space($row[2]) : '',
                'remark' => isset($row[3])? trim_space($row[3]) : '',
            ];

            if (
                empty($data['name'])
                || empty($data['code']) || !is_numeric($data['code'])
                || empty($data['kcode'])
            ) continue;

            if (!ObstetricsItem::create($data)) {
                //DB::rollBack();
            }
        }
        fclose($fp);

        DB::commit();
    }

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

    /*
     * EFファイルの入力件数
     * 
     * @param string $ward 病棟
     * @return integer count
     */
    public function resultTargetDayEFCount($ward, $date = null) {
        return $this->resultTargetDayByWard($ward, $date)
            ->where(function($query){
                $query->orWhere('status', 'checked')
                ->orWhere('status', 'not checked');
            })
            ->count();
    }

    /*
     * Hファイルの入力件数
     * 
     * @param string $ward 病棟
     * @return integer count
     */
    public function resultTargetDayHCount($ward, $date = null) {
        return $this->resultTargetDayByWard($ward, $date)
            ->where(function($query){
                $query->orWhere('status', 'checked')
                ->orWhere('status', 'h_only');
            })
            ->count();
    }

    /*
     * 両方にあるデータの件数
     * 
     * @param string $ward 病棟
     * @return integer count
     */
    public function resultTargetDayMatchCount($ward, $date = null) {
        return $this->resultTargetDayByWard($ward, $date)
            ->where('status', 'checked')
            ->count();
    }

    /*
     * ResultTargetDayを病棟で絞込み
     * 
     * @param string $ward 病棟
     * @return
     */
    public function resultTargetDayByWard($ward, $date = null) {
        $targetDays = $this->hasMany('App\ResultTargetDay')
            ->where(function($query) use ($ward){
                $query->orWhere('h_ward', $ward)
                ->orWhere('ef_ward', $ward);
            });

        if ($date) {
            $targetDays = $targetDays->where('date', $date);
        }

        return $targetDays;
    }

    public function systemSetting() {
        return $this->hasOne('App\SystemSetting');
    }

    public function aItems() {
        return $this->hasMany('App\AItem')
            ->orderBy('id', 'asc');
    }

    public function cItems() {
        return $this->hasMany('App\CItem')
            ->orderBy('id', 'asc');
    }

    public function obstetricsItems() {
        return $this->hasMany('App\ObstetricsItem')
            ->orderBy('id', 'asc');
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

    private function storageFileDir()
    {
        return storage_path(config('my.master.default_file_path'));
    }

    private function storageFilePath($filename)
    {
        return $this->storageFileDir() . '/' . $filename;
    }

}
