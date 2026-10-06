<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Conseil extends Model { protected $fillable=['titre','description','categorie','niveau','actif']; protected function casts(): array { return ['actif'=>'boolean']; } public function equipements(): BelongsToMany { return $this->belongsToMany(Equipement::class)->withTimestamps(); } }
