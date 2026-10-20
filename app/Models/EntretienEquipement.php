<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntretienEquipement extends Model
{
    use HasFactory;

    protected $table = 'entretiens_equipements';

    protected $fillable = [
        'titre',
        'type',
        'description',
        'date_prevue',
        'date_realisee',
        'rappel_avant_jours',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date_prevue' => 'date',
            'date_realisee' => 'date',
            'rappel_avant_jours' => 'integer',
        ];
    }

    public function equipement(): BelongsTo
    {
        return $this->belongsTo(Equipement::class);
    }

    public function statut(): string
    {
        if ($this->date_realisee) {
            return 'réalisé';
        }

        return $this->date_prevue->lt(today()) ? 'en_retard' : 'prévu';
    }

    public function isRappelActif(): bool
    {
        return $this->shouldShowReminder();
    }

    public function reminderStatus(): ?string
    {
        if ($this->date_realisee) {
            return null;
        }

        if ($this->date_prevue->lt(today())) {
            return 'overdue';
        }

        if ($this->date_prevue->isToday()) {
            return 'today';
        }

        if ($this->date_prevue->isTomorrow()) {
            return 'tomorrow';
        }

        if ($this->rappel_avant_jours !== null
            && $this->date_prevue->diffInDays(today(), true) <= $this->rappel_avant_jours) {
            return 'upcoming';
        }

        return null;
    }

    public function shouldShowReminder(): bool
    {
        return $this->reminderStatus() !== null;
    }

    public function reminderPriority(): int
    {
        return match ($this->reminderStatus()) {
            'overdue' => 1,
            'today' => 2,
            'tomorrow' => 3,
            'upcoming' => 4,
            default => 99,
        };
    }

    public function reminderTitle(): string
    {
        return match ($this->reminderStatus()) {
            'overdue' => 'Entretien en retard',
            'today' => 'Entretien prévu aujourd’hui',
            'tomorrow' => 'Entretien demain',
            default => 'Rappel d’entretien',
        };
    }

    public function reminderDescription(): string
    {
        $days = $this->date_prevue->diffInDays(today(), true);

        return match ($this->reminderStatus()) {
            'overdue' => 'En retard depuis '.$days.' jour'.($days > 1 ? 's' : '').'.',
            'today' => 'Est prévu aujourd’hui.',
            'tomorrow' => 'Est prévu demain.',
            'upcoming' => 'Prévu dans '.$days.' jour'.($days > 1 ? 's' : '').'.',
            default => '',
        };
    }
}
