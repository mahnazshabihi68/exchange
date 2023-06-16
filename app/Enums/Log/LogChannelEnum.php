<?php

namespace App\Enums\Log;

enum LogChannelEnum: string
{
    case STACK = 'stack';
    case SINGLE = 'single';
    case DAILY = 'daily';
    case SLACK = 'slack';
    case PAPERTRAIL = 'papertrail';
    case STDERR = 'stderr';
    case SYSLOG = 'syslog';
    case ERROR_LOG = 'errorlog';
    case EMERGENCY = 'emergency';
    case FILE = 'file';
    case FILE_ASYNC = 'file-async';
    case MYSQL = 'mysql';
    case MONGODB = 'mongo';
    case CONSOLE = 'console';
    case APPLICATION = 'application';
}
