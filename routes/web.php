<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return redirect('/admin'); // Ganti '/admin' sesuai prefix panel Filament-mu
});
