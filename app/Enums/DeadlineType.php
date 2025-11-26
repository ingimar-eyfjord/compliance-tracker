<?php

namespace App\Enums;

enum DeadlineType: string
{
    case ACK = 'ack';
    case TRIAGE = 'triage';
    case ASSIGN = 'assign';
    case FEEDBACK = 'feedback';
}
