<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockProduct extends Model
{
    //
    protected $table   = 'stock_products';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'stock',
        'date_of_entry',
        'location_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
