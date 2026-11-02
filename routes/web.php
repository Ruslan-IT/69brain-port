<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\EnbController;
use App\Http\Controllers\PageController;
use App\Support\SiteUrl;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/raschety-na-prochnost', [PageController::class, 'strength'])->name('strength');
Route::get('/statisticheskie-raschety', [PageController::class, 'statistics'])->name('statistics');
Route::get('/tehnologicheskie-raschety', [PageController::class, 'technology'])->name('technology');

Route::get('/enb', [EnbController::class, 'show'])->name('enb');
Route::get('/enb/login', fn () => redirect()->route('enb'));
Route::post('/enb/login', [EnbController::class, 'login'])->middleware('throttle:5,1')->name('enb.login');
Route::post('/enb', [EnbController::class, 'update'])->name('enb.update');

Route::get('/kontakty', [ContactController::class, 'show'])->name('contacts');
Route::post('/kontakty', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contacts.store');

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', SiteUrl::slugPattern())
    ->name('service.show');
