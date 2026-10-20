<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtablissementSensible extends Model
{
    protected $fillable = [
        'quartier_id',
        'nom',
        'type',
        'adresse',
        'telephone',
        'description',
        'horaires',
        'services',
        'accueil_public',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
            'accueil_public' => 'boolean',
        ];
    }

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class);
    }
}
