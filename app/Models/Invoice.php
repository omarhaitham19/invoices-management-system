<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Invoice extends Model
{
    use SoftDeletes;

    use HasFactory;
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'product',
        'amount_collection',
        'amount_commission',
        'discount',
        'value_VAT',
        'rate_VAT',
        'total',
        'status',
        'value_status',
        'note',
        'user',
        'section_id',
        'payment_date'
    ];

    public function section(){
        return $this->belongsTo(\App\Models\Section::class);
    }

    public function invoiceDetails(){
        return $this->hasOne(\App\Models\InvoiceDetails::class);
    }

    public function invoiceAttachments(){
        return $this->hasMany(\App\Models\InvoiceAttachements::class);
    }
}
