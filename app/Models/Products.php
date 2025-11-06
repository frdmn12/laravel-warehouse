<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table   = 'products';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'product_code',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];
}
