<?php

use App\Models\Kriteria;
use App\Models\Alternatif;
use Illuminate\Support\Facades\Route;
use App\Models\PerbandinganSubKriteria;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\KriteriaSAWController;
use App\Http\Controllers\PerhitunganController;
use App\Http\Controllers\SubKriteriaController;
use App\Http\Controllers\AlternatifSAWController;
use App\Http\Controllers\PerhitunganSAWController;
use App\Http\Controllers\SubKriteriaSAWController;
use App\Http\Controllers\PerbandinganKriteriaController;
use App\Http\Controllers\PerbandinganSubkriteriaController;

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
// AUTH
Route::get('/auth', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/authenticate', [AuthController::class, 'authenticate']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/register', [AuthController::class, 'register']);
Route::post('/register-create', [AuthController::class, 'create']);

Route::get('/', function () {
    $data = [
        'title' => 'Dashboard',
        'alternatif' => Alternatif::count('id'),
        'kriteria' => Kriteria::count('id'),
    ];
    return view('dashboard', $data);
})->middleware('auth');

Route::get('/home', function () {
    $data = [
        'title' => 'Dashboard',
        'alternatif' => Alternatif::count('id'),
        'kriteria' => Kriteria::count('id'),
    ];
    return view('dashboard', $data);
})->middleware('auth');

// ROUTE Kriteria dan Subkriteria
Route::resource('/kriterias', KriteriaController::class)->middleware('auth');
Route::get('/dataTablesKriterias', [KriteriaController::class, 'dataTables'])->middleware('auth');
Route::resource('/subkriterias', SubKriteriaController::class)->middleware('auth');
Route::get('/dataTablesSubKriterias', [SubKriteriaController::class, 'dataTables'])->middleware('auth');
Route::resource('/alternatifs', AlternatifController::class)->middleware('auth');
Route::get('/dataTablesAlternatifs', [AlternatifController::class, 'dataTables'])->middleware('auth');
// Route::get('/kriteria/{kriteria:unique}/edit', [KriteriaController::class, 'edit'])->middleware('auth');
Route::resource('/perbandingan-kriterias', PerbandinganKriteriaController::class)->middleware('auth');
Route::resource('/perbandingan-subkriterias', PerbandinganSubkriteriaController::class)->middleware('auth');
Route::resource('/perhitungans', PerhitunganController::class)->middleware('auth');
Route::get('/perhitungans/create', [PerhitunganController::class, 'create'])->middleware('auth');
Route::get('/perhitungans/update/{id}', [PerhitunganController::class, 'update'])->middleware('auth');
Route::get('/normalisasi', [PerhitunganController::class, 'normalisasi'])->middleware('auth');
Route::get('/perhitunganSAW', [PerhitunganController::class, 'perhitungan_saw'])->middleware('auth');
Route::post('/perhitungans-perangkingan', [PerhitunganController::class, 'perhitungan_perangkingan'])->middleware('auth');
Route::get('/ranking', [PerhitunganController::class, 'ranking'])->middleware('auth');
Route::get('/', function () {
    $data = [
        'title' => 'Dashboard',
        'alternatif' => Alternatif::count('id'),
        'kriteria' => Kriteria::count('id'),
    ];
    return view('saw.dashboard', $data);
})->middleware('auth');
Route::get('/home', function () {
    $data = [
        'title' => 'Dashboard',
        'alternatif' => Alternatif::count('id'),
        'kriteria' => Kriteria::count('id'),
    ];
    return view('saw.dashboard', $data);
})->middleware('auth');
Route::get('/dataTablesUser', [AuthController::class, 'dataTables'])->middleware('auth');

// KRITERIA
Route::resource('/kriteria', KriteriaSAWController::class)->middleware('auth');
Route::get('/dataTablesKriteria', [KriteriaSAWController::class, 'dataTablesKriteria'])->middleware('auth');
Route::get('/kriteriaEdit/{kreteria:uuid}', [KriteriaSAWController::class, 'edit'])->middleware('auth');
// SUB KRITERI->middleware('auth')A
Route::resource('/subKriteria', SubKriteriaSAWController::class)->middleware('auth');
Route::get('/dataTablesSubKriteria', [SubKriteriaSAWController::class, 'dataTablesSubKriteria'])->middleware('auth');
// Alternati->middleware('auth')f
Route::get('alternatif', [AlternatifSAWController::class, 'index'])->middleware('auth');
Route::get('/dataTablesAlternatif', [AlternatifSAWController::class, 'dataTablesAlternatif'])->middleware('auth');
Route::post('/alternatif-store', [AlternatifSAWController::class, 'store'])->middleware('auth');
Route::get('/alternatif-edit/{alternatif:uuid}', [AlternatifSAWController::class, 'edit'])->middleware('auth');
Route::post('/alternatif-update/{alternatif:uuid}', [AlternatifSAWController::class, 'update'])->middleware('auth');
Route::post('/alternatif-destroy/{alternatif:uuid}', [AlternatifSAWController::class, 'destroy'])->middleware('auth');
// Perhitunga Moor->middleware('auth')a
Route::get('/perhitungan', [PerhitunganSAWController::class, 'index'])->middleware('auth');
Route::get('/perhitungan-create', [PerhitunganSAWController::class, 'create'])->middleware('auth');
Route::get('/perhitungan-update/{perhitungan:uuid}', [PerhitunganSAWController::class, 'update'])->middleware('auth');
Route::get('/saw-normalisasi', [PerhitunganSAWController::class, 'normalisasi'])->middleware('auth');
Route::get('/saw-preferensi', [PerhitunganSAWController::class, 'preferensi'])->middleware('auth');
Route::get('/saw', [PerhitunganSAWController::class, 'index_saw'])->middleware('auth');
Route::get('/waspas', [PerhitunganSAWController::class, 'index_waspas'])->middleware('auth');
Route::get('/waspas-normalisasi', [PerhitunganSAWController::class, 'normalisasi_waspas'])->middleware('auth');
