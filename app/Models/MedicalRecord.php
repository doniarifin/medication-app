<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $table = 'medical_records';

    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'patient_name',
        'examined_at',
        'medicine_price',
        'is_paid',
        'updated_at',
    ];

    protected $casts = [
        'examined_at' => 'datetime',
        'is_paid' => 'boolean',
    ];

    public function vitalSign()
    {
        return $this->hasOne(VitalSign::class);
    }

    public function note()
    {
        return $this->hasOne(MedicalNote::class);
    }

    public function attachments()
    {
        return $this->hasMany(MedicalAttachment::class);
    }

    public function resepDokter()
    {
        return $this->hasMany(ResepDokter::class);
    }
}
