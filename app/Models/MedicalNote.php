<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalNote extends Model
{
    protected $fillable = [
        'medical_record_id',
        'notes',
        'attachment_id',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
