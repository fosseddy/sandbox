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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string("name", 200);
            $table->datetime("date");
            $table
                ->foreignId("category_id")
                ->constrained()
                ->onDelete("cascade");
            $table->integer("duration")->default(0);
            $table->string("location", 300)->default("");
            $table->string("organizer", 250)->default("");
            $table->string("image", 50)->default("");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
