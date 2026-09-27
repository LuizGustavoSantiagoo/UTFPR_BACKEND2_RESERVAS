<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['nome', 'bloco', 'capacidade', 'descricao'])]
class Place extends Model
{
    /** @return HasMany<PlaceSchedule, $this> */
    public function schedules(): HasMany
    {
        return $this->hasMany(PlaceSchedule::class);
    }

    /** @return HasManyThrough<Reservation, PlaceSchedule, $this> */
    public function reservations(): HasManyThrough
    {
        return $this->hasManyThrough(Reservation::class, PlaceSchedule::class, 'place_id', 'schedule_id');
    }
}
