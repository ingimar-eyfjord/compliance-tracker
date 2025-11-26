<?php

namespace App\Enums;

enum DeadlineStatus: string
{
    case OPEN = 'open';
    case MET = 'met';
    case BREACHED = 'breached';
}
