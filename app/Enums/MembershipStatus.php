<?php

namespace App\Enums;

enum MembershipStatus: string
{
    case INVITED = 'invited';
    case ACTIVE = 'active';
    case DISABLED = 'disabled';

    public function label(): string
    {
        return match ($this) {
            self::INVITED => 'Invited',
            self::ACTIVE => 'Active',
            self::DISABLED => 'Disabled',
        };
    }
}
