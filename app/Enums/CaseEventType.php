<?php

namespace App\Enums;

enum CaseEventType: string
{
    case STATUS_CHANGED = 'status_changed';
    case ASSIGNED = 'assigned';
    case MESSAGE_SENT = 'message_sent';
    case ATTACHMENT_ADDED = 'attachment_added';
    case DEADLINE_CREATED = 'deadline_created';
    case DEADLINE_MET = 'deadline_met';
    case DEADLINE_BREACHED = 'deadline_breached';
}
