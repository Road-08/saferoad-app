<?php

namespace App\Enums;

enum ReportStatus: string
{
    case PENDING_VALIDATION = 'pending_validation';
    case NEEDS_REVISION = 'needs_revision';
    case FORWARDED_TO_SECURITY = 'forwarded_to_security';
    case HANDLED = 'handled';
    case REJECTED = 'rejected';
}
