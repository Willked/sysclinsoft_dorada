<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table): void {
            $table->foreignId('departamento_id')
                ->nullable()
                ->after('direccion')
                ->constrained('departamentos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('municipio_id')
                ->nullable()
                ->after('departamento_id')
                ->constrained('municipios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index(['departamento_id', 'municipio_id'], 'pacientes_dep_mun_index');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table): void {
            $table->dropIndex('pacientes_dep_mun_index');
            $table->dropConstrainedForeignId('municipio_id');
            $table->dropConstrainedForeignId('departamento_id');
        });
    }
};
