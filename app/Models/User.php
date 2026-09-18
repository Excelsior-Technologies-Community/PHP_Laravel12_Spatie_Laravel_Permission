<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

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

    /**
     * Check and purge any roles or permissions whose expires_at has passed.
     */
    public function purgeExpiredRolesAndPermissions(): void
    {
        $tableNames = config('permission.table_names');

        // Check expired roles
        $expiredRoles = DB::table($tableNames['model_has_roles'])
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->pluck('role_id');

        if ($expiredRoles->isNotEmpty()) {
            DB::table($tableNames['model_has_roles'])
                ->where('model_id', $this->id)
                ->where('model_type', get_class($this))
                ->whereIn('role_id', $expiredRoles)
                ->delete();

            $this->forgetCachedPermissions();
        }

        // Check expired direct permissions
        $expiredPermissions = DB::table($tableNames['model_has_permissions'])
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->pluck('permission_id');

        if ($expiredPermissions->isNotEmpty()) {
            DB::table($tableNames['model_has_permissions'])
                ->where('model_id', $this->id)
                ->where('model_type', get_class($this))
                ->whereIn('permission_id', $expiredPermissions)
                ->delete();

            $this->forgetCachedPermissions();
        }
    }

    /**
     * Assign a role with an optional expiration timestamp.
     */
    public function assignRoleWithExpiry($role, ?Carbon $expiresAt = null): void
    {
        $this->syncRoles([$role]);

        $tableNames = config('permission.table_names');
        $roleModel = is_string($role) ? $this->getRoleNames() : null;

        DB::table($tableNames['model_has_roles'])
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->update(['expires_at' => $expiresAt]);

        $this->forgetCachedPermissions();
    }

    /**
     * Get the expiration date of the user's primary role.
     */
    public function getRoleExpiryAttribute(): ?Carbon
    {
        $tableNames = config('permission.table_names');

        $record = DB::table($tableNames['model_has_roles'])
            ->where('model_id', $this->id)
            ->where('model_type', get_class($this))
            ->whereNotNull('expires_at')
            ->first();

        return $record && $record->expires_at ? Carbon::parse($record->expires_at) : null;
    }

    /**
     * Check if the current user session is impersonated by an Admin.
     */
    public function isImpersonated(): bool
    {
        return session()->has('impersonator_id') && session('impersonator_id') != $this->id;
    }

    /**
     * Get the impersonating administrator instance.
     */
    public function getImpersonator(): ?User
    {
        if ($this->isImpersonated()) {
            return User::find(session('impersonator_id'));
        }
        return null;
    }
}