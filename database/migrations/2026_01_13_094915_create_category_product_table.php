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
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();

            /* cette table fait le lien entre les categories et les produits 
            * chaque produit peut appartenir à plusieurs categories et chaque categorie peut contenir plusieurs produits
            *constrained : crée une clé étrangère qui référence la table associée qui verifie que le numéro d'identifiant existe dans la table référencée
            *onDelete('cascade') : si une catégorie ou un produit est supprimé, les enregistrements associés dans cette table seront également supprimés automatiquement
            */
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_product');
    }
};
