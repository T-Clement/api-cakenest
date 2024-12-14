<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cart extends Model
{
    /** @use HasFactory<\Database\Factories\CartFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'total', "user_id",
    ];

    public function user() :BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function cupcakes() :BelongsToMany {
        return $this->belongsToMany(Cupcake::class)->withPivot("quantity");
    }
}
