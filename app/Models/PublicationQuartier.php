<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublicationQuartier extends Model
{
    use HasFactory;

    protected $table = 'publications_quartier';

    protected $fillable = ['user_id', 'quartier_id', 'titre', 'contenu', 'type', 'statut', 'resolved_at'];

    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class);
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(CommentairePublication::class, 'publication_id');
    }

    public function confirmations(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'publication_confirmations', 'publication_id', 'user_id')->withPivot('created_at');
    }
}
