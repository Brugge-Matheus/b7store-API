<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetadataValue extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'metadata_values';

    protected $fillable = [
        'id',
        'label',
        'category_metadata_id'
    ];

    public function categoryMetadata(): BelongsTo {
        return $this->belongsTo(CategoryMetadata::class);
    }
}
