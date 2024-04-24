<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $fillable = [
        "section_name",
        "description",
        "created_by",
    ];

    public function product(){
        return $this->hasMany(\App\Models\Product::class);
    }

    public function invoice(){
        return $this->hasMany(\App\Models\Invoice::class);
    }
}
