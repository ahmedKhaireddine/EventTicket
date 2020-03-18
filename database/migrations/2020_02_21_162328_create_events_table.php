<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('additionel_information')->nullable();
            $table->date('start_date');
            $table->boolean('is_active')->default(0);
            $table->date('publish_at')->nullable();
            $table->string('picture')->nullable();
            $table->json('event_program')->nullable();
            $table->string('subtitle');
            $table->time('start_time')->nullable();
            $table->date('end_date')->nullable();
            $table->string('title');
            $table->softDeletes();
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
        Schema::dropIfExists('events');
    }
}
