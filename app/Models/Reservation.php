<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'restaurant_table_id',
        'customer_id',
        'reservation_no',
        'customer_name',
        'customer_phone',
        'customer_email',
        'reservation_date',
        'guest_count',
        'status',
        'special_requests',
        'notes',
        'confirmed_at',
        'seated_at',
        'completed_at',
    ];

    protected $casts = [
        'reservation_date' => 'datetime',
        'guest_count' => 'integer',
        'confirmed_at' => 'datetime',
        'seated_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function updateStatus(string $status): void
    {
        $this->update([
            'status' => $status,
            $status.'_at' => now(),
        ]);
    }

    public static function generateReservationNo(): string
    {
        $date = now()->format('Ymd');
        $lastReservation = self::where('reservation_no', 'like', "RES-{$date}-%")
            ->orderBy('reservation_no', 'desc')
            ->first();

        $seq = 1;
        if ($lastReservation) {
            $parts = explode('-', $lastReservation->reservation_no);
            $seq = (int) end($parts) + 1;
        }

        return 'RES-'.$date.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
