<?php

namespace App\Enums;

enum CaseStatus: string
{
    case NEW = 'new';
    case TRIAGE = 'triage';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
}
