<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'zipcode',
        'street',
        'number',
        'city',
        'state',
        'country',
        'complement',
    ];

    protected function casts(): array
    {
        return [
            'zipcode' => 'string'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
