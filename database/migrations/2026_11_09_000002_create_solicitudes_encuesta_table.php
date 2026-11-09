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
        Schema::create('solicitudes_encuesta', function (Blueprint $table) {
            $table->increments('id');
            $table->string('radicado_codigo', 30)->unique();
            $table->string('nombres_solicitante', 100);
            $table->string('apellidos_solicitante', 100);
            $table->string('celular', 20);
            $table->string('direccion', 255);
            $table->unsignedInteger('barrio_id')->index();
            $table->unsignedInteger('comuna_corregimiento_id')->index();
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->unsignedInteger('punto_atencion_id')->index();
            $table->unsignedInteger('usuario_ventanilla_id')->index();
            $table->dateTime('fecha_radicacion')->index();
            $table->date('fecha_limite')->index(); // Política institucional: +15 días calendario
            $table->enum('estado', ['RADICADA', 'EN_RUTA', 'ASIGNADA', 'VISITADA', 'ANULADA'])->default('RADICADA')->index();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('barrio_id')->references('id')->on('barrios_veredas')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('comuna_corregimiento_id')->references('id')->on('comunas_corregimientos')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('punto_atencion_id')->references('id')->on('puntos_atencion')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('usuario_ventanilla_id')->references('id')->on('usuarios')->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_encuesta');
    }
};
