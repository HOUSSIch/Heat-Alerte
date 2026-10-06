<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointFraicheur extends Model
{
    use HasFactory;

    protected $table = 'points_fraicheur';

    protected $fillable = [
        'quartier_id', 'nom', 'type', 'adresse', 'latitude', 'longitude',
        'description', 'horaires', 'telephone', 'climatise', 'eau_disponible',
        'accessible_pmr', 'actif', 'source', 'geoapify_place_id',
    ];

    protected function casts(): array
    {
        return [
            'climatise' => 'boolean',
            'eau_disponible' => 'boolean',
            'accessible_pmr' => 'boolean',
            'actif' => 'boolean',
        ];
    }

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class);
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class);
    }
}
