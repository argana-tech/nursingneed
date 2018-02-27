<?php
/**
 * DpcController class
 *
 * @package 看護必要度チェッカ―
 * @author     eBase Solutions, Inc <info@ebase-sl.jp>
 * @copyright  2018 eBase Solutions, Inc
**/

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\User;

class DpcController extends Controller
{
    public function getImportStatus($id)
    {
        $user = User::findOrFail($id);

        $data = [
            'is_dpc_loading' => $user->is_dpc_loading,
            'dpc_import_status' => $user->dpc_import_status
        ];
        return response()->json($data);
    }
}
