<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model
{
    //
    protected $table = 'transaction_history';
    public $timestamps = false;

    protected $fillable = [
        'batch',
        'date',
        'date_of_transaction',
        'location_id',
        'qty',
        'product_id',
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
