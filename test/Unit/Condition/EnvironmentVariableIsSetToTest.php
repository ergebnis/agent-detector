<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE.md file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/agent-detector
 */

namespace Ergebnis\AgentDetector\Test\Unit\Condition;

use Ergebnis\AgentDetector\Condition;
use Ergebnis\AgentDetector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AgentDetector\Condition\EnvironmentVariableIsSetTo
 */
final class EnvironmentVariableIsSetToTest extends Framework\TestCase
{
    use Test\Util\Helper;
    private string $name;

    protected function setUp(): void
    {
        $this->name = \sprintf(
            'AGENT_DETECTOR_TEST_%s',
            \mb_strtoupper(\bin2hex(\random_bytes(8))),
        );
    }

    protected function tearDown(): void
    {
        \putenv($this->name);
    }

    public function testCreateReturnsEnvironmentVariableIsSetTo(): void
    {
        $faker = self::faker();

        $source = $faker->url();

        $condition = Condition\EnvironmentVariableIsSetTo::create(
            $this->name,
            $faker->word(),
            $source,
        );

        self::assertSame($source, $condition->source());
    }

    public function testIsMetReturnsFalseWhenEnvironmentVariableIsNotSet(): void
    {
        $faker = self::faker();

        $condition = Condition\EnvironmentVariableIsSetTo::create(
            $this->name,
            $faker->word(),
            $faker->url(),
        );

        self::assertFalse($condition->isMet());
    }

    public function testIsMetReturnsFalseWhenEnvironmentVariableIsSetToDifferentValue(): void
    {
        $faker = self::faker();

        $value = $faker->word();

        \putenv(\sprintf(
            '%s=%s-%s',
            $this->name,
            $value,
            $faker->word(),
        ));

        $condition = Condition\EnvironmentVariableIsSetTo::create(
            $this->name,
            $value,
            $faker->url(),
        );

        self::assertFalse($condition->isMet());
    }

    public function testIsMetReturnsTrueWhenEnvironmentVariableIsSetToValue(): void
    {
        $faker = self::faker();

        $value = $faker->word();

        \putenv(\sprintf(
            '%s=%s',
            $this->name,
            $value,
        ));

        $condition = Condition\EnvironmentVariableIsSetTo::create(
            $this->name,
            $value,
            $faker->url(),
        );

        self::assertTrue($condition->isMet());
    }
}
