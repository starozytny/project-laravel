<?php

namespace App\Enums;

enum UserRole: string
{
    case Developer = 'developer';
    case Admin = 'admin';
    case User = 'user';

    /**
     * Get the name of the role
     */
    public function label(): string
    {
        return match ($this) {
            self::Developer => 'Développeur',
            self::Admin => 'Administrateur',
            self::User => 'Utilisateur',
        };
    }

    /**
     * Get the roles this role inherits from directly
     *
     * @return array<int, self>
     */
    public function inheritedRoles(): array
    {
        return match ($this) {
            self::Developer => [self::Admin],
            self::Admin => [self::User],
            self::User => [],
        };
    }

    /**
     * Get all the roles inherited directly or through other roles
     *
     * @return array<int, self>
     */
    public function allInheritedRoles(): array
    {
        $inherited = [];

        foreach ($this->inheritedRoles() as $role) {
            $inherited = [...$inherited, $role, ...$role->allInheritedRoles()];
        }

        return array_values(array_unique($inherited, SORT_REGULAR));
    }

    /**
     * Get every role whose privileges include this role's, itself included
     *
     * @return array<int, self>
     */
    public function rolesGranting(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $candidate): bool => $candidate === $this || in_array($this, $candidate->allInheritedRoles(), true),
        ));
    }

    /**
     * Get array of roles options
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(fn (self $role): array => [
            'value' => $role->value,
            'label' => $role->label(),
        ], self::cases());
    }
}
