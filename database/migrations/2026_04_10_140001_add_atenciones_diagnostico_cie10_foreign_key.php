<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atenciones', function (Blueprint $table) {
            $table->foreign('diagnostico_id')
                ->references('id')
                ->on('cie_10')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('atenciones', function (Blueprint $table) {
            $table->dropForeign(['diagnostico_id']);
        });
    }
};
