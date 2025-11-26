<?php

namespace App\Enums;

enum AuditEvent: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case STATUS_CHANGED = 'status_changed';
    case ASSIGNED = 'assigned';
    case MESSAGE_SENT = 'message_sent';
    case ATTACHMENT_ADDED = 'attachment_added';
}
