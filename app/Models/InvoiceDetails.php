<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetails extends Model
{
    use HasFactory;
    protected $fillable =[
        'invoice_number',
        'invoice_id',
        'product',
        'section',
        'status',
        'value_status',
        'note',
        'user',
        'payment_date'
    ];

    public function invoice(){
        return $this->belongsTo(\App\Models\Invoice::class);
    }
}
