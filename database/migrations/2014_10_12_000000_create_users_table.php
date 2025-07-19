<?php

use App\Models\Pays;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('numerotelephone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignIdFor(Pays::class)->nullable();
            $table->string('ville')->nullable();
            $table->enum('role', ['eleveur', 'veterinaire']);
            $table->string('experience')->nullable();
            $table->string('type_elevage')->nullable();
            $table->integer('quantite')->nullable();
            $table->string('localisation')->nullable();
            $table->integer('surface_m2')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
