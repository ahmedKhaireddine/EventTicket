<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class  RemoveAllColumnsToTranslateInEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['additionel_information', 'event_program', 'subtitle', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('additionel_information')->nullable();
            $table->json('event_program')->nullable();
            $table->string('subtitle');
            $table->string('title');
        });
    }
}
