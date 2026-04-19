<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usando statement bruto para garantir compatibilidade com ENUM no MySQL
        DB::statement("ALTER TABLE presentations MODIFY COLUMN status ENUM('EM_ANALISE', 'APTO', 'INAPTO', 'FINALIZADA') DEFAULT 'EM_ANALISE'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE presentations MODIFY COLUMN status ENUM('EM_ANALISE', 'APTO', 'INAPTO') DEFAULT 'EM_ANALISE'");
    }
};
