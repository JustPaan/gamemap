<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            
            // Add your custom pivot fields
            $table->timestamp('registered_at')->nullable();
            $table->boolean('attended')->default(false);

            // New fields for email and name
            $table->string('name')->nullable(); // Store participant's name
            $table->string('email')->nullable(); // Store participant's email
            
            $table->timestamps();
            
            // Add unique constraint to prevent duplicate registrations
            $table->unique(['user_id', 'event_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('participants');
    }
}