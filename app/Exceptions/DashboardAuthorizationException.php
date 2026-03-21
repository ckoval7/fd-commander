<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Exception thrown when a user attempts to perform an unauthorized
 * action on a dashboard they do not own.
 */
class DashboardAuthorizationException extends RuntimeException
{
    //
}
