<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::view('admin/{path?}', 'app-be');
//Route::view('/{path?}', 'app');

Route::group(['prefix' => 'admin/videos'], function () {
    //Index
    Route::get('', [
        'uses' => 'PostController@getIndex',
        'as' => 'admin.videos.index'
    ]);

    //Add
    Route::get('add', [
        'uses' => 'VideoController@getAdd',
        'as' => 'admin.videos.add']
    );
    Route::post('add', [
        'uses' => 'VideoController@postAdd',
        'as' => 'admin.videos.add'
    ]);
    //Update
    Route::get('edit/{id}', [
        'uses' => 'VideoController@getEdit',
        'as' => 'admin.videos.edit'
    ]);
    Route::post('edit', [
        'uses' => 'VideoController@postEdit',
        'as' => 'admin.videos.update'
    ]);
});
