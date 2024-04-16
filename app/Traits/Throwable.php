<?php

namespace App\Traits;

/**
 * Trait providing throwable methods for throwing exceptions.
 */
trait Throwable
{
    /**
     * Throw an exception statically.
     *
     * @param mixed $exception The exception to throw.
     *
     * @return void
     */
    protected static function throwStatic(mixed $exception): void
    {
        throw $exception;
    }

    /**
     * Throw an exception.
     *
     * @param mixed $exception The exception to throw.
     *
     * @return void
     */
    protected function throw(mixed $exception): void
    {
        throw $exception;
    }

    /**
     * Throw an exception if a condition is met.
     *
     * @param bool $condition The condition to check.
     * @param mixed $exceptionClass The exception class to throw.
     * @param mixed ...$args Optional arguments to pass to the exception constructor.
     *
     * @return void
     */
    protected function throwIf(bool $condition, mixed $exceptionClass, ...$args): void
    {
        if ($condition) {
            $this->throw(new $exceptionClass(...$args));
        }
    }

    /**
     * Throw an exception if a condition is not met.
     *
     * @param bool $condition The condition to check.
     * @param mixed $exceptionClass The exception class to throw.
     * @param mixed ...$args Optional arguments to pass to the exception constructor.
     *
     * @return void
     */
    protected function throwIfNot(bool $condition, mixed $exceptionClass, ...$args): void
    {
        $this->throwIf(!$condition, $exceptionClass, ...$args);
    }
}
