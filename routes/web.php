<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return redirect('/admin'); 
});

Route::get('/login', function () {
    return redirect('/admin'); 
});
