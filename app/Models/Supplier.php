<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Ajout du trait HasFactory qui assure la liaison avec l'usine de modèles
use Illuminate\Database\Eloquent\Factories\HasFactory; 
class Supplier extends Model
{
    // utilisation du trait HasFactory
    use HasFactory;


    // autoriser l'ecriture avec fillable ( temportaire)
    protected $fillable = [
        'name',
        'address',
        'email',
        'phone',
        'contact_com',
    ];
    // définition de la relation entre Supplier et Product par une fonction products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    }
