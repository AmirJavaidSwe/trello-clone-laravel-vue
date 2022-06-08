<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CardsController;
use App\Http\Controllers\Api\ColumnsController;
use App\Http\Controllers\Api\SortingController;

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

Route::post('update-sorting', [SortingController::class, 'updateSort']);
Route::apiResource('column', ColumnsController::class);
Route::apiResource('card', CardsController::class);
