<?php
// database/migrations/xxxx_xx_xx_create_stations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id(); // This will be unsigned bigint auto_increment
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('station_name')->nullable();
            $table->string('station_code')->nullable();
            $table->string('station_head_name')->nullable();
            $table->string('station_head_phone')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('address')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('user_id');
            $table->index('station_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stations');
    }
};
