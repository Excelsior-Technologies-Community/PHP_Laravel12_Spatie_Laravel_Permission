<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');

        if (Schema::hasTable($tableNames['model_has_roles'])) {
            Schema::table($tableNames['model_has_roles'], function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('model_type');
                }
            });
        }

        if (Schema::hasTable($tableNames['model_has_permissions'])) {
            Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
                if (!Schema::hasColumn($table->getTable(), 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('model_type');
                }
            });
        }
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (Schema::hasTable($tableNames['model_has_roles'])) {
            Schema::table($tableNames['model_has_roles'], function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'expires_at')) {
                    $table->dropColumn('expires_at');
                }
            });
        }

        if (Schema::hasTable($tableNames['model_has_permissions'])) {
            Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'expires_at')) {
                    $table->dropColumn('expires_at');
                }
            });
        }
    }
};