<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory;
    const INITIAL_BALANCE=1000;
    protected $fillable = [
        'balance'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasBalance(float $value): bool
    {
        return ($this->balance - $value) >= 0;
    }

    protected function balance(): Attribute
    {
        return Attribute::make(
            set: function (float $value) {
                return [
                    'last_balance' => $this->balance??0,
                    'balance' => $value
                ];
            }
        );
    }
}
