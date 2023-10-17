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
        // ini table maintenance dengan konsep memecah downtime berdasarkan status mesin
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
            $table->text('deskripsi')->nullable();
            $table->date('prl')->nullable(); //purchase request (request sparepart baru yang diperlukan)
            $table->string('po')->nullable(); //puchase order (jadi setelah request belum langsung di order)
            $table->date('kedatangan_prl')->nullable();
            $table->string('kedatangan_po')->nullable();
            $table->dateTime('tgl_kerusakan')->nullable()->default(Carbon::now());
            $table->date('tgl_input')->nullable()->default(Carbon::now()->format('Y-m-d'));
            $table->dateTime('tgl_finish')->nullable();
            $table->enum('status_mesin', ['OK Repair (Finish)', 'Waiting Repair', 'Waiting Sparepart', 'On Repair']);
            $table->enum('status_aktifitas', ['Running', 'Stop']);
            $table->boolean('stop_by_production')->default(false);
            $table->dateTime('start_downtime');
            $table->dateTime('start_monthly_downtime');
            $table->string('current_downtime')->default('0:0:0:0');
            $table->string('prod_waiting_repair_dt')->default('0:0:0:0');
            $table->string('prod_waiting_sparepart_dt')->default('0:0:0:0');
            $table->string('prod_on_repair_dt')->default('0:0:0:0');
            $table->string('mtc_waiting_repair_dt')->default('0:0:0:0');
            $table->string('mtc_waiting_sparepart_dt')->default('0:0:0:0');
            $table->string('mtc_on_repair_dt')->default('0:0:0:0');
            $table->string('current_monthly_downtime')->default('0:0:0:0');
            $table->string('total_monthly_downtime')->default('0:0:0:0');
            $table->date('downtime_month')->default(Carbon::now()->format('Y-m-d'));
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
