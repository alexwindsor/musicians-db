<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('musician_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musician_id')->constrained()->onDelete('cascade');
            $table->foreignId('detail_types_id');
            $table->string('musician_details_text', 255);
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('musician_details');
    }
};
