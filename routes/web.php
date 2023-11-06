<?php

use App\Http\Controllers\CrateController;
use App\Models\Crate;
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

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/crates', [CrateController::class, 'index'])->name('crates');
Route::get('/crate/{id}', [CrateController::class, 'view']);

Route::get('/api/crate-list', [CrateController::class, 'getCrateList']);
Route::get('/api/crate-contents/{crate_id}', [CrateController::class, 'getCrateContents']);
Route::get('/api/server_hash', [CrateController::class, 'getServerHash']);
Route::post('/api/crate/open', [CrateController::class, 'openCrate']);
