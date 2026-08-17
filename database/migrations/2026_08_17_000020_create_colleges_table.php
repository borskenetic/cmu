<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('colleges', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('programs', function (Blueprint $table) {
            $column = $table->foreignId('college_id')->nullable();
            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $column->after('id');
            }
            $column->constrained('colleges')->nullOnDelete();
        });

        $now = now();
        $collegeNames = [
            'College of Agriculture',
            'College of Arts and Sciences',
            'College of Business and Management',
            'College of Education',
            'College of Engineering',
            'College of Forestry and Environmental Science',
            'College of Human Ecology',
            'College of Nursing',
            'College of Veterinary Medicine',
        ];

        foreach ($collegeNames as $name) {
            DB::table('colleges')->insert([
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (Schema::hasTable('programs')) {
            $collegeIds = DB::table('colleges')->pluck('id', 'name');
            $assignments = [
                'BSCS' => 'College of Arts and Sciences',
                'BSIT' => 'College of Arts and Sciences',
                'AB PHILO' => 'College of Arts and Sciences',
                'BSBIO' => 'College of Arts and Sciences',
                'BSED' => 'College of Education',
                'BSED- ENGLISH' => 'College of Education',
                'BSED-ENGLISH' => 'College of Education',
                'BSBA' => 'College of Business and Management',
                'BSA' => 'College of Business and Management',
            ];

            foreach ($assignments as $code => $collegeName) {
                if (! isset($collegeIds[$collegeName])) {
                    continue;
                }
                DB::table('programs')
                    ->where('program_code', $code)
                    ->update(['college_id' => $collegeIds[$collegeName]]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('college_id');
        });

        Schema::dropIfExists('colleges');
    }
};
