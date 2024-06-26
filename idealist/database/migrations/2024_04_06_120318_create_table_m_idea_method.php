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
            $table->integer('userId');
            $table->integer('mIdeaId');
            $table->integer('methodId');
            $table->string('name');
            $table->timestamps();

            $table->primary(['userId', 'mIdeaId', 'methodId']);
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
