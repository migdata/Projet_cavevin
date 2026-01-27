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
        'stock',
        'type',
        'barcode',
        'supplier_id',
    ];

    // définition de la relation avec le modèle Supplier (fournisseur)

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo // ajout d'un docblock pour indiquer le type de retour
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }               
}
