<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day'); // 1-31
            $table->tinyInteger('month'); // 1-12
            $table->smallInteger('year');
            $table->enum('category', ['Investasi', 'Tabungan', 'Tagihan', 'Pemasukan']);
            $table->string('description', 255);
            $table->bigInteger('amount');
            $table->tinyInteger('remind_before')->default(0); // 0=hari H, 1=H-1, 3=H-3, 7=H-7
            $table->timestamps();
            
            $table->index(['user_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_reminders');
    }
};
