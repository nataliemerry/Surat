<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagesController;
use App\Http\Controllers\OrganizationsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;


Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name('login.store')
    ->middleware('guest');

Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

Route::get('/', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::get('users', [UsersController::class, 'index'])
    ->name('users')
    ->middleware('auth');

Route::get('users/create', [UsersController::class, 'create'])
    ->name('users.create')
    ->middleware('auth');

Route::post('users', [UsersController::class, 'store'])
    ->name('users.store')
    ->middleware('auth');

Route::get('users/{user}/edit', [UsersController::class, 'edit'])
    ->name('users.edit')
    ->middleware('auth');

Route::put('users/{user}', [UsersController::class, 'update'])
    ->name('users.update')
    ->middleware('auth');

Route::delete('users/{user}', [UsersController::class, 'destroy'])
    ->name('users.destroy')
    ->middleware('auth');

Route::put('users/{user}/restore', [UsersController::class, 'restore'])
    ->name('users.restore')
    ->middleware('auth');

Route::get('/img/{path}', [ImagesController::class, 'show'])
    ->where('path', '.*')
    ->name('image');

Route::get('/surat-pengantar/form', [SuratController::class, 'formSuratPengantar'])
    ->name('form.surat.pengantar');

Route::get('/surat-undangan/form', [SuratController::class, 'formSuratUndangan'])
    ->name('form.surat.undangan');

Route::get('/surat-tugas/form', [SuratController::class, 'formSuratTugas'])
    ->name('form.surat.tugas');

Route::get('/surat-tugas', [SuratController::class, 'optionSuratTugas'])
    ->name('option.surat-tugas');

Route::get('/surat-tugas/upload-surat', [SuratController::class, 'uploadSuratTugas'])
    ->name('surat-tugas.upload-surat');

Route::post('/surat-pengantar/create', [SuratController::class, 'storeSuratPengantar'])
    ->name('surat.store.pengantar');

Route::post('/surat-undangan/create', [SuratController::class, 'storeSuratUndangan'])
    ->name('surat.store.undangan');

Route::post('/surat-tugas/create', [SuratController::class, 'storeSuratTugas'])
    ->name('surat.store.tugas');

Route::post('/surat-tugas/update', [SuratController::class, 'updateSuratTugas'])
    ->name('surat.update.tugas');

Route::put('surat-tugas/{surat}', [SuratController::class, 'editedSuratTugas'])
    ->name('surat-tugas.update')
    ->middleware('auth');

Route::get('surat-tugas/{surat}/edit', [SuratController::class, 'editSuratTugas'])
    ->name('surat-tugas.edit')
    ->middleware('auth');