<?php

namespace App\Enums;

enum SenderType: string
{
    case AGENT = 'agent';
    case REPORTER = 'reporter';
    case SYSTEM = 'system';
}
