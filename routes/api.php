<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NodeController;

Route::middleware('api')->group(function () {
    Route::post('nodes', [NodeController::class, 'store']);
    Route::get('nodes/roots', [NodeController::class, 'roots']);
    Route::get('nodes/{node}/children', [NodeController::class, 'children']);
    Route::delete('nodes/{node}', [NodeController::class, 'destroy']);
});
