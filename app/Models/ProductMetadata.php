<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMetadata extends Model
{
    protected $table = 'product_metadata';

    protected $fillable = [
        'product_id',
        'category_metadata_id',
        'metadata_value_id'
    ];

    public function product(): BelongsTo  {
        return $this->belongsTo(Product::class);
    }

    public function categoryMetadata(): BelongsTo {
        return $this->belongsTo(CategoryMetadata::class);
    }

   public function metadataValue(): BelongsTo {
    return $this->belongsTo(MetadataValue::class);
   }
}
