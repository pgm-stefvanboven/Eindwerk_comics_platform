<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComicNotesTable extends Migration
{
    public function up()
    {
        Schema::create('comic_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comic_id');
            $table->text('note');
            $table->timestamps();

            $table->foreign('comic_id')->references('id')->on('comics')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('comic_notes');
    }
}