<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'photo',
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

    public function conseils(): BelongsToMany
    {
        return $this->belongsToMany(Conseil::class)->withTimestamps();
    }

    public function entretiens(): HasMany
    {
        return $this->hasMany(EntretienEquipement::class);
    }

    public function maintenanceStatus(): string
    {
        if ($this->entretiens->isEmpty()) {
            return 'aucun_entretien';
        }

        if ($this->entretiens->contains(fn (EntretienEquipement $entretien) => $entretien->reminderStatus() === 'overdue')) {
            return 'en_retard';
        }

        if ($this->entretiens->contains(fn (EntretienEquipement $entretien) => ! $entretien->date_realisee && $entretien->date_prevue->gte(today()))) {
            return 'prévu';
        }

        return 'à_jour';
    }

    public function nextMaintenance(): ?EntretienEquipement
    {
        return $this->entretiens
            ->filter(fn (EntretienEquipement $entretien) => ! $entretien->date_realisee && $entretien->date_prevue->gte(today()))
            ->sortBy('date_prevue')
            ->first();
    }

    public function lastMaintenance(): ?EntretienEquipement
    {
        return $this->entretiens
            ->filter(fn (EntretienEquipement $entretien) => $entretien->date_realisee)
            ->sortByDesc('date_realisee')
            ->first();
    }
}
