<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CoinflipController;
use App\Http\Controllers\CrateBattleController;
use App\Http\Controllers\CrateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
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

// Auth::routes();

// Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/crates', [CrateController::class, 'index'])->name('crates');
// Route::get('/profile', [UserController::class, 'index'])->name('profile')->middleware("auth");
// Route::get('/coinflip', [CoinflipController::class, 'index'])->name('coinflip');
// Route::get('/crate/{id}', [CrateController::class, 'view']);
// Route::get('/crate-battles', [CrateBattleController::class, 'index']);
// Route::get('/crate-battles/{id}', [CrateBattleController::class, 'view']);


// Auth
Route::post('/api/login', [LoginController::class, 'login']);
Route::post('/api/logout', [LoginController::class, 'logout']);
Route::post('/api/register', [RegisterController::class, 'register']);

Route::get('/api/crate-list', [CrateController::class, 'getCrateList']);
Route::get('/api/crate/{crate_id}', [CrateController::class, 'getCrate']);
Route::get('/api/server_hash', [CrateController::class, 'getServerHash']);
Route::post('/api/crate/open', [CrateController::class, 'openCrate'])->middleware("auth");
Route::get('/api/user', [UserController::class, 'getUser'])->middleware("auth");
Route::get('/api/user/profile', [UserController::class, 'getUserProfile'])->middleware("auth");
Route::post('/api/user/profile/profit-chart', [UserController::class, 'getProfitChartData'])->middleware("auth");
Route::post('/api/coinflip/join', [CoinflipController::class, 'joinGame'])->middleware("auth");


Route::get('/{any}', [HomeController::class, 'index'])->where('any', ".*");


