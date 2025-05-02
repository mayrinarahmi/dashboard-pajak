<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\LaporanStandarController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});




Route::get('/test-pdf', function() {
    $html = '<h1>Test PDF</h1><p>Ini adalah PDF test pada ' . date('Y-m-d H:i:s') . '</p>';
    $pdf = Pdf::loadHTML($html);
    return $pdf->download('test.pdf');
});

Route::get('/test-assets', function() {
    return view('test-assets');
});

Route::get('/laporan-standar', [LaporanStandarController::class, 'index'])->name('laporan.standar.index');
Route::post('/laporan-standar/generate', [LaporanStandarController::class, 'generate'])->name('laporan.standar.generate');
Route::get('/laporan-standar/pdf', [LaporanStandarController::class, 'generatePdf'])->name('laporan.standar.pdf');
