<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// Ajout du trait HasFactory qui assure la liaison avec l'usine de modèles
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{


    // utilisation du trait HasFactory
    use HasFactory;


        // ajout de fillable pour l'ecriture (temporaire)
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_quantity',
        'barcode',
        'supplier_id',
    ];
}
