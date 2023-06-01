<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('detail_types', function (Blueprint $table) {
            $table->id();
            $table->string('detail_type_text', 48);
        });
    }


    public function down()
    {
        Schema::dropIfExists('detail_types');
    }
};
