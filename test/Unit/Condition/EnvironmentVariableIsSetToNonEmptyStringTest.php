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
 * @covers \Ergebnis\AgentDetector\Condition\EnvironmentVariableIsSetToNonEmptyString
 */
final class EnvironmentVariableIsSetToNonEmptyStringTest extends Framework\TestCase
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

    public function testCreateReturnsEnvironmentVariableIsSetToNonEmptyString(): void
    {
        $source = self::faker()->url();

        $condition = Condition\EnvironmentVariableIsSetToNonEmptyString::create(
            $this->name,
            $source,
        );

        self::assertSame($source, $condition->source());
    }

    public function testIsMetReturnsFalseWhenEnvironmentVariableIsNotSet(): void
    {
        $condition = Condition\EnvironmentVariableIsSetToNonEmptyString::create(
            $this->name,
            self::faker()->url(),
        );

        self::assertFalse($condition->isMet());
    }

    public function testIsMetReturnsFalseWhenEnvironmentVariableIsSetToEmptyValue(): void
    {
        \putenv(\sprintf(
            '%s=',
            $this->name,
        ));

        $condition = Condition\EnvironmentVariableIsSetToNonEmptyString::create(
            $this->name,
            self::faker()->url(),
        );

        self::assertFalse($condition->isMet());
    }

    public function testIsMetReturnsTrueWhenEnvironmentVariableIsSetToNonEmptyValue(): void
    {
        $faker = self::faker();

        \putenv(\sprintf(
            '%s=%s',
            $this->name,
            $faker->word(),
        ));

        $condition = Condition\EnvironmentVariableIsSetToNonEmptyString::create(
            $this->name,
            $faker->url(),
        );

        self::assertTrue($condition->isMet());
    }
}
