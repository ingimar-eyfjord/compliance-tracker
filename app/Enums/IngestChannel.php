<?php

namespace App\Enums;

enum IngestChannel: string
{
    case WEB = 'web';
    case EMAIL = 'email';
    case IMPORT = 'import';
    case API = 'api';
}
