# PHP_Laravel12_Spatie_Laravel_Permission


##  Overview

This project demonstrates a **complete Role & Permission based access control system** built with **Laravel 12** using **Spatie Laravel Permission** and **Laravel Breeze authentication**.

It includes:

* Secure authentication (login/register)
* Role-based access control (Admin / User)
* Permission-based actions
* Separate admin dashboard
* Clean, scalable project structure

This setup is suitable for **real-world applications** such as admin panels, e-commerce systems, CMS platforms, and enterprise dashboards.

---

##  Features

*  Authentication using Laravel Breeze
*  Role management using Spatie (Admin, User)
*  Permission management (example: edit orders)
*  Middleware-protected routes
*  Blade directives for roles & permissions
*  Separate Admin Dashboard
*  Breeze-based UI layout
*  Laravel 12 middleware configuration
*  Production-ready folder structure

---

##  Folder Structure

```text
laravel12-spatie
├── app
│   ├── Http
│   │   └── Controllers
│   │       └── ProfileController.php
│   └── Models
│       └── User.php
│
├── bootstrap
│   └── app.php              # Middleware registration (Laravel 12)
│
├── config
│   └── permission.php       # Spatie permission config
│
├── database
│   ├── migrations
│   │   ├── create_roles_table.php
│   │   ├── create_permissions_table.php
│   │   ├── create_model_has_roles_table.php
│   │   ├── create_model_has_permissions_table.php
│   │   └── create_role_has_permissions_table.php
│
├── resources
│   ├── views
│   │   ├── admin
│   │   │   └── dashboard.blade.php   # Admin dashboard
│   │   ├── layouts
│   │   │   ├── app.blade.php         # User layout (Breeze)
│   │   │   └── navigation.blade.php
│   │   ├── dashboard.blade.php       # User dashboard
│   │   └── welcome.blade.php
│   │
│   ├── css
│   └── js
│
├── routes
│   ├── web.php               # Web + admin routes
│   └── auth.php              # Auth routes (Breeze)
│
├── .env
├── composer.json
├── package.json
└── README.md
```

---

## 1. Prerequisites

Make sure you have installed:

* PHP **8.2+**
* Composer
* Node.js & npm
* MySQL
* Laravel CLI (optional)

---

## 2. Create Laravel 12 Project

```bash
composer create-project laravel/laravel laravel12-spatie
```

Start server:

```bash
php artisan serve
```

Open browser:

```
http://127.0.0.1:8000
```

---

## 3. Database Configuration

Edit `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=permission
DB_USERNAME=root
DB_PASSWORD=
```

Create database in MySQL:

```sql
CREATE DATABASE permission;
```

Run default migrations:

```bash
php artisan migrate
```

---

## 4. Install Laravel Breeze (Authentication)

Laravel does **NOT** include login/register by default.

Install Breeze:

```bash
composer require laravel/breeze --dev
```

Install Breeze scaffolding:

```bash
php artisan breeze:install
```

Choose:

* Blade
* Dark mode (optional)

Run migrations & frontend build:

```bash
php artisan migrate

npm install

npm run dev
```

Now these routes exist:

* `/login`
* `/register`
* `/dashboard`

---

## 5. Install Spatie Laravel Permission

```bash
composer require spatie/laravel-permission
```

Publish config & migrations:

```bash
php artisan vendor:publish --provider="Spatie\\Permission\\PermissionServiceProvider"
```

Run migrations:

```bash
php artisan migrate
```

This creates tables:

* roles
* permissions
* model_has_roles
* model_has_permissions
* role_has_permissions

---

## 6. Configure User Model

**File:** `app/Models/User.php`

```php
<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Foundation\\Auth\\User as Authenticatable;
use Illuminate\\Notifications\\Notifiable;
use Spatie\\Permission\\Traits\\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

⚠️ This step is mandatory, otherwise `assignRole()` will not work.

---

## 7. Register Spatie Middleware (Laravel 12)

Laravel 12 does **not** have `Kernel.php`.

Edit: `bootstrap/app.php`

```php
<?php

use Illuminate\\Foundation\\Application;
use Illuminate\\Foundation\\Configuration\\Exceptions;
use Illuminate\\Foundation\\Configuration\\Middleware;
use Spatie\\Permission\\Middleware\\RoleMiddleware;
use Spatie\\Permission\\Middleware\\PermissionMiddleware;
use Spatie\\Permission\\Middleware\\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

Clear cache:

```bash
php artisan optimize:clear
php artisan permission:cache-reset
```

---

## 8. Create Roles & Permissions

Open Tinker:

```bash
php artisan tinker
```

```php
use Spatie\\Permission\\Models\\Role;
use Spatie\\Permission\\Models\\Permission;

Permission::create(['name' => 'edit orders']);

Role::create(['name' => 'admin']);
Role::create(['name' => 'user']);

Role::findByName('admin')->givePermissionTo('edit orders');
```

---

## 9. Create Admin User & Assign Role

In Tinker:

```php
use App\\Models\\User;

$user = User::create([
    'name' => 'Admin User',
    'email' => 'admin@gmail.com',
    'password' => bcrypt('password')
]);

$user->assignRole('admin');
```

Verify:

```php
$user->hasRole('admin'); // true
```

---

## 10. Routes Configuration

**File:** `routes/web.php`

```php
<?php

use Illuminate\\Support\\Facades\\Route;
use App\\Http\\Controllers\\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    });
});

Route::middleware(['auth', 'permission:edit orders'])->group(function () {
    Route::get('/orders/edit', function () {
        return 'Edit Orders Page';
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
```

---

## 11. Admin Dashboard View

Create folder:

```
resources/views/admin
```

Create file:

```
resources/views/admin/dashboard.blade.php
```

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">

                <h1 class="text-2xl font-bold mb-4">
                    Welcome Admin: {{ auth()->user()->name }}
                </h1>

                @role('admin')
                    <p class="text-green-700 font-semibold">
                        You have full admin access.
                    </p>
                @endrole

                <div class="mt-4">
                    <a href="/orders/edit" class="px-4 py-2 bg-blue-600 text-white rounded">
                        Manage Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## 12. Blade Directives (Spatie)

**File:** `resources/views/dashboard.blade.php`

```blade
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## 13. User Layout (Default Breeze)

**File:** `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>
```

---

## 14. Login & Test

Login:

```
http://127.0.0.1:8000/login
```

Admin credentials:

* Email: `admin@gmail.com`
* Password: `password`

  <img width="760" height="578" alt="Screenshot 2026-01-21 131222" src="https://github.com/user-attachments/assets/43033c00-9d22-4f3c-8c82-804e8c49b16d" />


Test URLs:

* `/dashboard` → normal dashboard

  <img width="1773" height="347" alt="Screenshot 2026-01-21 125024" src="https://github.com/user-attachments/assets/75afa4ca-5248-45e7-b01e-66c661f43042" />

* `/admin` → admin dashboard

  <img width="1730" height="502" alt="Screenshot 2026-01-21 124653" src="https://github.com/user-attachments/assets/dddc8e32-a260-428c-88f7-a27fa5e9c370" />

* `/orders/edit` → permission protected

  <img width="590" height="100" alt="Screenshot 2026-01-21 124706" src="https://github.com/user-attachments/assets/9741073d-ab39-4cb5-99a6-fc81b937dbea" />


---

##  Final Result

* Laravel 12 project
* Authentication via Breeze
* Role & Permission system using Spatie
* Admin-only routes
* Permission-based UI
* Production-ready structure
