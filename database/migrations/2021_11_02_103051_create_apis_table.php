<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apis', function (Blueprint $table) {
            $table->id();
            $table->string('purpose', 100)->nullable();
            $table->string('app', 100)->nullable();
            $table->text('controller')->nullable();
            $table->string('method', 100)->nullable();
            $table->string('url', 100)->nullable();
            $table->string('body')->nullable();
            $table->string('output')->nullable();
        });
    }

         
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apis');
    }
}
