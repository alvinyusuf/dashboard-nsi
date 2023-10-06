<?php

use App\Http\Controllers\MachineRepairController;
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

Route::resource('/dashboard', MachineRepairController::class);
Route::post('/run-downtime', [MachineRepairController::class, 'downtime']);

Route::get('/cek', [MachineRepairController::class, 'cek']);
