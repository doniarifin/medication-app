<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Doctor = 'doctor';
    case Pharmacist = 'pharmacist';
    case User = 'user';
}
