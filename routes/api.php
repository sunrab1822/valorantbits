<?php

use App\Http\Controllers\CrateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/crate-list', [CrateController::class, 'getCrateList']);
Route::get('/crate-contents/{crate_id}', [CrateController::class, 'getCrateContents']);
Route::get('/server_hash', [CrateController::class, 'getServerHash']);
Route::post('/crate/open', [CrateController::class, 'openCrate']);
