<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });
use App\Http\Controllers\EksporController;
Route::get('/logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');
Auth::routes(['verify' => true]);
Route::get('/',[App\Http\Controllers\BerandaController::class, 'index'])->name('home');
Route::get('/home',[App\Http\Controllers\BerandaController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard',[App\Http\Controllers\BerandaController::class, 'dashboard'])->name('dashboard')->middleware('verified');
    Route::post('/organisasi/join', [App\Http\Controllers\OrganisasiController::class, 'join'])->name('organisasi.join')->middleware('throttle:5,1');
    Route::post('/organisasi/keluar/{idOrg}/{idUser}', [App\Http\Controllers\OrganisasiController::class, 'keluar'])->name('organisasi.keluar');
    Route::post('/organisasi/ubah_role/{id}', [App\Http\Controllers\OrganisasiController::class, 'ubah_role'])->name('organisasi.ubah.role');
    Route::resource('organisasi', App\Http\Controllers\OrganisasiController::class)->except('show');
    Route::get('/organisasi/member', [App\Http\Controllers\OrganisasiController::class, 'member'])->name('organisasi.member');
    Route::put('/organisasi/{id}', [App\Http\Controllers\OrganisasiController::class, 'update'])->name('organisasi.update');
    Route::resource('sertifikat', App\Http\Controllers\SertifikatController::class);
    Route::get('/sertifikat/template', [App\Http\Controllers\SertifikatController::class, 'template'])->name('sertifikat.template');
});


Route::view('/sertifikat/generate', 'sertifikat.generate');
Route::view('/sertifikat/penerima', 'sertifikat.penerima');
Route::view('/users', 'users.index');
Route::view('/pengaturan', 'pengaturan.index');

Route::get('/clear-cache', function() {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');
    return "✅ Cache cleared successfully!";
});
