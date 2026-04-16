<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('atenciones', 'enfermero_id')) {
            Schema::table('atenciones', function (Blueprint $table): void {
                $table->foreignId('enfermero_id')
                    ->nullable()
                    ->after('conductor_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('atenciones', 'enfermero_id')) {
            Schema::table('atenciones', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('enfermero_id');
            });
        }
    }
};
