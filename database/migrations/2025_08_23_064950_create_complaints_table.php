<?php
// database/migrations/xxxx_xx_xx_create_complaints_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id(); // unsigned bigint auto_increment
            $table->unsignedBigInteger('user_id')->nullable(); // User who created complaint
            $table->unsignedBigInteger('station_id')->nullable(); // Station handling complaint
            $table->integer('fir_number')->nullable();
            $table->string('complainant_name')->nullable();
            $table->text('fir_description')->nullable();
            $table->text('user_description')->nullable();
            $table->string('police_station_name')->nullable();
            $table->string('officer_name')->nullable();
            $table->string('police_station_number')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'closed', 'rejected'])->default('pending');
            $table->date('fir_date')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('station_id')->references('id')->on('stations')->onDelete('cascade');

            // Indexes
            $table->index('user_id');
            $table->index('station_id');
            $table->index('fir_number');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('complaints');
    }
};
