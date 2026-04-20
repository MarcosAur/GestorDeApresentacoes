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
        Schema::table('user_documents', function (Blueprint $table) {
            $table->dropForeign('user_documents_user_id_foreign');
        });

        Schema::rename('user_documents', 'presentation_documents');
        
        Schema::table('presentation_documents', function (Blueprint $table) {
            $table->renameColumn('user_id', 'presentation_id');
            $table->foreign('presentation_id')->references('id')->on('presentations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presentation_documents', function (Blueprint $table) {
            $table->dropForeign(['presentation_id']);
            $table->renameColumn('presentation_id', 'user_id');
        });

        Schema::rename('presentation_documents', 'user_documents');

        Schema::table('user_documents', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
