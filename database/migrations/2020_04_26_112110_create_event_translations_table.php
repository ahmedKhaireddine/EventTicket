<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('additionel_information')->nullable();
            $table->integer('event_id')->unsigned()->nullable();
            $table->json('event_program')->nullable();
            $table->string('locale')->index();
            $table->string('subtitle');
            $table->string('title');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['event_id','locale']);
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_translations');
    }
}
