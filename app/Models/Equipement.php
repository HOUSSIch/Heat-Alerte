<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipement extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'type',
        'marque',
        'sensible_chaleur',
        'sensible_coupure',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'sensible_chaleur' => 'boolean',
            'sensible_coupure' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function conseils(): BelongsToMany { return $this->belongsToMany(Conseil::class)->withTimestamps(); }
}
