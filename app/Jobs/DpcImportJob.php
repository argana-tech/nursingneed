<?php
/**
 * DpcImportJob class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

use App\Dpc;
use App\User;

class DpcImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId = null;
    private $efFilePath = null;
    private $hFilePath = null;
    private $code = null;
    private $endDate = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $efFilePath, $hFilePath, $code, $endDate)
    {
        $this->userId = $userId;
        $this->efFilePath = $efFilePath;
        $this->hFilePath = $hFilePath;
        $this->code = $code;
        $this->endDate = $endDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // todo memo: php artisan queue:work で確認
        // 常にworkerを起動 https://readouble.com/laravel/5.3/ja/queues.html#supervisor-configuration

        Log::error('DpcImportJob start');

        $res = Dpc::import(
            $this->userId,
            $this->efFilePath,
            $this->hFilePath,
            $this->code,
            $this->endDate
        );
        Log::error('DpcImportJob end');

        // 処理中フラグオフ
        $status = ($res)? User::$dpc_status_successfully : User::$dpc_status_failed;
        $user = User::findOrFail($this->userId);
        $user->is_dpc_loading = 0;
        $user->dpc_import_status = $status;
        $user->save();

        return true;
    }
}
