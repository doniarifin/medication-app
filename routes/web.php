<?php

use App\Enums\UserRole;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Models\MedicalAttachment;
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
    'role:' . UserRole::Doctor->value,
])->group(function () {
    // Route::resource('rekam-medis', MedicalRecordController::class);
    Route::get('/rekam-medis', [MedicalRecordController::class, 'index'])
        ->name('rekam-medis');
    Route::get('/rekam-medis/create', [MedicalRecordController::class, 'create'])
        ->name('rekam-medis.create');
    Route::get('/rekam-medis/{id}/edit', [MedicalRecordController::class, 'edit'])
        ->name('rekam-medis.edit');

    // api
    Route::post('/api/rekam-medis/insert', [MedicalRecordController::class, 'store'])
        ->name('rekam-medis.store');
    Route::post('/api/rekam-medis/getdata', [MedicalRecordController::class, 'getData'])
        ->name('rekam-medis.index');
    Route::put('/api/rekam-medis/{id}', [MedicalRecordController::class, 'update'])
        ->name('rekam-medis.update');
    Route::delete('/api/rekam-medis/{id}', [MedicalRecordController::class, 'destroy'])
        ->name('rekam-medis.delete');
    //upload
    Route::post('/api/attachment/gets', [AttachmentController::class, 'gets'])
        ->name('attachment.gets');
    Route::post('/api/attachment/upload', [AttachmentController::class, 'upload'])
        ->name('attachment.upload');
});

require __DIR__ . '/settings.php';
