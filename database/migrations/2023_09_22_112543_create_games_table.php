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
            $table->string('slug');
            $table->tinyInteger('status')->default(0)->index();
            $table->integer('width')->default(1000);
            $table->integer('height')->default(1000);
            $table->string('source_link')->nullable();
            $table->integer('author_id')->default(1);
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('images')->nullable();
            $table->string('video')->nullable();
            $table->tinyInteger('hot')->default(0);
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
