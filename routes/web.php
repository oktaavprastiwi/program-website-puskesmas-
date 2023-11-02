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

Auth::routes();

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', 'HomeController@index')->name('home');
Route::get('/upload', 'UploadController@create')->name('upload');
Route::post('/upload', 'UploadController@store')->name('upload.process');
Route::get('/diagnosis', 'DiagnosisController@index')->name('diagnosis');
Route::get('/diagnosis/{fileId}', 'DiagnosisController@show')->name('diagnosis.detail');
Route::delete('/diagnosis/{fileId}', 'DiagnosisController@destroy')->name('diagnosis.delete');
Route::get('/clustering/{fileId}', 'ClusteringController@create')->name('clustering');
Route::post('/clustering/{fileId}', 'ClusteringController@store')->name('clustering.process');
Route::get('/clustering/{fileId}/result', 'ClusteringController@index')->name('clustering.result');