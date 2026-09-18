<?php

use App\Http\Controllers\Admin\AuthorizationDashboardController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home & User Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware(['auth'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| User Impersonation Leave (Accessible by any authenticated impersonated session)
|--------------------------------------------------------------------------
*/

Route::post('/impersonate/leave', [ImpersonationController::class, 'leave'])
    ->middleware(['auth'])
    ->name('impersonate.leave');

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [AuthorizationDashboardController::class, 'index'])->name('dashboard');

    // User Impersonation
    Route::post('/impersonate/{user}', [ImpersonationController::class, 'impersonate'])->name('impersonate');

    // Users & Permissions Analyzer
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::get('/users/{user}/permissions-analyzer', [UserManagementController::class, 'permissionsAnalyzer'])->name('users.analyzer');
    Route::post('/users/{user}/test-permission', [UserManagementController::class, 'testPermission'])->name('users.test-permission');

    // Roles CRUD & Clone
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/bulk-delete', [RoleController::class, 'bulkDestroy'])->name('roles.bulk-delete');
    Route::post('/roles/{role}/clone', [RoleController::class, 'clone'])->name('roles.clone');

    // Permissions CRUD & 1-Click Generator
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::post('/permissions/bulk-delete', [PermissionController::class, 'bulkDestroy'])->name('permissions.bulk-delete');
    Route::post('/permissions/generate-crud', [PermissionController::class, 'generateCrud'])->name('permissions.generate-crud');

    // RBAC Schema JSON Export & Import
    Route::get('/schema/export-json', [PermissionController::class, 'exportJson'])->name('schema.export-json');
    Route::post('/schema/import-json', [PermissionController::class, 'importJson'])->name('schema.import-json');
});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';