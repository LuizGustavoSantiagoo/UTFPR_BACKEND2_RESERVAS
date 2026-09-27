<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'schedule_id', 'date', 'status'])]
class Reservation extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => ReservationStatus::class,
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<PlaceSchedule, $this> */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(PlaceSchedule::class, 'schedule_id');
    }
}
