<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMIdea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mIdea', function (Blueprint $table) {
            $table->integer('userId');
            $table->integer('id');
            $table->integer('extendsId')->nullable();
            $table->string('name')->default(null);
            $table->text('contents')->default(null);
            $table->timestamps();

            $table->primary(['userId', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mIdea');
    }
}
