<?php

namespace App\Notifications;

use App\Models\AlerteMeteo;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WeatherVigilanceNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly AlerteMeteo $alerte) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->alerte->titre,
            'message' => 'Une température maximale de '.$this->alerte->temperature_max.'°C est prévue dans votre quartier.',
            'alerte_id' => $this->alerte->id,
            'quartier_id' => $this->alerte->quartier_id,
            'niveau' => $this->alerte->niveau,
            'date' => $this->alerte->date_debut?->toDateString(),
        ];
    }
}
