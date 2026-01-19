<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoryMetadata extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'category_metadata';

    protected $fillable = [
        'id',
        'name',
        'category_id'
    ];

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function values(): HasMany {
        return $this->hasMany(MetadataValue::class);
    }
}
