<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'label',
        'description',
        'price',
        'sales_count',
        'views_count',
        'category_id'
    ];

    public function category(): BelongsTo 
    {
        return $this->belongsTo(Category::class);
    }

    public function images():HasMany 
    {
        return $this->hasMany(ProductImage::class);
    }

    public function price(): Attribute
    {
        return Attribute::make(
            set: fn ($price) => ($price * 100),
            get: fn ($price) => ($price / 100)
        );
    }

    public function getFormattedPriceAttribute()
    {
        return 'R$' . number_format($this->price, 2, ',', '.');
    }
}
