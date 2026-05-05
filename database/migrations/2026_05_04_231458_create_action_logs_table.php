<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('action_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');        //  login, logout, create_product...
            $table->string('model')->nullable();  //  Product, User
            $table->integer('model_id')->nullable(); // id du produit
            $table->json('details')->nullable();  // données supplémentaires
            $table->string('ip')->nullable();     // adresse IP
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_logs');
    }
};