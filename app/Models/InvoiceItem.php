<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price',
        'amount'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2'
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->amount = $item->quantity * $item->unit_price;
            
            // Update the parent invoice's total amount
            if ($item->invoice) {
                $item->invoice->total_amount = $item->invoice->items()->sum('amount');
                $item->invoice->save();
            }
        });

        static::deleted(function ($item) {
            // Update the parent invoice's total amount when an item is deleted
            if ($item->invoice) {
                $item->invoice->total_amount = $item->invoice->items()->sum('amount');
                $item->invoice->save();
            }
        });
    }
}
