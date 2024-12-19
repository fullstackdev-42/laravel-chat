<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('center_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('center_id');
            $table->unsignedInteger('user_id');
            $table->integer('role')->default(1);
            $table->timestamps();

            $table->foreign('center_id')
                ->references('id')->on('centers')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('center_users');
    }
};
