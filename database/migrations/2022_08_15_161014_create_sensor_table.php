<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Channel;
use App\Models\Hardware;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit');
            $table->unsignedBigInteger('id_node')->nullable();
            $table->foreign('id_node')->references('id')->on('nodes');
            $table->unsignedBigInteger('id_hardware')->nullable();
            $table->foreign('id_hardware')->references('id')->on('hardwares');
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
        Schema::dropIfExists('sensors');
    }
};
