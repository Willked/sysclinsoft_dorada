<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de EPS (Entidades Promotoras de Salud) para vínculo con pacientes y RIPS.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eps', function (Blueprint $table) {
            $table->id();
            /** Código de habilitación u otro identificador oficial cuando aplique */
            $table->string('codigo', 32)->unique();
            $table->string('nombre', 255);
            /** NIT sin guiones o con formato estándar que defina el negocio */
            $table->string('nit', 20)->nullable()->unique();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 32)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('logo', 255)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->string('contacto', 255)->nullable();
            $table->string('contacto_telefono', 32)->nullable();
            $table->string('contacto_email', 255)->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eps');
    }
};
