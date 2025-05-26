<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;


Route::get('/', function () {
    return view('welcome');
});

Route::view('/upload', 'upload');

Route::post('/upload-chunk', [FileUploadController::class, 'uploadChunk']);
