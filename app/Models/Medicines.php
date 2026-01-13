<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Medicines extends Model
{
    //

    protected $keyType = 'string';
    public $incrementing = false;


    protected $fillable = [
        'name',
    ];

    // generate uuid
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}
