<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Num_FactureController;
use App\Http\Controllers\Read_ExcelController;
use App\Http\Controllers\AdminController;

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
Route::get('file-read', [Read_ExcelController::class, 'Read'])->name('file-read');
Route::get('IsPaye', [Read_ExcelController::class, 'ReadIsPaye'])->name('IsPaye');
Route::get('FactChan', [Read_ExcelController::class, 'FactureChantier'])->name('FactChan');
Route::get('AdminUpdate', [AdminController::class, 'GitUpdate'])->name('AdminUpdate');
Route::get('excel-edit', ['as' => 'excel-edit', 'uses' => 'App\Http\Controllers\Num_FactureController@index']);
Route::get('excel-read', ['as' => 'excel-read', 'uses' => 'App\Http\Controllers\Read_ExcelController@index']);
Route::get('excel-echeance', ['as' => 'excel-echeance', 'uses' => 'App\Http\Controllers\ReadEcheanceController@index']);
Route::get('Admin-Update', ['as' => 'Admin-Update', 'uses' => 'App\Http\Controllers\AdminController@index']);
Route::resource('Num_Facture',Num_FactureController::class);
Route::resource('Read_Excel',Read_ExcelController::class);
Route::resource('Admin',AdminController::class);
Route::resource('Echeance','ReadEcheanceController');

Route::get('get-oFichier', function()
{
    dd(config('global.oFichier'));
});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
