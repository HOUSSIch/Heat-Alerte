<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avis extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'point_fraicheur_id', 'note', 'commentaire', 'statut'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pointFraicheur(): BelongsTo
    {
        return $this->belongsTo(PointFraicheur::class);
    }
}
