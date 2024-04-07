<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMIdeaMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mIdeaMethod', function (Blueprint $table) {
            $table->integer('mIdeaId');
            $table->integer('methodId');
            $table->timestamps();

            $table->primary(['mIdeaId', 'methodId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mIdeaMethod');
    }
}
