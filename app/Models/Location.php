<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    //
    protected $table   = 'location';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

}
