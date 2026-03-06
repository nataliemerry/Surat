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

Route::get('/surat-tugas', [SuratController::class, 'optionSuratTugas'])
    ->name('option.surat-tugas');

Route::get('/surat-dinas', [SuratController::class, 'optionSuratDinas'])
    ->name('option.surat-dinas');

Route::get('/surat-undangan', [SuratController::class, 'optionSuratUndangan'])
    ->name('option.surat-undangan');

Route::get('/surat-dinas/form', [SuratController::class, 'formSuratDinas'])
    ->name('form.surat.dinas');

Route::get('/surat-undangan/form', [SuratController::class, 'formSuratUndangan'])
    ->name('form.surat.undangan');

Route::get('/surat-tugas/form', [SuratController::class, 'formSuratTugas'])
    ->name('form.surat.tugas');

Route::get('/surat-dinas/upload-surat', [SuratController::class, 'uploadSuratDinas'])
    ->name('surat-dinas.upload-surat');

Route::get('/surat-undangan/upload-surat', [SuratController::class, 'uploadSuratUndangan'])
    ->name('surat-undangan.upload-surat');

Route::get('/surat-tugas/upload-surat', [SuratController::class, 'uploadSuratTugas'])
    ->name('surat-tugas.upload-surat');

Route::post('/surat-dinas/create', [SuratController::class, 'storeSuratDinas'])
    ->name('surat.store.dinas');

Route::post('/surat-undangan/create', [SuratController::class, 'storeSuratUndangan'])
    ->name('surat.store.undangan');

Route::post('/surat-tugas/create', [SuratController::class, 'storeSuratTugas'])
    ->name('surat.store.tugas');

Route::post('/surat-tugas/update', [SuratController::class, 'updateSuratTugas'])
    ->name('surat.update.tugas');

Route::post('/surat-undangan/update', [SuratController::class, 'updateSuratUndangan'])
    ->name('surat.update.undangan');

Route::post('/surat-dinas/update', [SuratController::class, 'updateSuratDinas'])
    ->name('surat.update.dinas');

Route::put('surat-tugas/{surat}', [SuratController::class, 'editedSuratTugas'])
    ->name('surat-tugas.update')
    ->middleware('auth');

Route::get('surat-tugas/{surat}/edit', [SuratController::class, 'editSuratTugas'])
    ->name('surat-tugas.edit')
    ->middleware('auth');

Route::delete('surat-tugas/{surat}', [SuratController::class, 'destroySuratTugas'])
    ->name('surat-tugas.destroy')
    ->middleware('auth');

Route::get('surat-undangan/{surat}/edit', [SuratController::class, 'editSuratUndangan'])
    ->name('surat-undangan.edit')
    ->middleware('auth');

Route::put('surat-undangan/{surat}', [SuratController::class, 'editedSuratUndangan'])
    ->name('surat-undangan.update')
    ->middleware('auth');

Route::delete('surat-undangan/{surat}', [SuratController::class, 'destroySuratUndangan'])
    ->name('surat-undangan.destroy')
    ->middleware('auth');

Route::get('surat-dinas/{surat}/edit', [SuratController::class, 'editSuratDinas'])
    ->name('surat-dinas.edit')
    ->middleware('auth');

Route::put('surat-dinas/{surat}', [SuratController::class, 'editedSuratDinas'])
    ->name('surat-dinas.update')
    ->middleware('auth');

Route::delete('surat-dinas/{surat}', [SuratController::class, 'destroySuratDinas'])
    ->name('surat-dinas.destroy')
    ->middleware('auth');