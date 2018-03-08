<?php
/**
 * ObstetricsItem class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ObstetricsItem extends Model
{
    protected $fillable = [
        'user_id', 'name', 'code', 'kcode', 'remark',
    ];

    protected $hidden = [
        'file',
    ];

    public function insertTsvdata($file)
    {
        $user = auth()->user();
        $this->file = uniqid();
        $file->move($this->storageFileDir(), $this->file);

        DB::beginTransaction();
        $user->obstetricsItems()->delete();

        $fp = fopen($this->storageFilePath(), 'r');
        while (($row = fgetcsv($fp, 0, "\t")) !== FALSE) {
            $data = [
                'user_id' => $user->id,
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

            if (!self::create($data)) {
                //DB::rollBack();
            }
        }
        fclose($fp);

        DB::commit();

        unlink($this->storageFilePath());

        return true;
    }

    public static function getCsvdata()
    {
        $columns = [
            '名称', 'コード', 'Kコード', '備考'
        ];

        // import
        $stream = fopen('php://temp', 'r+b');

        /*fputcsv(
            $stream,
            $columns,
            "\t"
        );*/

        $user = auth()->user();
        $items = $user->obstetricsItems;
        if ($items->count() > 0) {
            foreach ($items as $item) {
                $row = [
                    $item->name,
                    $item->code,
                    $item->kcode,
                    $item->remark,
                ];

                fputcsv(
                    $stream,
                    $row,
                    "\t"
                );
            }
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        // convert
        $csv = str_replace(
            PHP_EOL,
            "\r\n",
            $csv
        );

        /*$csv = mb_convert_encoding(
            $csv,
            'SJIS-win',
            'UTF-8'
        );*/

        return $csv;
    }

    private function storageFileDir()
    {
        return storage_path(config('my.master.upload_file_path'));
    }

    private function storageFilePath()
    {
        return $this->storageFileDir() . '/' . $this->file;
    }
}
