
<?php
use App\Http\Controllers\UcapanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UndanganController;
use App\Http\Controllers\UndanganKalenderController;
use App\Http\Controllers\RsvpController;

Route::get('/', [UndanganController::class, 'index'])
    ->name('undangan');

Route::post('/rsvp', [RsvpController::class, 'store'])
    ->middleware('throttle:5,1')->name('rsvp.store');
Route::post('/ucapan', [UcapanController::class, 'store'])
    ->middleware('throttle:5,1')->name('ucapan.store');
Route::get('/kalender.ics', UndanganKalenderController::class)->name('undangan.kalender');
