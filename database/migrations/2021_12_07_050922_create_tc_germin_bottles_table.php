<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTcGerminBottlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tc_germin_bottles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tc_init_id');
            $table->bigInteger('tc_embryo_transfer_id')->nullable();
            $table->bigInteger('tc_matur_transfer_id')->nullable();
            $table->bigInteger('tc_germin_transfer_id')->nullable();
            $table->bigInteger('tc_worker_id');
            $table->bigInteger('tc_laminar_id');
            $table->bigInteger('tc_bottle_id');
            $table->tinyInteger('sub');
            $table->string('type')->default('Solid');
            $table->char('alpha',2);
            $table->integer('bottle_count');
            $table->dateTime('bottle_date');
            $table->tinyInteger('status')->default(1);
            $table->text('desc')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('tc_germin_bottles');
    }
}
