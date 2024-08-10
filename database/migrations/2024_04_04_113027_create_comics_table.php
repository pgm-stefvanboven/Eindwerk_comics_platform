<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('comics', function (Blueprint $table) {
            $table->id();
            $table->string('poster');
            $table->string('thumbnail')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->number('total')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->enum('type', ['sale', 'rent']);
            $table->string('publisher');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comics');
    }
};
