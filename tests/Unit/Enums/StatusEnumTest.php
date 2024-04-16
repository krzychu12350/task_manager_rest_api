<?php

namespace tests\Unit\Enums;

use App\Enums\StatusEnum;
use PHPUnit\Framework\TestCase;

/**
 * Class StatusEnumTest
 */
class StatusEnumTest extends TestCase
{
    /**
     * Test retrieving all status values.
     *
     * @return void
     */
    public function test_values_method_returns_all_enum_values_in_array(): void
    {
        $expectedValues = [1, 2, 3, 4, 5];

        $this->assertEquals($expectedValues, StatusEnum::values());
    }

    /**
     * Test retrieving a random status value.
     *
     * @return void
     */
    public function test_random_method_returns_random_value_of_enum(): void
    {
        $randomValue = StatusEnum::random()->value;

        $this->assertContains($randomValue, StatusEnum::values());
    }
}