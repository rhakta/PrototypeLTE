<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Num_Facture extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'Catégorie',
        'Fournisseur',
        'N°Facture',
        'Montant',
        'Chantier',
        'Date échéance'
    ];


}
