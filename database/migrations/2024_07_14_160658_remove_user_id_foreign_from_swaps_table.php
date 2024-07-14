<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUserIdForeignFromSwapsTable extends Migration
{
    public function up()
    {
        Schema::table('swaps', function (Blueprint $table) {
            // Remove the foreign key constraint
            $table->dropForeign(['user_id']);
            // Remove the user_id column
            $table->dropColumn('user_id');
        });
    }

    public function down()
    {
        Schema::table('swaps', function (Blueprint $table) {
            // Add the user_id column again
            $table->unsignedBigInteger('user_id')->nullable();
            // Fix the foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}