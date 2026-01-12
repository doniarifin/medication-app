<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MedicalAttachment extends Model
{

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'medical_record_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
