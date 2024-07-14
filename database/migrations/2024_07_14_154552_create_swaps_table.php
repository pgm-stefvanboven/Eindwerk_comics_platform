<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSwapsTable extends Migration
{
    public function up()
    {
        Schema::create('swaps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('comic_id');
            $table->unsignedBigInteger('requested_comic_id');
            $table->timestamps();

            $table->foreign('comic_id')->references('id')->on('comics')->onDelete('cascade');
            $table->foreign('requested_comic_id')->references('id')->on('comics')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('swaps');
    }
}