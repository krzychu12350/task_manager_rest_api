<?php

namespace App\Services;

use App\Traits\Throwable;

/**
 * Class BaseService
 *
 * This is the base service class for other service classes in the application.
 * It provides common functionality and error handling utilities.
 *
 */
abstract class BaseService
{
    use Throwable;
}