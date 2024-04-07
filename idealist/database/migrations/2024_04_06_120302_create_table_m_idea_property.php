<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMIdeaProperty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mIdeaProperty', function (Blueprint $table) {
            $table->integer('mIdeaId');
            $table->integer('propetyId');
            $table->string('value')->default(null);
            $table->timestamps();

            $table->primary(['mIdeaId', 'propetyId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mIdeaProperty');
    }
}
