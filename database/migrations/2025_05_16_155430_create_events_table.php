<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_participants');
            $table->decimal('total_fee', 8, 2)->default(0);
            $table->string('location_name');
            $table->decimal('location_lat', 10, 8);
            $table->decimal('location_lng', 11, 8);
            $table->boolean('is_approved')->default(false);
            $table->foreignId('organizer_id')->constrained('users');
            $table->foreignId('game_id')->constrained();
            $table->integer('participants_count')->default(0);
            $table->string('image_path')->nullable();
            $table->string('device_type');
            $table->string('game_type');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}