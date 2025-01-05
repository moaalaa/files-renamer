<?php

use App\Livewire\Home;
use App\Livewire\Renamer;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/app', Renamer::class)->name('app');
