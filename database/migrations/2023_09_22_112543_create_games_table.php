<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->tinyInteger('status')->default(0)->index();
            $table->tinyInteger('active')->default(0)->index();
            $table->integer('width')->default(600);
            $table->integer('height')->default(800);
            $table->string('source_link')->nullable();
            $table->integer('author_id')->default(1);
            $table->integer('play_times')->default(0);
            $table->tinyInteger('is_hot')->default(0);
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('images')->nullable();
            $table->string('video')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
