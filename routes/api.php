<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api', 'prefix' => 'api'], function () {
        Route::get('dpc/get_import_status/{id}', [
            'as' => 'api.dpc.get_import_status',
            'uses' => 'DpcController@getImportStatus',
        ])->where('id', '[0-9]+');
});
