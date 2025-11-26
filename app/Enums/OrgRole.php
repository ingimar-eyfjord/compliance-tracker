<?php

namespace App\Enums;

enum OrgRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case AGENT = 'agent';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Owner',
            self::ADMIN => 'Admin',
            self::MANAGER => 'Manager',
            self::AGENT => 'Agent',
            self::VIEWER => 'Viewer',
        };
    }
}
