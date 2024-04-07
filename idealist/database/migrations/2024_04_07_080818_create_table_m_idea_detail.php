<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMIdeaDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mIdeaDetail', function (Blueprint $table) {
            $table->integer('mIdeaId');
            $table->integer('id');
            $table->string('name')->default(null);
            $table->timestamps();

            $table->primary(['mIdeaId', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mIdeaDetail');
    }
}
