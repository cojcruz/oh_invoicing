<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'employee_name',
        'employee_id',
        'total_amount',
        'invoice_date'
    ];

    protected $dates = [
        'invoice_date'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    protected static function booted()
    {
        static::creating(function ($invoice) {
            $lastInvoice = static::latest()->first();
            $invoiceNumber = $lastInvoice ? ((int) explode('_', $lastInvoice->invoice_id)[1] + 1) : 1;
            $date = now()->format('mdY');
            $invoice->invoice_id = "{$date}_{$invoiceNumber}_{$invoice->employee_id}";
        });
    }
}
