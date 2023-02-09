<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMusiciansTable extends Migration
{

    public function up()
    {
        Schema::create('musicians', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 32);
            $table->string('last_name', 32);
            $table->timestamps();
            $table->index(['first_name', 'last_name']);
        });
    }


    public function down()
    {
        Schema::dropIfExists('musicians');
    }
}
