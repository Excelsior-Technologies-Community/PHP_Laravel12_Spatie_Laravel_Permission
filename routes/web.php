<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home Route
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| User Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Dashboard
|--------------------------------------------------------------------------
|
| Only users with the admin role can access the main admin dashboard.
|
*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

});

/*
|--------------------------------------------------------------------------
| User Management
|--------------------------------------------------------------------------
|
| Access is controlled by permissions.
|
*/

Route::middleware(['auth', 'permission:users.view'])->group(function () {

    Route::get('/admin/users', [
        UserManagementController::class,
        'index'
    ])->name('admin.users.index');

});

Route::middleware(['auth', 'permission:users.edit'])->group(function () {

    Route::get('/admin/users/{user}/edit', [
        UserManagementController::class,
        'edit'
    ])->name('admin.users.edit');

    Route::put('/admin/users/{user}', [
        UserManagementController::class,
        'update'
    ])->name('admin.users.update');

});

/*
|--------------------------------------------------------------------------
| Role Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:roles.view'])->group(function () {

    Route::get('/admin/roles', [
        RoleController::class,
        'index'
    ])->name('admin.roles.index');

});

Route::middleware(['auth', 'permission:roles.create'])->group(function () {

    Route::get('/admin/roles/create', [
        RoleController::class,
        'create'
    ])->name('admin.roles.create');

    Route::post('/admin/roles', [
        RoleController::class,
        'store'
    ])->name('admin.roles.store');

});

Route::middleware(['auth', 'permission:roles.edit'])->group(function () {

    Route::get('/admin/roles/{role}/edit', [
        RoleController::class,
        'edit'
    ])->name('admin.roles.edit');

    Route::put('/admin/roles/{role}', [
        RoleController::class,
        'update'
    ])->name('admin.roles.update');

});

Route::delete('/admin/roles/{role}', [
    RoleController::class,
    'destroy'
])->middleware(['auth', 'permission:roles.delete'])
  ->name('admin.roles.destroy');

/*
|--------------------------------------------------------------------------
| Permission Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'permission:permissions.view'])->group(function () {

    Route::get('/admin/permissions', [
        PermissionController::class,
        'index'
    ])->name('admin.permissions.index');

});

Route::middleware(['auth', 'permission:permissions.create'])->group(function () {

    Route::get('/admin/permissions/create', [
        PermissionController::class,
        'create'
    ])->name('admin.permissions.create');

    Route::post('/admin/permissions', [
        PermissionController::class,
        'store'
    ])->name('admin.permissions.store');

});

Route::delete('/admin/permissions/{permission}', [
    PermissionController::class,
    'destroy'
])->middleware(['auth', 'permission:permissions.delete'])
  ->name('admin.permissions.destroy');

/*
|--------------------------------------------------------------------------
| Existing Permission Route
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'permission:edit orders'
])->group(function () {

    Route::get('/orders/edit', function () {
        return 'Edit Orders Page';
    })->name('orders.edit');

});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');

});

require __DIR__.'/auth.php';

