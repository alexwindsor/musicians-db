<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musician_id')->unique()->constrained()->onDelete('cascade');
            $table->text('text');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('profiles');
    }
};
