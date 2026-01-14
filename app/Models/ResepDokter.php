<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class ResepDokter extends Model
{

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'medical_record_id',
        'examined_at',
        'resep_dokter',
        'updated_at',
    ];

    protected $casts = [
        'examined_at' => 'datetime',
        'resep_dokter' => 'array',
    ];


    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    //generate uuid
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }
}
