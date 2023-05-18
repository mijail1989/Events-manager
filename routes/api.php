<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\API\AuthenticationController;

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
// Route::get("events",[EventsController::class,"index"]);
Route::get("/events",[EventsController::class,"index"]);
Route::post("/events",[EventsController::class,"create"]);

Route::get("/events/{id}",[EventsController::class,"getEvent"]);
Route::put("/events/{id}",[EventsController::class,"update"]);


Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
Route::post('/register', [AuthenticationController::class, 'register'])->name('register');  
// logout is a protected endpoint
Route::middleware('auth:sanctum')->post('/logout', [AuthenticationController::class, 'logout'])->name('logout');