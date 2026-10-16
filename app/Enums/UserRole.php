<?php

namespace App\Enums;

enum UserRole: string
{
    case Developer = 'developer';
    case Admin = 'admin';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Developer => 'Développeur',
            self::Admin => 'Administrateur',
            self::User => 'Utilisateur',
        };
    }
}
