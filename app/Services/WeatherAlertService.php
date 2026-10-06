<?php
namespace App\Services; class WeatherAlertService { public function niveau(?float $temperature): string { return match(true){$temperature === null => 'Normal',$temperature < 35 => 'Normal',$temperature < 39 => 'Vigilance',$temperature < 42 => 'Alerte',default => 'Danger'}; } }
