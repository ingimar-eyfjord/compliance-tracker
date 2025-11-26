<?php

namespace App\Enums;

enum ActorType: string
{
    case USER = 'user';
    case SYSTEM = 'system';
    case API = 'api';
    case REPORTER = 'reporter';
}
