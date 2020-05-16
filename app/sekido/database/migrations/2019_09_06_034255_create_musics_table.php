<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMusicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path')->unique();

            $table->string('album')->nullable();
            $table->string('artist')->nullable();
            $table->string('bitrate')->nullable();
            $table->string('cover')->nullable();
            $table->string('document')->nullable();
            $table->string('genre')->nullable();
            $table->string('originalArtist')->nullable();
            $table->string('playtime_seconds')->nullable();
            $table->string('related_works')->nullable();
            $table->string('title')->nullable();
            $table->string('year')->nullable();
            $table->timestamps();
            $table->tinyInteger('track_num')->nullable();

            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // 外部キー制約
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('musics');
    }
}
