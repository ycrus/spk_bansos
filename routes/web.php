<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin'); // Ganti '/admin' sesuai prefix panel Filament-mu
});
