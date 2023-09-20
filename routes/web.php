<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
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

Route::get('/', [TaskController::class, 'index']);
Route::post('/try', [TaskController::class, 'create']);
Route::post('/remove/{id}', [TaskController::class, 'remove']);
Route::post('/update/{id}', [TaskController::class, 'update']);
Route::get('/view-attachments/{id}', [TaskController::class, 'viewAttach']);
Route::get('/remove-attach/{name}/{id}', [TaskController::class, 'removeAttach']);
Route::get('/completed/{id}', [TaskController::class, 'completed']);