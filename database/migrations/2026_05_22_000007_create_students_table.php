<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->string('district_id')->nullable()->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('middle_initial')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('blood_type', 10)->nullable();
            $table->string('qrcode')->unique();
            $table->string('course')->nullable();
            $table->string('year')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('emergency_person')->nullable();
            $table->string('emergency_relationship')->nullable();
            $table->string('emergency_number')->nullable();
            $table->text('emergency_address')->nullable();
            $table->string('student_signature')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
