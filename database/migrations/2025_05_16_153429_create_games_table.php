<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id(); // Changed to standard primary key
            $table->string('name');
            $table->enum('device_type', ['PC', 'Mobile', 'Console']);
            $table->enum('game_type', [
                'FIGHTING', 'RPG', 'FPS', 'TBS', 'SPORT', 
                'ARCADE', 'RACING', 'MMORPG', 'TPS', 'STRATEGY'
            ])->default('RPG');
            $table->boolean('is_deleted')->default(false);
            $table->integer('active_events_count')->default(0);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('games');
    }
};