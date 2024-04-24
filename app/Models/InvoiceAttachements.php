<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAttachements extends Model
{
    use HasFactory;
    protected $fillable = [
        "file_name",
        "invoice_number",
        "created_by",
        "invoice_id"
    ];

    public function invoice(){
        return $this->belongsTo(\App\Models\Invoice::class);
    }
}
