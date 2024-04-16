<?php

namespace App\Enums;

use Illuminate\Support\Arr;

/**
 * An enumeration class for status values.
 */
enum StatusEnum: int
{
    /**
     * Status: Open.
     */
    case OPEN = 1;

    /**
     * Status: In Progress.
     */
    case IN_PROGRESS = 2;

    /**
     * Status: Pending.
     */
    case PENDING = 3;

    /**
     * Status: Closed.
     */
    case CLOSED = 4;

    /**
     * Status: Canceled.
     */
    case CANCELED = 5;

    /**
     * Retrieve all status values.
     *
     * @return int[] Array of status values.
     */
    public static function values(): array
    {
        $values = [];

        foreach (self::cases() as $props) {
            $values[] = $props->value;
        }

        return $values;
    }

    /**
     * Retrieve a random status value.
     *
     * @return StatusEnum Random status value.
     */
    public static function random(): StatusEnum
    {
        return Arr::random(self::cases());
    }
}
