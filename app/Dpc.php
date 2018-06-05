<?php
/**
 * Dpc class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\User;
use App\AItem;
use App\CItem;
use App\SystemSetting;
use App\Result;
use App\ResultTargetDay;
use App\ResultInIntensiveWardDay;
use App\ResultTargetOperationData;
use App\ResultReferenceOperationData;
use App\ResultUsedHFileCData;
use App\ResultUnusedHFileCData;

class Dpc extends Model
{
    protected $fillable = [
    ];

    protected $hidden = [
        'file',
    ];

    /**
     * EFファイルアップロード
     *
     * @param object $file
     *
     * @return bool
     */
    public function efUpload($file)
    {
        $user = auth()->user();
        return $this->upload($file, $user->id . "_" . config('my.dpc.ef_filename'));
    }

    /**
     * Hファイルアップロード
     *
     * @param object $file
     *
     * @return bool
     */
    public function hUpload($file)
    {
        $user = auth()->user();
        return $this->upload($file, $user->id . "_" . config('my.dpc.h_filename'));
    }

    /**
     * ファイルアップロード
     *
     * @param object $file
     * @param string $fileName
     *
     * @return bool
     */
    public function upload($file, $fileName)
    {
        $this->file = $fileName;
        $file->move($this->storageFileDir(), $this->file);
        return $this->storageFilePath();
    }

    /**
     * EFファイルとHファイルを取り込み、チェック漏れを検出
     * 結果をDBに保存
     *
     * @param int $userId
     * @param string $efFilePath
     * @param string $hFilePath
     * @param int $code
     * @param date $endDate
     *
     * @return bool
     */
    public static function import($userId, $efFilePath, $hFilePath, $code, $endDate)
    {
        try {
            if (! $results = self::getResultData($userId, $efFilePath, $hFilePath, $code, $endDate)) {
                return false;
            }
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            return false;
        }

        // DB
        $dataCount = 0;
        $notCheckedDaysCount = 0;
        $tooManyFDileDaysCount = 0;
        $sankaCount = 0;
        $kodomoCount = 0;

        $user = User::findOrFail($userId);

        DB::beginTransaction();

        try {
            $user->results()->delete();
            $user->resultTargetDays()->delete();
            $user->resultInIntensiveWardDays()->delete();
            $user->resultTargetOperationData()->delete();
            $user->resultReferenceOperationData()->delete();
            $user->resultUsedHFileCData()->delete();
            $user->resultUnusedHFileCData()->delete();

            foreach($results as $sikibetuId => $userData) {
                // C項目・A項目どちらにも該当しない場合はスキップ
                /*if ( ! count($userData['syujutsu']) && ! count($userData['syochi'])) {
                    continue;
                }*/

                $notCheckedDays = 0;
                if (isset($userData['a_date_check'])) {
                    foreach($userData['a_date_check'] as $value) {
                        if ($value['status'] == 'not checked' || $value['status'] == 'h_only') {
                            $notCheckedDays += 1;
                        }
                    }
                }
                if (isset($userData['c_date_check'])) {
                    foreach($userData['c_date_check'] as $value) {
                        if ($value['status'] == 'not checked' || $value['status'] == 'h_only') {
                            $notCheckedDays += 1;
                        }
                    }
                }
                $userData['not_checked_days'] = $notCheckedDays;
                if ($userData['not_checked_days'] == 0 && !count($userData['not_used_h_files'])) continue;

                if ($userData['kodomo'] == true) {
                    $kodomoCount += 1;
                }

                if ($userData['sanka'] == true) {
                    $sankaCount += 1;
                }

                $dataCount += 1;

                if ($userData['not_checked_days'] != 0) {
                    $notCheckedDaysCount += $userData['not_checked_days'];
                }
                if (count($userData['not_used_h_files']) > 0) {
                    $tooManyFDileDaysCount += count($userData['not_used_h_files']);
                }

                $result = Result::create([
                    'user_id' => $userId,
                    'identification_id' => self::encryptionByCodo($sikibetuId, $code),
                    'target_days' => $userData['target_days'],
                    'unchecked_days' => $userData['not_checked_days'],
                    'is_obstetrics' => $userData['sanka'],
                    'is_child' => $userData['kodomo'],
                    'remark' => '',
                ]);

                // a items
                if (isset($userData['a_date_check'])) {
                    foreach($userData['a_date_check'] as $k => $v) {
                        ResultTargetDay::create([
                            'user_id' => $userId,
                            'result_id' => $result->id,
                            'date' => $k,
                            'c_master_days' => null,
                            'status' => @$v['status'],
                            'remark' => @$v['remark'],
                            'ef_ward' => ltrim_zero(@$v['ef_byoutou']),
                            'ef_name' => @$v['ef_name'],
                            'h_ward' => ltrim_zero(@$v['h_byoutou']),
                            'h_name' => @$v['h_name'],
                            'content_type' => 'A',
                            'is_syutyu' => (count(@$userData['syutyu_days'][$k]))? 1 : 0,
                        ]);
                    }
                }

                // c items
                if (isset($userData['c_date_check'])) {
                    foreach($userData['c_date_check'] as $k => $v) {
                        ResultTargetDay::create([
                            'user_id' => $userId,
                            'result_id' => $result->id,
                            'date' => $k,
                            'c_master_days' => isset($v['master_days'])? $v['master_days'] : 0,
                            'count_days' => isset($v['count_days'])? $v['count_days'] : 0,
                            'status' => @$v['status'],
                            'remark' => @$v['remark'],
                            'ef_ward' => ltrim_zero(@$v['ef_byoutou']),
                            'ef_name' => @$v['ef_name'],
                            'h_name' => @$v['h_name'],
                            'h_ward' => ltrim_zero(@$v['h_byoutou']),
                            'content_type' => 'C',
                            'is_syutyu' => (count(@$userData['syutyu_days'][$k]))? 1 : 0,
                        ]);
                    }
                }

                foreach($userData['syutyu_days'] as $k => $v) {
                    ResultInIntensiveWardDay::create([
                        'user_id' => $userId,
                        'result_id' => $result->id,
                        'date' => $k,
                        'name' => $v,
                    ]);
                }

                foreach($userData['syujutsu'] as $a) {
                    ResultTargetOperationData::create([
                        'user_id' => $userId,
                        'result_id' => $result->id,
                        'date' => $a['do_date'],
                        'tensu_code' => @$a['tensu_code'],
                        'densan_code' => @$a['densan_code'],
                        'ef_name' => @$a['ef_name'],
                        'c_master_name' => @$a['master_name'],
                        'c_master_days' => @$a['master_days'],
                        'start_date' => @$a['start_date'],
                        'end_date' => @$a['end_date'],
                        'remark' => '',
                        'ward' => @$a['byoutou'],
                    ]);
                }

                foreach($userData['sankou'] as $a) {
                    ResultReferenceOperationData::create([
                        'user_id' => $userId,
                        'result_id' => $result->id,
                        'date' => $a['do_date'],
                        'tensu_code' => @$a['tensu_code'],
                        'densan_code' => @$a['densan_code'],
                        'ef_name' => @$a['ef_name'],
                        'start_date' => @$a['start_date'],
                        'end_date' => @$a['end_date'],
                        //'ward' => $a['byoutou'],
                    ]);
                }

                foreach($userData['used_h_files'] as $a) {
                    ResultUsedHFileCData::create([
                        'user_id' => $userId,
                        'result_id' => $result->id,
                        'payload_check' => $a['payload_check'],
                        'target_days' => $a['target_days'],
                        'date' => $a['do_date'],
                        'ward_code' => @$a['byoutou_code'],
                        'ward_name' => @$a['byouin_name'],
                        'payload1' => @$a['payload1'],
                        'payload2' => @$a['payload2'],
                        'payload3' => @$a['payload3'],
                        'payload4' => @$a['payload4'],
                        'payload5' => @$a['payload5'],
                        'payload6' => @$a['payload6'],
                        'payload7' => @$a['payload7'],
                        'remark' => implode(' ', [
                            $a['remark1'],
                            $a['remark2'],
                            $a['remark3'],
                            $a['remark4'],
                            $a['remark5'],
                            $a['remark6'],
                            $a['remark7'],
                        ]),
                    ]);
                }

                foreach($userData['not_used_h_files'] as $a) {
                    ResultUnusedHFileCData::create([
                        'user_id' => $userId,
                        'result_id' => $result->id,
                        'payload_check' => $a['payload_check'],
                        'target_days' => $a['target_days'],
                        'date' => $a['do_date'],
                        'ward_code' => @$a['byoutou_code'],
                        'ward_name' => @$a['byouin_name'],
                        'payload1' => @$a['payload1'],
                        'payload2' => @$a['payload2'],
                        'payload3' => @$a['payload3'],
                        'payload4' => @$a['payload4'],
                        'payload5' => @$a['payload5'],
                        'payload6' => @$a['payload6'],
                        'payload7' => @$a['payload7'],
                        'remark' => implode(' ', [
                            $a['remark1'],
                            $a['remark2'],
                            $a['remark3'],
                            $a['remark4'],
                            $a['remark5'],
                            $a['remark6'],
                            $a['remark7'],
                        ]),
                    ]);
                }
            }

            if (!$dataCount) {
                Log::error("データが見つかりませんでした");
                DB::rollBack();
                return false;
            }
        } catch(\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return false;
        }

        DB::commit();

        echo 'data_count:' . $dataCount . "\n";
        echo 'not_checked_days_count:' . $notCheckedDaysCount . "\n";
        echo 'too_many_h_file_days_count:' . $tooManyFDileDaysCount . "\n";
        echo 'sanka_count:' . $sankaCount . "\n";
        echo 'kodomo_count:' . $kodomoCount . "\n";

        return true;
    }

    /**
     * EFファイルとHファイルを取り込み、チェック漏れを検出
     *
     * @param string $efFilePath
     * @param string $hFilePath
     * @param int $code
     * @param date $endDate
     *
     * @return bool
     */
    private static function getResultData($userId, $efFilePath, $hFilePath, $code, $endDate)
    {
        $maxDate = (empty($endDate))? Carbon::today() : Carbon::parse($endDate);

        $user = User::findOrFail($userId);

        // get master
        $obstetricsMaster = [];
        foreach($user->obstetricsItems as $obstetricsItem) {
            $obstetricsMaster[$obstetricsItem->code] = $obstetricsItem;
        }

        $aMaster = [];
        foreach ($user->aItems as $aItem) {
            $aMaster[$aItem->code] = $aItem;
        }

        $cMaster = [];
        foreach($user->cItems as $cItem) {
            $cMaster[$cItem->code] = $cItem;
        }

        // get system setting
        $system = SystemSetting::firstOrNew(['user_id' => $user->id]);

        $settingChildNames = ($system->child_operation_name)? explode("\n", $system->child_operation_name) : [];
        $settingIntensiveWards = ($system->intensive_ward)? explode("\n", $system->intensive_ward) : [];
        $settingObstetricsWards = ($system->obstetrics_ward)? explode("\n", $system->obstetrics_ward) : [];

        $settingChildNames = array_map(function($n) { return trim(str_replace(array("\r","\n"), '', $n)); }, $settingChildNames);
        $settingIntensiveWards = array_map(function($n) { return trim(str_replace(array("\r","\n"), '', $n)); }, $settingIntensiveWards);
        $settingObstetricsWards = array_map(function($n) { return trim(str_replace(array("\r","\n"), '', $n)); }, $settingObstetricsWards);

        $efFile = [];
        $byoutouData = [];

        // ef file
        $countEf = 0;
        $encode = config('my.dpc.import.encode');
        $fp = fopen($efFilePath, 'r');
        while (($row = fgetcsv($fp, 0, config('my.dpc.import.delimiter'))) !== FALSE) {
            if (empty($row)) continue;

            $countEf += 1;

            if ($encode != 'UTF-8')
                mb_convert_variables('UTF-8', $encode, $row);

            $efWorkFile = [
                'byouin_code' => self::trimId(@$row[0]),
                'sikibetu_id' => self::trimId(@$row[1]),
                'out_date' => self::trimId(@$row[2]),
                'in_date' => self::trimId(@$row[3]),
                'data_type' => @$row[4],
                'order_num' => @$row[5],
                'meisai_bangou' => @$row[6],
                'tensuu_master' => @$row[7],
                'densan_code' => @$row[8],
                'kaisyaku_bangou' => @$row[9],
                'name' => @$row[10],
                'volume' => @$row[11],
                'unit' => @$row[12],
                'price' => @$row[13],
                'enten_type' => @$row[14],
                'dekidaka' => @$row[15],
                'meisaikubun' => @$row[16],
                'tensuu' => @$row[17],
                'yakuzai' => @$row[18],
                'zairyou' => @$row[19],
                'kaisuu' => @$row[20],
                'bango' => @$row[21],
                'sikibetu_code' => @$row[22],
                'do_date' => @$row[23],
                'reseputo_kubn' => @$row[24],
                'sinryouka_kubun' => @$row[25],
                'isi_code' => @$row[26],
                'byoutou_code' => @$row[27],
                'byoutou_kubun' => @$row[28],
                'nyougai_kubun' => @$row[29],
                'sisetu_type' => @$row[30],
            ];

            if (
                $countEf == 1
                && !is_numeric($efWorkFile['byouin_code'])
                && !is_numeric($efWorkFile['sikibetu_id'])
                && !is_numeric($efWorkFile['out_date'])
                && !is_numeric($efWorkFile['in_date'])
            ) continue;

            if (empty($efWorkFile['do_date'])) {
                $doDate = Carbon::today()->format('Y-m-d');
            } else {
                if (!check_is_date($efWorkFile['do_date'])) {
                    Log::error("不正な日付がありました #{$efWorkFile['do_date']}");
                    return false;
                }
                $doDate = Carbon::parse($efWorkFile['do_date'])->format('Y-m-d');
            }

            // byoto
            if (!isset($byoutouData[$efWorkFile['sikibetu_id']]))
                $byoutouData[$efWorkFile['sikibetu_id']] = [];
            $byoutouDataAt = (@$byoutouData[$efWorkFile['sikibetu_id']][$doDate])? $byoutouData[$efWorkFile['sikibetu_id']][$doDate] : [];

            if ($efWorkFile['byoutou_code'] != $byoutouDataAt) {
                $byoutouData[$efWorkFile['sikibetu_id']][$doDate] = $efWorkFile['byoutou_code'];
            }

            // sanka
            $sanka = null;
            if (in_array($efWorkFile['byoutou_code'], $settingObstetricsWards)) {
                $sanka = true;
            } elseif (isset($obstetricsMaster[$efWorkFile['byoutou_code']])) {
                $sanka = true;
            }
            if ($sanka) {
                if (!isset($efFile[$efWorkFile['sikibetu_id']])) {
                    $efFile[$efWorkFile['sikibetu_id']] = [
                        'target_days' => 0,
                        'sanka' => false,
                        'kodomo' => false,
                        'a_date_check' => [],
                        'c_date_check' => [],
                        'syutyu_days' => [],
                        'syujutsu' => [],
                        'syochi' => [],
                        'sankou' => [],
                        'used_h_files' => [],
                        'not_used_h_files' => []
                    ];
                }
                $efFile[$efWorkFile['sikibetu_id']]['sanka'] = true;
            }

            // kodomo
            $isKodomo = false;
            foreach($settingChildNames as $settingChildName) {
                if (strpos($efWorkFile['name'], $settingChildName) !== false) {
                    $isKodomo = true;
                    break;
                }
            }

            if ($isKodomo) {
                if (!isset($efFile[$efWorkFile['sikibetu_id']])) {
                    $efFile[$efWorkFile['sikibetu_id']] = [
                        'target_days' => 0,
                        'sanka' => false,
                        'kodomo' => false,
                        'a_date_check' => [],
                        'c_date_check' => [],
                        'syutyu_days' => [],
                        'syujutsu' => [],
                        'syochi' => [],
                        'sankou' => [],
                        'used_h_files' => [],
                        'not_used_h_files' => []
                    ];
                }
                $efFile[$efWorkFile['sikibetu_id']]['kodomo'] = true;
            }

            // syutyu
            if (in_array($efWorkFile['byoutou_code'], $settingIntensiveWards)) {
                if (!isset($efFile[$efWorkFile['sikibetu_id']])) {
                    $efFile[$efWorkFile['sikibetu_id']] = [
                        'target_days' => 0,
                        'sanka' => false,
                        'kodomo' => false,
                        'a_date_check' => [],
                        'c_date_check' => [],
                        'syutyu_days' => [],
                        'syujutsu' => [],
                        'syochi' => [],
                        'sankou' => [],
                        'used_h_files' => [],
                        'not_used_h_files' => []
                    ];
                }
                $efFile[$efWorkFile['sikibetu_id']]['syutyu_days'][$doDate] = $efWorkFile['byoutou_code'];
            }

            // a_master
            $a_master =  @$aMaster[$efWorkFile['densan_code']];
            if (!empty($a_master)) {
                if (!isset($efFile[$efWorkFile['sikibetu_id']])) {
                    $efFile[$efWorkFile['sikibetu_id']] = [
                        'target_days' => 0,
                        'sanka' => false,
                        'kodomo' => false,
                        'a_date_check' => [],
                        'c_date_check' => [],
                        'syutyu_days' => [],
                        'syujutsu' => [],
                        'syochi' => [],
                        'sankou' => [],
                        'used_h_files' => [],
                        'not_used_h_files' => []
                    ];
                }
                $efFile[$efWorkFile['sikibetu_id']]['syochi'][] = [
                    'id' => @$efWorkFile['id'],
                    'do_date' => $efWorkFile['do_date'],
                    'start' => $efWorkFile['in_date'],
                    'end' => $efWorkFile['out_date'],
                    'code' => $efWorkFile['tensuu_master'],
                    'densan_code' => $efWorkFile['densan_code'],
                    'ef_name' => trim_space($efWorkFile['name']),
                    'master_payload' => $a_master->payload,
                    'master_name' => $a_master->name,
                    'master_days' => $a_master->days,
                    'byoutou' => $efWorkFile['byoutou_code']
                ];
            } else {
                if (strpos($efWorkFile['tensuu_master'], 'J') === 0) {
                    if (!isset($efFile[$efWorkFile['sikibetu_id']])) {
                        $efFile[$efWorkFile['sikibetu_id']] = [
                            'target_days' => 0,
                            'sanka' => false,
                            'kodomo' => false,
                            'a_date_check' => [],
                            'c_date_check' => [],
                            'syutyu_days' => [],
                            'syujutsu' => [],
                            'syochi' => [],
                            'sankou' => [],
                            'used_h_files' => [],
                            'not_used_h_files' => []
                        ];
                    }
                    $efFile[$efWorkFile['sikibetu_id']]['sankou'][] = [
                        'id' => @$efWorkFile['id'],
                        'do_date' => $efWorkFile['do_date'],
                        'start' => $efWorkFile['in_date'],
                        'end' => $efWorkFile['out_date'],
                        'tensu_code' => $efWorkFile['tensuu_master'],
                        'densan_code' => $efWorkFile['densan_code'],
                        'ef_name' => trim_space($efWorkFile['name']),
                        'byoutou' => $efWorkFile['byoutou_code']
                    ];
                }
            }

            // c_master
            $c_master =  @$cMaster[$efWorkFile['densan_code']];
            if (!empty($c_master)) {
                if (!isset($efFile[$efWorkFile['sikibetu_id']])) {
                    $efFile[$efWorkFile['sikibetu_id']] = [
                        'target_days' => 0,
                        'sanka' => false,
                        'kodomo' => false,
                        'a_date_check' => [],
                        'c_date_check' => [],
                        'syutyu_days' => [],
                        'syujutsu' => [],
                        'syochi' => [],
                        'sankou' => [],
                        'used_h_files' => [],
                        'not_used_h_files' => []
                    ];
                }
                $efFile[$efWorkFile['sikibetu_id']]['syujutsu'][] = [
                    'id' => @$efWorkFile['id'],
                    'do_date' => $efWorkFile['do_date'],
                    'start' => $efWorkFile['in_date'],
                    'end' => $efWorkFile['out_date'],
                    'code' => $efWorkFile['tensuu_master'],
                    'densan_code' => $efWorkFile['densan_code'],
                    'ef_name' => trim_space($efWorkFile['name']),
                    'master_payload' => $c_master->payload,
                    'master_name' => $c_master->name,
                    'master_days' => $c_master->days,
                    'byoutou' => $efWorkFile['byoutou_code']
                ];
            } else {
                if (strpos($efWorkFile['tensuu_master'], 'K') === 0) {
                    if (!isset($efFile[$efWorkFile['sikibetu_id']])) {
                        $efFile[$efWorkFile['sikibetu_id']] = [
                            'target_days' => 0,
                            'sanka' => false,
                            'kodomo' => false,
                            'a_date_check' => [],
                            'c_date_check' => [],
                            'syutyu_days' => [],
                            'syujutsu' => [],
                            'syochi' => [],
                            'sankou' => [],
                            'used_h_files' => [],
                            'not_used_h_files' => []
                        ];
                    }
                    $efFile[$efWorkFile['sikibetu_id']]['sankou'][] = [
                        'id' => @$efWorkFile['id'],
                        'do_date' => $efWorkFile['do_date'],
                        'start' => $efWorkFile['in_date'],
                        'end' => $efWorkFile['out_date'],
                        'tensu_code' => $efWorkFile['tensuu_master'],
                        'densan_code' => $efWorkFile['densan_code'],
                        'ef_name' => trim_space($efWorkFile['name']),
                        'byoutou' => $efWorkFile['byoutou_code']
                    ];
                }
            }
        }
        fclose($fp);

        // user_data
        foreach($efFile as $sikibetuId => $userData) {
            // a items
            foreach($userData['syochi'] as $value) {
                if (!check_is_date($value['do_date'])) {
                    Log::error("不正な日付がありました #{$value['do_date']}");
                    return false;
                }
                $doDate = Carbon::parse($value['do_date'])->format('Y-m-d');

                $targetDate = new Carbon($doDate);

                if ($targetDate > $maxDate) continue;

                $byoutou = @$byoutouData[$sikibetuId][$targetDate->format('Y-m-d')];

                if (!empty($userData['syutyu_days'][$targetDate->format('Y-m-d')])) {
                    $userData['a_date_check'][$targetDate->format('Y-m-d')] = [
                        'master_payload' => $value['master_payload'],
                        'master_days' => 1, // 処置は「1日」
                        //'status' => 'syutyu',
                        'status' => 'not checked', // A項目は集中も対象
                        'ef_byoutou' => $byoutou,
                        'ef_name' => $value['ef_name'],
                        'h_name' => ''
                    ];
                } else {
                    $userData['a_date_check'][$targetDate->format('Y-m-d')] = [
                        'master_payload' => $value['master_payload'],
                        'master_days' => 1, // 処置は「1日」
                        'status' => 'not checked',
                        'ef_byoutou' => $byoutou,
                        'ef_name' => $value['ef_name'],
                        'h_name' => ''
                    ];
                }
            }

            // c items
            foreach($userData['syujutsu'] as $value) {
                if (!check_is_date($value['do_date'])) {
                    Log::error("不正な日付がありました #{$value['do_date']}");
                    return false;
                }
                $doDate = Carbon::parse($value['do_date'])->format('Y-m-d');

                $value['do_date'] = $doDate;

                // end
                if ($value['end'] == '00000000' || $value['end'] == '0') {
                    $endDate = Carbon::today()->format('Y-m-d');
                } else {
                    if (!check_is_date($value['end'])) {
                        Log::error("不正な日付がありました #{$value['end']}");
                        return false;
                    }
                    $endDate = Carbon::parse($value['end'])->format('Y-m-d');
                }
                $value['end'] = $endDate;

                $doDateTime = new Carbon($doDate);
                $endDateTime = new Carbon($endDate);
                $endDateTime = $endDateTime->addDay();
                $diff = $endDateTime->diff($doDateTime);

                $value['diff'] = $diff->days;

                $targetDate = $doDateTime;
                for($index=0; $index < $value['master_days'];$index++) {

                    if ($index > 0)
                        $targetDate->addDay();
                    if ($targetDate > $maxDate) continue;

                    $currentMasterDays = 0;
                    if (@$userData['c_date_check'][$targetDate->format('Y-m-d')] != null) {
                        $currentMasterDays = $userData['c_date_check'][$targetDate->format('Y-m-d')]['master_days'];
                    }

                    $byoutou = @$byoutouData[$sikibetuId][$targetDate->format('Y-m-d')];

                    if (!empty($userData['syutyu_days'][$targetDate->format('Y-m-d')])) {
                        $userData['c_date_check'][$targetDate->format('Y-m-d')] = [
                            'master_payload' => $value['master_payload'],
                            'master_days' => max([$value['master_days'], $currentMasterDays]),
                            'count_days' => $index + 1,
                            'status' => 'syutyu',
                            'ef_byoutou' => $byoutou,
                            'ef_name' => $value['ef_name'],
                            'h_name' => ''
                        ];
                    } else {
                        $userData['c_date_check'][$targetDate->format('Y-m-d')] = [
                            'master_payload' => $value['master_payload'],
                            'master_days' => max([$value['master_days'], $currentMasterDays]),
                            'count_days' => $index + 1,
                            'status' => 'not checked',
                            'ef_byoutou' => $byoutou,
                            'ef_name' => $value['ef_name'],
                            'h_name' => ''
                        ];
                    }
                }
            }

            $efFile[$sikibetuId] = $userData;
            $efFile[$sikibetuId]['target_days'] = count($userData['a_date_check']);
            $efFile[$sikibetuId]['not_checked_days'] = 'h file was not found';

        }

        // h file
        $fp = fopen($hFilePath, 'r');
        while (($row = fgetcsv($fp, 0, config('my.dpc.import.delimiter'))) !== FALSE) {
            if (empty($row)) continue;

            if ($encode != 'UTF-8')
                mb_convert_variables('UTF-8', $encode, $row);

            $hFile = [
                'payload_check' => null,
                'target_days' => null,
                'byouin_name' => @$row[0],
                'byoutou_code' => @$row[1],
                'sikibetu_id' => self::trimId(@$row[2]),
                'out_date' => @$row[3],
                'in_date' => @$row[4],
                'do_date' => @$row[5],
                'code' => @$row[6],
                'version' => @$row[7],
                'order_num' => @$row[8],
                'payload1' => @$row[9], # 7日
                'payload2' => @$row[10], # 7日
                'payload3' => @$row[11], # 5日
                'payload4' => @$row[12], # 5日
                'payload5' => @$row[13], # 3日
                'payload6' => @$row[14], # 2日
                'payload7' => @$row[15], # 2日
                'payload8' => @$row[16], # 2日
                'remark1' => (isset($row[29]))? $row[29] : '', # 備考
                'remark2' => (isset($row[30]))? $row[30] : '', # 備考
                'remark3' => (isset($row[31]))? $row[31] : '', # 備考
                'remark4' => (isset($row[32]))? $row[32] : '', # 備考
                'remark5' => (isset($row[33]))? $row[33] : '', # 備考
                'remark6' => (isset($row[34]))? $row[34] : '', # 備考
                'remark7' => (isset($row[35]))? $row[35] : '', # 備考
            ];

            // a items
            if ($hFile['code'] == 'ASS0010') {
                if (					// 対象日数が0の場合はスキップ
                    $hFile['payload1'] == '0'
                    && $hFile['payload2'] == '0'
                    && $hFile['payload3'] == '0'
                    && $hFile['payload4'] == '0'
                    && $hFile['payload5'] == '0'
                    && $hFile['payload6'] == '0'
                    && ($hFile['payload7'] == '0' || $hFile['payload7'] == '000')
                    && ($hFile['payload8'] == '0' || $hFile['payload8'] == '000')
                ) continue;

                if (!check_is_date($hFile['do_date'])) {
                    Log::error("不正な日付がありました #{$hFile['do_date']}");
                    return false;
                }
                $doDate = Carbon::parse($hFile['do_date'])->format('Y-m-d');

                if ($doDate > $maxDate->format('Y-m-d')) continue;	// 最終日以降の日付の場合はスキップ

                $hName = '';
                $payloadNames = config('my.h_file.payload_names.a');
                if ($hFile['payload1'] != '0') {
                    $hName = $payloadNames[1];
                }
                if ($hFile['payload2'] != '0') {
                    $hName = $payloadNames[2];
                    $hName = '呼吸ケア';
                }
                if ($hFile['payload3'] != '0') {
                    $hName = $payloadNames[3];
                }
                if ($hFile['payload4'] != '0') {
                    $hName = $payloadNames[4];
                }
                if ($hFile['payload5'] != '0') {
                    $hName = $payloadNames[5];
                }
                if ($hFile['payload6'] != '0') {
                    $hName = $payloadNames[6];
                }
                if ($hFile['payload7'] != '0' && $hFile['payload7'] != '000') {
                    $hName = $payloadNames[7];
                }
                if ($hFile['payload8'] != '0' && $hFile['payload8'] != '000') {
                    $hName = $payloadNames[8];
                }

                $hFile['target_days'] = 1;

                $userData = @$efFile[$hFile['sikibetu_id']];

                if (!$userData) {
                    // efファイルに存在しない場合
                    $efFile[$hFile['sikibetu_id']] = [
                        'target_days' => 0,
                        'sanka' => false,
                        'kodomo' => false,
                        'syutyu_days' => [],
                        'syujutsu' => [],
                        'syochi' => [],
                        'sankou' => [],
                        'used_h_files' => [],
                        'not_used_h_files' => [],
                        'c_date_check' => [],
                        'a_date_check' => []
                    ];
                    $userData = $efFile[$hFile['sikibetu_id']];
                }

                // check payload
                $payload = @$userData['a_date_check'][$doDate]['master_payload'];
                $payload = @$hFile['payload' . $payload];

                $efFile[$hFile['sikibetu_id']]['content_type'] = 'A';

                if (
                    @$userData['a_date_check'][$doDate]
                    && $userData['a_date_check'][$doDate]['status'] == 'not checked'
                    && $payload != '0' && $payload != '000'
                ) {
                    $userData['a_date_check'][$doDate]['status'] = 'checked';
                    $userData['a_date_check'][$doDate]['h_byoutou'] = $hFile['byoutou_code'];
                    $userData['a_date_check'][$doDate]['remark'] = implode(' ', [
                        $hFile['remark1'],
                        $hFile['remark2'],
                        $hFile['remark3'],
                        $hFile['remark4'],
                        $hFile['remark5'],
                        $hFile['remark6'],
                        $hFile['remark7'],
                    ]);
                    if ($hFile['target_days'] == $userData['a_date_check'][$doDate]['master_days']) {
                       $hFile['payload_check'] = true;
                    } else {
                       $hFile['payload_check'] = false;
                    }

                    $efFile[$hFile['sikibetu_id']] = $userData;
                    $efFile[$hFile['sikibetu_id']]['used_h_files'][] = $hFile;

                } elseif (@$userData['a_date_check'][$doDate] && $userData['a_date_check'][$doDate]['status'] == 'syutyu') {

                    if ($hFile['target_days'] == @$userData['a_date_check'][$doDate]['master_days']) {
                       $hFile['payload_check'] = true;
                    } else {
                       $hFile['payload_check'] = false;
                    }
                    $efFile[$hFile['sikibetu_id']]['used_h_files'][] = $hFile;

                } else {
                    // efファイルに存在しない場合
                    if (!isset($userData['a_date_check'][$doDate])) {
                        $userData['a_date_check'][$doDate] = [
                            'h_name' => $hName,
                            'status' => 'h_only',
                            'h_byoutou' => $hFile['byoutou_code'],
                            'remark' => implode(' ', [
                                $hFile['remark1'],
                                $hFile['remark2'],
                                $hFile['remark3'],
                                $hFile['remark4'],
                                $hFile['remark5'],
                                $hFile['remark6'],
                                $hFile['remark7'],
                            ])
                        ];
                        $efFile[$hFile['sikibetu_id']] = $userData;
                    }

                    $efFile[$hFile['sikibetu_id']]['not_used_h_files'][] = $hFile;
                }
            }

            // c items
            if ($hFile['code'] == 'ASS0030') {
                if (					// 対象日数が0の場合はスキップ
                    $hFile['payload1'] == '0'
                    && $hFile['payload2'] == '0'
                    && $hFile['payload3'] == '0'
                    && $hFile['payload4'] == '0'
                    && $hFile['payload5'] == '0'
                    && $hFile['payload6'] == '0'
                    && ($hFile['payload7'] == '0' || $hFile['payload7'] == '000')
                ) continue;

                if (!check_is_date($hFile['do_date'])) {
                    Log::error("不正な日付がありました #{$hFile['do_date']}");
                    return false;
                }
                $doDate = Carbon::parse($hFile['do_date'])->format('Y-m-d');

                if ($doDate > $maxDate->format('Y-m-d')) continue;	// 最終日以降の日付の場合はスキップ

                $hName = '';
                $payloadNames = config('my.h_file.payload_names.c');
                if ($hFile['payload1'] != '0') {
                    $hFile['target_days'] = 7;
                    $hName = $payloadNames[1];
                }
                if ($hFile['payload2'] != '0') {
                    $hFile['target_days'] = 7;
                    $hName = $payloadNames[2];
                }
                if ($hFile['payload3'] != '0') {
                    $hFile['target_days'] = 5;
                    $hName = $payloadNames[3];
                }
                if ($hFile['payload4'] != '0') {
                    $hFile['target_days'] = 5;
                    $hName = $payloadNames[4];
                }
                if ($hFile['payload5'] != '0') {
                    $hFile['target_days'] = 3;
                    $hName = $payloadNames[5];
                }
                if ($hFile['payload6'] != '0') {
                    $hFile['target_days'] = 2;
                    $hName = $payloadNames[6];
                }
                if ($hFile['payload7'] != '0' && $hFile['payload7'] != '000') {
                    $hFile['target_days'] = 2;
                    $hName = $payloadNames[7];
                }

                $userData = @$efFile[$hFile['sikibetu_id']];
                if (!$userData) {
                    // efファイルに存在しない場合
                    $efFile[$hFile['sikibetu_id']] = [
                        'target_days' => 0,
                        'sanka' => false,
                        'kodomo' => false,
                        'syutyu_days' => [],
                        'syujutsu' => [],
                        'syochi' => [],
                        'sankou' => [],
                        'used_h_files' => [],
                        'not_used_h_files' => [],
                        'a_date_check' => [],
                        'c_date_check' => []
                    ];
                    $userData = $efFile[$hFile['sikibetu_id']];
                }


                // check payload
                $payload = @$userData['c_date_check'][$doDate]['master_payload'];
                $payload = @$hFile['payload' . $payload];

                $efFile[$hFile['sikibetu_id']]['content_type'] = 'C';

                if (
                    @$userData['c_date_check'][$doDate]
                    && $userData['c_date_check'][$doDate]['status'] == 'not checked'
                    && $payload != '0' && $payload != '000'
                 ) {
                    $userData['c_date_check'][$doDate]['status'] = 'checked';
                    $userData['c_date_check'][$doDate]['h_byoutou'] = $hFile['byoutou_code'];
                    $userData['c_date_check'][$doDate]['remark'] = implode(' ', [
                        $hFile['remark1'],
                        $hFile['remark2'],
                        $hFile['remark3'],
                        $hFile['remark4'],
                        $hFile['remark5'],
                        $hFile['remark6'],
                        $hFile['remark7'],
                    ]);
                    if ($hFile['target_days'] == $userData['c_date_check'][$doDate]['master_days']) {
                       $hFile['payload_check'] = true;
                    } else {
                       $hFile['payload_check'] = false;
                    }

                    $efFile[$hFile['sikibetu_id']] = $userData;
                    $efFile[$hFile['sikibetu_id']]['used_h_files'][] = $hFile;

                } elseif (@$userData['c_date_check'][$doDate] && $userData['c_date_check'][$doDate]['status'] == 'syutyu') {

                    if ($hFile['target_days'] == @$userData['c_date_check'][$doDate]['master_days']) {
                       $hFile['payload_check'] = true;
                    } else {
                       $hFile['payload_check'] = false;
                    }
                    $efFile[$hFile['sikibetu_id']]['used_h_files'][] = $hFile;

                } else {
                    // efファイルに存在しない場合
                    if (!isset($userData['c_date_check'][$doDate])) {
                        $userData['c_date_check'][$doDate] = [
                            'h_name' => $hName,
                            'status' => 'h_only',
                            'h_byoutou' => $hFile['byoutou_code'],
                            'remark' => implode(' ', [
                                $hFile['remark1'],
                                $hFile['remark2'],
                                $hFile['remark3'],
                                $hFile['remark4'],
                                $hFile['remark5'],
                                $hFile['remark6'],
                                $hFile['remark7'],
                            ])
                        ];
                        $efFile[$hFile['sikibetu_id']] = $userData;
                    }

                    $efFile[$hFile['sikibetu_id']]['not_used_h_files'][] = $hFile;
                }
            }
        }

        return $efFile;
    }

    private static function encryptionByCodo($id, $code)
    {
        $id = self::trimId($id);
        $code = self::trimId($code);

        if (empty($id) || empty($code)) {
            return $id;
        }
        if (!is_numeric($id)) {
            Log::error("app/dpc/encryptionByCodo:無効なIDがありました " . $id);
            return $id;
        }
        if (!is_numeric($code)) {
            Log::error("app/dpc/encryptionByCodo:無効なCodeがありました " . $code);
            return $id;
        }

        return $id + $code;
    }

    private static function trimId($id)
    {
        return str_replace(array(' ', '　'), '', $id);
    }

    private function storageFileDir()
    {
        return storage_path(config('my.dpc.upload_file_path'));
    }

    private function storageFilePath()
    {
        return $this->storageFileDir() . '/' . $this->file;
    }
}
