<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingToComicsTable extends Migration
{
    public function up()
    {
        Schema::table('comics', function (Blueprint $table) {
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('rating_count')->default(0);
        });
    }

    public function down()
    {
        Schema::table('comics', function (Blueprint $table) {
            $table->dropColumn(['rating', 'rating_count']);
        });
    }
}