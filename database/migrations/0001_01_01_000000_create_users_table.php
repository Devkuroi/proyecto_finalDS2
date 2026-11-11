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
        // 1. Roles institucionales
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 50)->unique();
            $table->timestamps();
        });

        // 2. Puntos de atención oficiales en Cali
        Schema::create('puntos_atencion', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 120);
            $table->string('codigo', 30)->unique();
            $table->integer('comuna_numero')->index();
            $table->string('direccion', 255);
            $table->string('telefono', 50)->nullable();
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->boolean('es_principal')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 3. Usuarios del sistema
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 120);
            $table->string('email', 150)->unique()->index();
            $table->string('password', 255);
            $table->unsignedInteger('rol_id')->index();
            $table->unsignedInteger('punto_atencion_id')->nullable()->index();
            $table->string('identificacion', 30);
            $table->string('telefono', 30)->nullable();
            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('rol_id')->references('id')->on('roles')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreign('punto_atencion_id')->references('id')->on('puntos_atencion')->nullOnDelete()->cascadeOnUpdate();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('puntos_atencion');
        Schema::dropIfExists('roles');
    }
};
