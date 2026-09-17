<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/reports/download/{file}', [\App\Http\Controllers\ReportDownloadController::class, 'download'])
    ->name('report.download')
    ->middleware('signed');


