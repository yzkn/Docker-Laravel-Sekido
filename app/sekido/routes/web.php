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

Route::get('/', function () {
    return view('home');
})->name('home');

Auth::routes();

// システム管理者のみ
Route::group(['middleware' => ['auth', 'can:system-only']], function () {
});

// 管理者以上
Route::group(['middleware' => ['auth', 'can:admin-higher']], function () {
  // ファイルアップロード
  Route::get('/music/upload', 'MusicController@upload')->name('music.upload');
});

// 全員
Route::group(['middleware' => ['auth', 'can:user-higher']], function () {
    // 検索
    Route::get('/music/search', 'MusicController@searchform')->name('music.searchform');
    Route::post('/music/search', 'MusicController@search')->name('music.search');

    // グループ化リスト
    Route::get('/music/list', 'MusicController@list')->name('music.list');

    // 楽曲CRUD
    Route::resource('/music', 'MusicController');

    // プレイリストCRUD
    Route::resource('/playlist', 'PlaylistController');

    // アップロードされたファイル
    Route::get('/c/{path}', 'FileController@covers');
    Route::get('/d/{path}', 'FileController@documents');
    Route::get('/m/{path}', 'FileController@musics');
});