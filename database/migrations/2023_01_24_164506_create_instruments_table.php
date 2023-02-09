<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstrumentsTable extends Migration
{

    public function up()
    {
        Schema::create('instruments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique();
            $table->index(['name']);
        });

        Schema::create('instrument_musician', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instrument_id')->constrained()->onDelete('cascade');
            $table->foreignId('musician_id')->constrained()->onDelete('cascade');

            $table->unique(['instrument_id', 'musician_id']);

        });


    }


    public function down()
    {
        Schema::dropIfExists('instruments');
        Schema::dropIfExists('instrument_musician');
    }
}

