<?php

namespace App\Enums;

enum ChannelStatus: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
