<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::group(['middleware' => ['guest:web']], function () {
    Route::get('signin', [
        'as' => 'auth.signin_form',
        'uses' => 'AuthController@signinForm',
    ]);

    Route::post('signin', [
        'as' => 'auth.signin',
        'uses' => 'AuthController@signin',
    ]);
});

Route::group(['middleware' => ['auth:web']], function () {
    Route::get('signout', [
        'as' => 'auth.signout',
        'uses' => 'AuthController@signout',
    ]);

    Route::get('/', [
        'as' => 'root.index',
        'uses' => 'RootController@index',
    ]);

    // ファイル取込
    Route::get('dpc', [
        'as' => 'dpc.index',
        'uses' => 'DpcController@index',
    ]);

    Route::post('dpc/upload', [
        'as' => 'dpc.upload',
        'uses' => 'DpcController@upload',
    ]);

    // 結果
    Route::resource(
        'results',
        'ResultController',
        ['only' => ['index', 'show']]
    );

    // マスタ
    Route::group(['namespace' => 'Master', 'prefix' => 'master'], function () {
        Route::resource(
            'a_items',
            'AItemController',
            ['only' => ['index', 'edit', 'update']]
        );

        Route::post('a_items/upload', [
            'as' => 'a_items.upload',
            'uses' => 'AItemController@upload',
        ]);

        Route::get('a_items/download', [
            'as' => 'a_items.download',
            'uses' => 'AItemController@download',
        ]);

        Route::resource(
            'c_items',
            'CItemController',
            ['only' => ['index', 'edit', 'update']]
        );

        Route::post('c_items/upload', [
            'as' => 'c_items.upload',
            'uses' => 'CItemController@upload',
        ]);

        Route::get('c_items/download', [
            'as' => 'c_items.download',
            'uses' => 'CItemController@download',
        ]);

        Route::resource(
            'obstetrics_items',
            'ObstetricsItemController',
            ['only' => ['index', 'edit', 'update']]
        );

        Route::post('obstetrics_items/upload', [
            'as' => 'obstetrics_items.upload',
            'uses' => 'ObstetricsItemController@upload',
        ]);

        Route::get('obstetrics_items/download', [
            'as' => 'obstetrics_items.download',
            'uses' => 'ObstetricsItemController@download',
        ]);
    });

    // システム設定
    Route::resource(
        'system',
        'SystemController',
        ['only' => ['index', 'edit', 'update']]
    );
});
