<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;



    // un cupcake peut avoir 1 et jusqu'à 3 catégories
    // quelle relation ??

    public function cupcakes(): BelongsToMany
    {
        return $this->belongsToMany(Cupcake::class, 'cupcakes_categories');
    }


}
