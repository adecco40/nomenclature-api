<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\ChangeController;
use App\Http\Controllers\Api\AuthenticationController;

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello from API!']);
});


// Route::middleware('auth:api')->group(function () {
//     Route::apiResource('products', ProductController::class);
//     Route::apiResource('categories', CategoryController::class);
//     Route::apiResource('suppliers', SupplierController::class);
//     Route::get('changes', [ChangeController::class, 'index']);
// });

Route::middleware('auth:api')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::post('products/upload', [ProductController::class, 'upload']);
    Route::post('products/import', [ProductController::class, 'import']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('suppliers', SupplierController::class);

    Route::get('changes', [ChangeController::class, 'index']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
 
Route::post('register', [AuthenticationController::class, 'register'])->name('register');
Route::post('login', [AuthenticationController::class, 'login'])->name('login');


// Route::middleware('auth:api')->group(function () {
//     Route::apiResource('products', ProductController::class);
//     Route::post('products/upload', [ProductController::class, 'upload']);
//     Route::post('products/import', [ProductController::class, 'import']);

//     Route::apiResource('categories', CategoryController::class);
//     Route::apiResource('suppliers', SupplierController::class);

//     Route::get('changes', [ChangeController::class, 'index']);
// });
