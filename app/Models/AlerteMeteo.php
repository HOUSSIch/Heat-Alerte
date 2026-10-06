<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AlerteMeteo extends Model { protected $fillable=['quartier_id','titre','description','temperature','temperature_max','temperature_min','niveau','source','date_debut','date_fin','actif']; protected function casts():array{return ['actif'=>'boolean','date_debut'=>'datetime','date_fin'=>'datetime'];} public function quartier():BelongsTo{return $this->belongsTo(Quartier::class);} }
