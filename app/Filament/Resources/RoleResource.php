<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as ShieldRoleResource;

class RoleResource extends ShieldRoleResource
{
    public static function getNavigationGroup(): ?string
    {
        return 'Seguridad';
    }
}
