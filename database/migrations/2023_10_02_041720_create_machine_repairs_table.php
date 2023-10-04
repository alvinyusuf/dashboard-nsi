<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('machine_repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mesin_id');
            $table->string('pic');
            $table->text('request');
            $table->text('bagian_rusak')->nullable();
            $table->text('sebab')->nullable();
            $table->text('analisa')->nullable();
            $table->text('aksi')->nullable();
            $table->text('sparepart')->nullable();
            $table->date('prl')->nullable(); //purchase request (request sparepart baru yang diperlukan)
            $table->string('po')->nullable(); //puchase order (jadi setelah request belum langsung di order)
            $table->date('kedatangan_prl')->nullable();
            $table->string('kedatangan_po')->nullable();
            $table->date('tgl_input')->nullable()->default(Carbon::now()->format('Y-m-d'));
            $table->dateTime('tgl_kerusakan')->nullable()->default(Carbon::now());
            $table->dateTime('tgl_finish')->nullable();
            $table->enum('status_mesin', ['OK Repair (Finish)', 'Waiting Repair', 'Waiting Sparepart', 'On Repair', 'Stop by Prod']);
            $table->enum('status_aktifitas', ['Running', 'Stop']);
            $table->text('deskripsi')->nullable();
            $table->dateTime('start_downtime');
            $table->string('current_downtime');
            $table->string('prod_downtime');
            $table->string('total_downtime');
            $table->string('monthly_downtime');
            $table->date('downtime_month');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_repairs');
    }
};
