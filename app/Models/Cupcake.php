<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cupcake extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price_in_cents',
        'photo_url',
        'description',
        'quantity',
        'is_available',
        'is_advertised'
    ];


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'cupcakes_categories');
    }


}
