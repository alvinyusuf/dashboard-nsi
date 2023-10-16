<?php

use App\Http\Controllers\IpqcController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\MachineFinishController;
use App\Http\Controllers\MachineRepairController;
use App\Http\Controllers\OqcController;
use App\Http\Controllers\QualityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// auto redirect route
Route::get('/', function () {
  return redirect('/maintenance/dashboard-repair');
})->middleware('auth');

// maintenance routes
Route::prefix('maintenance')->middleware('auth')->group(function () {
  // main dashboard maintenance routes
  // repair machines
  Route::resource('/dashboard-repair', MachineRepairController::class)->middleware('auth');
  Route::post('/run-downtime', [MachineRepairController::class, 'downtime'])->middleware('auth');
  Route::post('/export-machine-repairs', [MachineRepairController::class, 'export'])->middleware('auth');
  Route::post('/get-total-downtime-by-month', [MachineRepairController::class, 'getTotalDowntime'])->middleware('auth');

  // finish machine
  Route::get('/dashboard-finish', [MachineFinishController::class, 'index'])->middleware('auth');
  Route::delete('/dashboard-finish/{id}', [MachineFinishController::class, 'destroy'])->middleware('auth');
  Route::post('/export-machine-finish', [MachineFinishController::class, 'export'])->middleware('auth');

  // machines routes
  Route::resource('/machines', MachineController::class)->middleware('auth');
});

// quality routes
Route::prefix('quality')->middleware('auth')->group(function () {
  Route::resource('/home', QualityController::class)->middleware('auth');
  Route::resource('/dashboard-ipqc', IpqcController::class)->middleware('auth');
  Route::resource('/dashboard-oqc', OqcController::class)->middleware('auth');
  Route::post('/export-ipqc', [IpqcController::class, 'export'])->middleware('auth');
  Route::post('/export-oqc', [OqcController::class, 'export'])->middleware('auth');
});

// login routes
Route::get('/login', [LoginController::class, 'indexLogin'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->middleware('guest');
Route::delete('/login', [LoginController::class, 'logout'])->middleware('auth');

// register routes
Route::get('/register', [LoginController::class, 'indexRegister']);
Route::post('/register', [LoginController::class, 'store']);
