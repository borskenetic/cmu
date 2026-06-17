<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['students', 'pending_students'] as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $schoolIdColumn = $this->ensureSchoolIdColumn($table);

            if (Schema::hasColumn($table, 'student_id')) {
                DB::table($table)
                    ->whereNotNull('student_id')
                    ->where(function ($q) use ($schoolIdColumn) {
                        $q->whereNull($schoolIdColumn)->orWhere($schoolIdColumn, '');
                    })
                    ->orderBy('id')
                    ->chunkById(100, function ($rows) use ($table, $schoolIdColumn) {
                        foreach ($rows as $row) {
                            DB::table($table)->where('id', $row->id)->update([
                                $schoolIdColumn => $row->student_id,
                            ]);
                        }
                    });

                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('student_id');
                });
            }

            if ($table === 'pending_students' && Schema::hasColumn($table, 'qrcode')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropColumn('qrcode');
                });
            }
        }

        $this->alignPendingStudentsColumns();
    }

    public function down(): void
    {
        $schoolIdColumn = Schema::hasTable('students')
            ? $this->resolveSchoolIdColumn('students')
            : 'district_id';

        if (Schema::hasTable('students') && ! Schema::hasColumn('students', 'student_id')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('student_id')->nullable()->after('role_id');
            });
            DB::table('students')->update(['student_id' => DB::raw($schoolIdColumn)]);
        }

        if (Schema::hasTable('pending_students') && ! Schema::hasColumn('pending_students', 'student_id')) {
            Schema::table('pending_students', function (Blueprint $table) {
                $table->string('student_id')->nullable()->after('id');
                $table->string('qrcode')->nullable();
            });
            DB::table('pending_students')->update(['student_id' => DB::raw($schoolIdColumn)]);
        }
    }

    private function ensureSchoolIdColumn(string $table): string
    {
        $column = $this->resolveSchoolIdColumn($table);
        if ($column) {
            return $column;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($table) {
            $after = $table === 'students' ? 'role_id' : 'id';

            if ($table === 'students') {
                $blueprint->string('district_id')->nullable()->unique()->after($after);
            } else {
                $blueprint->string('district_id')->nullable()->after($after);
            }
        });

        return 'district_id';
    }

    private function resolveSchoolIdColumn(string $table): ?string
    {
        if (Schema::hasColumn($table, 'district_id')) {
            return 'district_id';
        }

        if (Schema::hasColumn($table, 'id_number')) {
            return 'id_number';
        }

        return null;
    }

    private function alignPendingStudentsColumns(): void
    {
        if (! Schema::hasTable('pending_students')) {
            return;
        }

        $schoolIdColumn = $this->ensureSchoolIdColumn('pending_students');

        Schema::table('pending_students', function (Blueprint $table) use ($schoolIdColumn) {
            $columns = [
                $schoolIdColumn => fn () => $table->string($schoolIdColumn)->nullable()->after('id'),
                'middle_initial' => fn () => $table->string('middle_initial')->nullable()->after('lastname'),
                'birth_date' => fn () => $table->date('birth_date')->nullable(),
                'blood_type' => fn () => $table->string('blood_type', 10)->nullable(),
                'emergency_person' => fn () => $table->string('emergency_person')->nullable(),
                'emergency_relationship' => fn () => $table->string('emergency_relationship')->nullable(),
                'emergency_number' => fn () => $table->string('emergency_number')->nullable(),
                'emergency_address' => fn () => $table->text('emergency_address')->nullable(),
                'student_signature' => fn () => $table->string('student_signature')->nullable(),
                'address' => fn () => $table->text('address')->nullable(),
            ];

            foreach ($columns as $name => $add) {
                if (! Schema::hasColumn('pending_students', $name)) {
                    $add();
                }
            }
        });
    }
};
