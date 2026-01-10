<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResepDokter extends Model
{
    protected $fillable = [
        'medical_record_id',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
