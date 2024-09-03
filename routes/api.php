<?php

use App\Http\Controllers\CupcakeController;
use App\Models\Cupcake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/cupcake', [CupcakeController::class, 'index'])->name('cupcake.index');
    Route::get('/cupcake/{id}', [CupcakeController::class, 'show'])->name('cupcake.show');
});