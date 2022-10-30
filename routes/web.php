<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('/register', [AuthController::class, 'registrationPage'])->name('registerPage');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::group(['middleware' => ['auth:sanctum']], function ($router) {
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('/', [LinkController::class, 'index'])->name('dashboard');
    Route::post('/shorten', [LinkController::class, 'shorten'])->name('shorten');
    Route::get('my-links', [LinkController::class, 'linksPage'])->name('my-links-page');
    Route::get('link/{link:slug}', [LinkController::class, 'viewLink'])->name('view-link');
    Route::get('/all-links-csv', [LinkController::class, 'allLinksCsv'])->name('all-links-csv');
    Route::get('/upload-csv', [LinkController::class, 'uploadCSVPage'])->name('upload-csv-page');
    Route::post('/upload-csv', [LinkController::class, 'uploadCSV'])->name('upload-csv');
});
