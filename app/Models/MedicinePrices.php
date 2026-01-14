<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MedicinePrices extends Model
{

    protected $keyType = 'string';
    public $incrementing = false;


    protected $fillable = [
        'name',
        'medicine_id',
        'unit_price',
        'start_date',
        'end_date',
    ];

    // generate uuid
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}
