<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CupcakeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\isAdmin;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/cupcake/{id}', [CupcakeController::class, 'show'])->name('cupcake.show');
    Route::delete('/cupcake/{id}', [CupcakeController::class, 'destroy'])->name('cupcake.delete');
    Route::post('/cupcake/create', [CupcakeController::class, "store"])->name('cupcake.store');
    Route::get('/cupcake', [CupcakeController::class, 'index'])->name('cupcake.index');
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    
    Route::get("/category/{id}", [CategoryController::class, 'show'])->name("category.show");


    // isAdmin routes
    Route::group(['middleware' => isAdmin::class], function() {

        Route::get('/admin/user', [UserController::class, function () {return User::all();}])->name('admin.users.index');
    });
});


// créer des ressources pour retourner les données dans une meilleur format

