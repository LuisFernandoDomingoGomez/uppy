<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\CardWizardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('can:view dashboard')
        ->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Usuarios
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('can:view users')
        ->name('users.index');

    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('can:create users')
        ->name('users.create');

    Route::post('/users', [UserController::class, 'store'])
        ->middleware('can:create users')
        ->name('users.store');

    Route::get('/users/{user}', [UserController::class, 'show'])
        ->middleware('can:view users')
        ->name('users.show');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('can:edit users')
        ->name('users.edit');

    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('can:edit users')
        ->name('users.update');

    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('can:delete users')
        ->name('users.destroy');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('can:manage roles')
        ->name('roles.index');

    Route::get('/roles/create', [RoleController::class, 'create'])
        ->middleware('can:manage roles')
        ->name('roles.create');

    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('can:manage roles')
        ->name('roles.store');

    Route::get('/roles/{role}', [RoleController::class, 'show'])
        ->middleware('can:manage roles')
        ->name('roles.show');

    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('can:manage roles')
        ->name('roles.edit');

    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('can:manage roles')
        ->name('roles.update');

    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('can:manage roles')
        ->name('roles.destroy');

    // Cards
    Route::resource('cards', CardController::class)->except(['destroy', 'update']);

    Route::get('/cards/{card}/wizard/step-1', [CardWizardController::class, 'step1'])->name('cards.wizard.step1');
    Route::put('/cards/{card}/wizard/step-1', [CardWizardController::class, 'saveStep1'])->name('cards.wizard.step1.save');

    Route::get('/cards/{card}/wizard/step-2', [CardWizardController::class, 'step2'])->name('cards.wizard.step2');
    Route::put('/cards/{card}/wizard/step-2', [CardWizardController::class, 'saveStep2'])->name('cards.wizard.step2.save');

    Route::get('/cards/{card}/wizard/step-3', [CardWizardController::class, 'step3'])->name('cards.wizard.step3');
    Route::put('/cards/{card}/wizard/step-3', [CardWizardController::class, 'saveStep3'])->name('cards.wizard.step3.save');

    Route::get('/cards/{card}/wizard/step-4', [CardWizardController::class, 'step4'])->name('cards.wizard.step4');
    Route::put('/cards/{card}/wizard/step-4', [CardWizardController::class, 'saveStep4'])->name('cards.wizard.step4.save');

    Route::patch('/cards/{card}/publish', [CardWizardController::class, 'publish'])->name('cards.publish');
    Route::patch('/cards/{card}/pause', [CardWizardController::class, 'pause'])->name('cards.pause');

    Route::patch('/cards/{card}/autosave', [CardWizardController::class, 'autosave'])
        ->name('cards.autosave');
});
