<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CardController;
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
    Route::get('/cards', [CardController::class, 'index'])
        ->name('cards.index');

    Route::get('/cards/create', [CardController::class, 'create'])
        ->name('cards.create');

    Route::post('/cards/store-draft', [CardController::class, 'storeDraft'])
        ->name('cards.store-draft');

    Route::get('/cards/{card}/edit', [CardController::class, 'edit'])
        ->name('cards.edit');
});
