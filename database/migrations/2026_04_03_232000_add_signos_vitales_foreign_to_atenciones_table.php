<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atenciones', function (Blueprint $table) {
            $table->foreign('signos_vitales_id')
                ->references('id')
                ->on('signos_vitales')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('atenciones', function (Blueprint $table) {
            $table->dropForeign(['signos_vitales_id']);
        });
    }
};
