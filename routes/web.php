<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::get('/contact', [ContactController::class, 'contact'])->name('contact');
Route::get('/contactlist', [ContactController::class, 'contactlist'])->name('contactlist');
Route::post('/store', [ContactController::class, 'store'])->name('contact.store');