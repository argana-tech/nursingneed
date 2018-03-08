<?php
/**
 * CItem class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CItem extends Model
{
    protected $fillable = [
        'user_id', 'days', 'name', 'code', 'remark',
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
        $user->cItems()->delete();

        $fp = fopen($this->storageFilePath(), 'r');
        while (($row = fgetcsv($fp, 0, "\t")) !== FALSE) {
            $data = [
                'user_id' => $user->id,
                'days' => isset($row[0])? trim_space($row[0]) : '',
                'name' => isset($row[1])? trim_space($row[1]) : '',
                'code' => isset($row[2])? trim_space($row[2]) : '',
                'remark' => isset($row[3])? trim_space($row[3]) : '',
            ];

            if (
                empty($data['days']) || !is_numeric($data['days'])
                || empty($data['name'])
                || empty($data['code']) || !is_numeric($data['code'])
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
            '日数', '名称', 'コード', '備考'
        ];

        // import
        $stream = fopen('php://temp', 'r+b');

        /*fputcsv(
            $stream,
            $columns,
            "\t"
        );*/

        $user = auth()->user();
        $items = $user->cItems;
        if ($items->count() > 0) {
            foreach ($items as $item) {
                $row = [
                    $item->days,
                    $item->name,
                    $item->code,
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
