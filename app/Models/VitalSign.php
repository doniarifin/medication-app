<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VitalSign extends Model
{
    protected $fillable = [
        'medical_record_id',
        'height',
        'weight',
        'systole',
        'diastole',
        'heart_rate',
        'respiration_rate',
        'body_temperature',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
