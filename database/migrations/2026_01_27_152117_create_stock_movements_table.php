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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();

            // le produit supprimé se vooit supprimé son historique 
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // la quantité 
            $table->integer('quantity');

            // le type de mouvement 
            $table->string('type')->default('correction');
            // la date et l'heure 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
