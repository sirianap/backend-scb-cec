<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('login', 'UserController@login');
Route::post('register', 'UserController@register');
 
Route::group(['middleware' => 'auth:api'], function(){
    Route::get('users','UserController@index');
    Route::post('user/{user}','UserController@update');
    Route::post('getuser', 'UserController@details');
    Route::post('logout', 'UserController@logout');
    Route::get('transaksi2','TransaksiController@transaksi');
    Route::post('transaksi/detail','TransaksiController@storeDetail');
    Route::resource('transaksi', 'TransaksiController');
});

Route::get('siswa/{nis}','SiswaController@show');
Route::resource('siswa', 'SiswaController');



Route::get('produk/{produk:nomor_produk}','ProdukController@show');
Route::resource('produk', 'ProdukController');
