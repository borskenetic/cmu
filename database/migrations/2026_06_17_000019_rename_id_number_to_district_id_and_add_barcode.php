<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['students', 'pending_students'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (Schema::hasColumn($table, 'id_number') && ! Schema::hasColumn($table, 'district_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->renameColumn('id_number', 'district_id');
                });
            }

            if (! Schema::hasColumn($table, 'barcode')) {
                Schema::table($table, function (Blueprint $blueprint) use ($table) {
                    $after = Schema::hasColumn($table, 'district_id') ? 'district_id' : 'id';
                    $blueprint->string('barcode')->nullable()->unique()->after($after);
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['students', 'pending_students'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            if (Schema::hasColumn($table, 'barcode')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('barcode');
                });
            }

            if (Schema::hasColumn($table, 'district_id') && ! Schema::hasColumn($table, 'id_number')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->renameColumn('district_id', 'id_number');
                });
            }
        }
    }
};
