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
        // Catálogo territorial: Comunas y Corregimientos
        Schema::create('comunas_corregimientos', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('tipo', ['COMUNA', 'CORREGIMIENTO'])->index();
            $table->integer('numero')->nullable()->index();
            $table->string('nombre', 100)->unique();
            $table->enum('zona', ['URBANA', 'RURAL']);
            $table->timestamp('created_at')->nullable();
        });

        // Catálogo territorial: Barrios de comunas y Veredas de corregimientos
        Schema::create('barrios_veredas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comuna_corregimiento_id')->index();
            $table->enum('tipo', ['BARRIO', 'VEREDA', 'SECTOR'])->default('BARRIO');
            $table->string('nombre', 150)->index();
            $table->string('codigo_dane', 20)->nullable();
            $table->decimal('latitud_referencia', 10, 8)->nullable();
            $table->decimal('longitud_referencia', 11, 8)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('comuna_corregimiento_id')
                ->references('id')
                ->on('comunas_corregimientos')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barrios_veredas');
        Schema::dropIfExists('comunas_corregimientos');
    }
};
