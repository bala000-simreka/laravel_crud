<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PlotlyController;

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
    if (Auth::check()) {
        return redirect()->route('plotly.index');
    } else {
        return redirect()->route('login');
    }
    //return view('welcome');
});

Auth::routes();
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', function(){
    return redirect()->route('plotly.index');
})->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::resource('users', UserController::class);
    Route::resource('employees', EmployeeController::class);

    Route::get('plotly', [PlotlyController::class, 'index'])->name('plotly.index');
    Route::get('plotly/visualise', [PlotlyController::class, 'visualise'])->name('plotly.visualise');
    Route::get('plotly/list-data', [PlotlyController::class, 'listData'])->name('plotly.list');
    Route::post('plotly-get-column-data', [PlotlyController::class, 'getColumnDataCSV'])->name('plotly.ajax_get_column_data');
    Route::get('plotly/list-new', [PlotlyController::class, 'importCSV'])->name('plotly.listnew');
    Route::get('plotly/list-sheet', [PlotlyController::class, 'listCSVsheetjs'])->name('plotly.listsheet');
    Route::get('plotly/list-table', [PlotlyController::class, 'listCSVTable'])->name('plotly.listtable');    
});
