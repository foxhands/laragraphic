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
        Schema::create('astro_data', function (Blueprint $table) {
        $table->id();
        $table->string('u_namez')->default('Отсутствует');
        $table->decimal('N', 8, 2)->default(50.26);
        $table->decimal('E', 8, 2)->default(30.32);
        $table->string('frm_autogmt')->default('on');
        $table->integer('dayz')->default(1);;
        $table->integer('monthz')->default(1);;
        $table->integer('yearz')->default(1901);;
        $table->integer('hourz')->default(12);;
        $table->integer('minutz')->default(00);;
        $table->integer('city')->default(593);;
        $table->string('sun')->default('yes');
        $table->string('moon')->default('yes');
        $table->string('mercury')->default('yes');
        $table->string('venus')->default('yes');
        $table->string('mars')->default('yes');
        $table->string('jupiter')->default('yes');
        $table->string('saturn')->default('yes');
        $table->string('uran')->default('yes');
        $table->string('neptun')->default('yes');
        $table->string('pluton')->default('yes');
        $table->string('node')->default('yes');
        $table->string('snode')->default('no');
        $table->string('lilit')->default('no');
        $table->string('selena')->default('no');
        $table->string('prozerpina')->default('no');
        $table->string('hiron')->default('no');
        $table->string('het1')->default('no');
        $table->string('sakoyan1')->default('yes');
        $table->string('globa1')->default('no');
        $table->string('podvodniy1')->default('no');
        $table->string('katrin1')->default('no');
        $table->string('mariya1')->default('no');
        $table->string('het2')->default('no');
        $table->string('podvodniy2')->default('no');
        $table->string('katrin2')->default('yes');
        $table->string('globa3')->default('yes');
        $table->string('het4')->default('no');
        $table->string('sakoyan4')->default('no');
        $table->string('podvodniy3')->default('no');
        $table->string('izraitel4')->default('yes');
        $table->string('ruler5')->default('yes');
        $table->string('tranz10')->default('yes');
        $table->string('solar12')->default('yes');
        $table->string('shulman14')->default('yes');
        $table->string('nazarova9')->default('yes');
        $table->string('nazarova10')->default('yes');
        $table->string('nazarova11')->default('yes');
        $table->string('sakoyan10')->default('no');
        $table->integer('conj1')->default(0);
        $table->integer('conj2')->default(5);
        $table->integer('sekst1')->default(52);
        $table->integer('sekst2')->default(68);
        $table->integer('kvad1')->default(82);
        $table->integer('kvad2')->default(98);
        $table->integer('trin1')->default(112);
        $table->integer('trin2')->default(128);
        $table->integer('opp1')->default(172);
        $table->integer('opp2')->default(180);
        $table->string('leto_zima')->default('y');
        $table->integer('sort_asp')->default(0);
        $table->integer('gamma')->default(1);
        $table->integer('house')->default(0);
        $table->integer('cat_id')->default(1);
        $table->string('ssid')->default('');
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
        //
    }
};
