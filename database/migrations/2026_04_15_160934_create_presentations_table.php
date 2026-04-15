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
        Schema::create('presentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contest_id')->constrained()->onDelete('cascade');
            $table->foreignId('competitor_id')->constrained('users')->onDelete('cascade');
            $table->string('work_title');
            $table->enum('status', ['EM_ANALISE', 'APTO', 'INAPTO'])->default('EM_ANALISE');
            $table->text('justification_inapto')->nullable();
            $table->string('qr_code_hash')->unique()->nullable();
            $table->boolean('checkin_realizado')->default(false);
            $table->integer('presentation_order')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentations');
    }
};
