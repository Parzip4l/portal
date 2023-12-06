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
        Schema::create('maintenanceticket', function (Blueprint $table) {
            $table->id();
            $table->string('nomor');
            $table->string('tanggal');
            $table->text('permasalahan');
            $table->string('lokasi');
            $table->string('pengirim');
            $table->string('assign_to');
            $table->string('status');
            $table->string('kategori');
            $table->string('tanggal_mulai')->nullable();
            $table->string('tanggal_selesai')->nullable();
            $table->string('estimasi_waktu')->nullable();
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
        Schema::dropIfExists('ticket_models');
    }
};
