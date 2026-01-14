<?php

use App\Enums\UserRole;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\MedicinesController;
use App\Http\Controllers\ResepDokterController;
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
    /**
     * route untuk inertia
     */

    // rekam medis
    Route::get('/rekam-medis', function () {
        return Inertia::render('MedicalRecord/Index');
    })->name('rekam-medis');
    Route::get('/rekam-medis/create', [MedicalRecordController::class, 'create'])
        ->name('rekam-medis.create');
    Route::get('/rekam-medis/{id}/edit', [MedicalRecordController::class, 'edit'])
        ->name('rekam-medis.edit');

    //resep dokter
    Route::get('/resep', function () {
        return Inertia::render('ResepDokter/Index');
    })->name('resep');


    // medicines
    Route::get('/masterObat', function () {
        return Inertia::render('MasterObat/Index');
    })->name('masterObat');


    /**
     * route untuk api
     */

    // api rekam-meids
    Route::post('/api/rekam-medis/insert', [MedicalRecordController::class, 'store'])
        ->name('rekam-medis.store');
    Route::post('/api/rekam-medis/getdata', [MedicalRecordController::class, 'getData'])
        ->name('rekam-medis.getdata');
    Route::put('/api/rekam-medis/{id}', [MedicalRecordController::class, 'update'])
        ->name('rekam-medis.update');
    Route::delete('/api/rekam-medis/{id}', [MedicalRecordController::class, 'destroy'])
        ->name('rekam-medis.delete');

    //upload
    Route::post('/api/attachment/gets', [AttachmentController::class, 'gets'])
        ->name('attachment.gets');
    Route::post('/api/attachment/upload', [AttachmentController::class, 'upload'])
        ->name('attachment.upload');

    //api medicines
    Route::post('/api/medicines/getdata', [MedicinesController::class, 'getData'])
        ->name('medicines.getdata');
    Route::post('/api/medicines/getprice', [MedicinesController::class, 'getPrice'])
        ->name('medicines.getprice');


    //api resep dokter
    Route::post('/api/resep/getdata', [ResepDokterController::class, 'getData'])
        ->name('resep.index');
    Route::post('/api/resep/update', [ResepDokterController::class, 'update'])
        ->name('resep.update');
});

require __DIR__ . '/settings.php';
