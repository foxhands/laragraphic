<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_req', function (Blueprint $table) {
            $table->id();
            $table->string('user_anon');
            $table->string('user_id');
            $table->string('male_name');
            $table->date('male_birthday');
            $table->string('male_birthplace');
            $table->string('female_name');
            $table->date('female_birthday');
            $table->string('female_birthplace');
            $table->string('form_id');
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
        Schema::dropIfExists('user_req');
    }
};
