<?php

namespace App\Rules;

use Illuminate\Validation\Rules\Enum;

class EnumRule extends Enum
{
    /**
     * Get enum type;
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
