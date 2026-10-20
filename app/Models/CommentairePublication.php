<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentairePublication extends Model
{
    use HasFactory;

    protected $table = 'commentaires_publication';

    protected $fillable = ['publication_id', 'user_id', 'contenu'];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(PublicationQuartier::class, 'publication_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
