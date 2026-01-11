<?php

use App\Enums\UserRole;
use App\Http\Controllers\MedicalRecordController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware([
    'auth',
    'role:'.UserRole::Doctor->value,
])->group(function () {
    // Route::resource('rekam-medis', MedicalRecordController::class);
    Route::get('/rekam-medis', fn () => Inertia::render('MedicalRecord/Index'))
        ->name('rekam-medis');
    Route::get('/rekam-medis/create', [MedicalRecordController::class, 'create'])
        ->name('rekam-medis.create');
    Route::get('/rekam-medis/{id}/edit', [MedicalRecordController::class, 'edit'])
        ->name('rekam-medis.edit');

    Route::get('/api/rekam-medis', [MedicalRecordController::class, 'index'])
        ->name('rekam-medis.index');
    // Route::get('/api/rekam-medis/{id}', [MedicalRecordController::class, 'edit'])
    //     ->name('rekam-medis.edit');
    Route::delete('/api/rekam-medis/{id}', [MedicalRecordController::class, 'destroy'])
        ->name('rekam-medis.delete');
    Route::post('/api/rekam-medis', [MedicalRecordController::class, 'store'])
        ->name('rekam-medis.store');
});

require __DIR__.'/settings.php';
