<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->json('breaks')->nullable(); // stores multiple breaks [{in:'time', out:'time'}
            $table->decimal('total_work_hours', 8, 2)->nullable();
            $table->decimal('total_break_hours', 8, 2)->nullable();
            $table->string('status')->default('checked_out'); // checked_in, on_break, checked_out
            $table->string('attendance_status')->nullable(); // Present, Absent
            $table->timestamps();
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
