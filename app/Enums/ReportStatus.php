<?php

namespace App\Enums;

enum ReportStatus: string
{
    case NEW = 'new';
    case TRIAGED = 'triaged';
    case REJECTED = 'rejected';
}
