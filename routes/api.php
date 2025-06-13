<?php

use App\Http\Controllers\TodoListController;
use Illuminate\Support\Facades\Route;


Route::post('/todo', [TodoListController::class, 'store']);
Route::get('/chart', [TodoListController::class, 'getChartData']);
Route::get('/generate-excel', [TodoListController::class, 'generateExcelReport']);
