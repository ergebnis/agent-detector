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

namespace Ergebnis\AgentDetector\Test\Unit;

use Ergebnis\AgentDetector\Detector;
use Ergebnis\AgentDetector\Test;
use PHPUnit\Framework;

/**
 * @covers \Ergebnis\AgentDetector\Detector
 */
final class DetectorTest extends Framework\TestCase
{
    use Test\Util\Helper;
    private const AGENT_ENVIRONMENT_VARIABLES = [
        'AGENT_CONTEXT_OUT',
        'AGENT_DISPLAY_OUT',
        'AI_AGENT',
        'AMP_CURRENT_THREAD_ID',
        'ANTIGRAVITY_AGENT',
        'AUGMENT_AGENT',
        'CLAUDECODE',
        'CLAUDE_CODE',
        'CLAUDE_CODE_IS_COWORK',
        'CLINE_ACTIVE',
        'CODEX_CI',
        'CODEX_SANDBOX',
        'CODEX_THREAD_ID',
        'COPILOT_CLI',
        'CURSOR_AGENT',
        'GEMINI_CLI',
        'GOOSE_TERMINAL',
        'GROK_AGENT',
        'JUNIE_DATA',
        'JUNIE_SHIM_PATH',
        'OPENCLAW_SHELL',
        'OPENCODE',
        'OPENCODE_CLIENT',
        'PI_CODING_AGENT',
    ];
    private const AGENT_ENVIRONMENT_VARIABLE_VALUES = [
        'CURSOR_EXTENSION_HOST_ROLE' => 'agent-exec',
    ];

    /**
     * @var array<string, false|string>
     */
    private array $environmentVariables = [];

    protected function setUp(): void
    {
        $variables = \array_merge(
            self::AGENT_ENVIRONMENT_VARIABLES,
            \array_keys(self::AGENT_ENVIRONMENT_VARIABLE_VALUES),
            [
                'AGENT_DETECTOR_DISABLE',
            ],
        );

        foreach ($variables as $variable) {
            $this->environmentVariables[$variable] = \getenv($variable);

            \putenv($variable);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->environmentVariables as $variable => $value) {
            if (false === $value) {
                \putenv($variable);

                continue;
            }

            \putenv(\sprintf(
                '%s=%s',
                $variable,
                $value,
            ));
        }

        $this->environmentVariables = [];
    }

    public function testIsAgentPresentReturnsFalseWhenNoAgentEnvironmentVariableIsSet(): void
    {
        $detector = new Detector();

        $result = $detector->isAgentPresent();

        self::assertFalse($result);
    }

    public function testIsAgentPresentReturnsFalseWhenUnknownEnvironmentVariableIsSet(): void
    {
        $variable = \mb_strtoupper(\str_replace(
            '-',
            '_',
            self::faker()->slug(),
        ));

        $this->environmentVariables[$variable] = false;

        \putenv(\sprintf(
            '%s=1',
            $variable,
        ));

        $detector = new Detector();

        $result = $detector->isAgentPresent();

        self::assertFalse($result);
    }

    /**
     * @dataProvider provideAgentEnvironmentVariable
     */
    public function testIsAgentPresentReturnsFalseWhenAgentEnvironmentVariableIsSetToEmptyValue(string $variable): void
    {
        \putenv(\sprintf(
            '%s=',
            $variable,
        ));

        $detector = new Detector();

        $result = $detector->isAgentPresent();

        self::assertFalse($result);
    }

    public function testIsAgentPresentReturnsTrueWhenAgentEnvironmentVariableIsSetToEmptyValueAndAnotherAgentEnvironmentVariableIsSet(): void
    {
        \putenv('AGENT_CONTEXT_OUT=');
        \putenv('PI_CODING_AGENT=1');

        $detector = new Detector();

        $result = $detector->isAgentPresent();

        self::assertTrue($result);
    }

    /**
     * @dataProvider provideAgentEnvironmentVariable
     */
    public function testIsAgentPresentReturnsTrueWhenAgentEnvironmentVariableIsSet(string $variable): void
    {
        \putenv(\sprintf(
            '%s=1',
            $variable,
        ));

        $detector = new Detector();

        $result = $detector->isAgentPresent();

        self::assertTrue($result);
    }

    /**
     * @dataProvider provideAgentEnvironmentVariable
     */
    public function testIsAgentPresentReturnsFalseWhenAgentEnvironmentVariableIsSetAndDisableEnvironmentVariableIsSet(string $variable): void
    {
        \putenv(\sprintf(
            '%s=1',
            $variable,
        ));

        \putenv(\sprintf(
            'AGENT_DETECTOR_DISABLE=%s',
            self::faker()->word(),
        ));

        $detector = new Detector();

        $result = $detector->isAgentPresent();

        self::assertFalse($result);
    }

    /**
     * @dataProvider provideAgentEnvironmentVariable
     */
    public function testIsAgentPresentReturnsTrueWhenAgentEnvironmentVariableIsSetAndDisableEnvironmentVariableIsSetToEmptyValue(string $variable): void
    {
        \putenv(\sprintf(
            '%s=1',
            $variable,
        ));

        \putenv('AGENT_DETECTOR_DISABLE=');

        $detector = new Detector();

        $result = $detector->isAgentPresent();

        self::assertTrue($result);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideAgentEnvironmentVariable(): iterable
    {
        foreach (self::AGENT_ENVIRONMENT_VARIABLES as $variable) {
            yield $variable => [
                $variable,
            ];
        }
    }

    /**
     * @dataProvider provideAgentEnvironmentVariableAndValue
     */
    public function testIsAgentPresentReturnsFalseWhenAgentEnvironmentVariableIsSetToUnexpectedValue(
        string $variable,
        string $value
    ): void {
        \putenv(\sprintf(
            '%s=%s',
            $variable,
            self::faker()->word(),
        ));

        $detector = new Detector();

        $result = $detector->isAgentPresent();

        self::assertFalse($result);
    }

    /**
     * @dataProvider provideAgentEnvironmentVariableAndValue
     */
    public function testIsAgentPresentReturnsTrueWhenAgentEnvironmentVariableIsSetToExpectedValue(
        string $variable,
        string $value
    ): void {
        \putenv(\sprintf(
            '%s=%s',
            $variable,
            $value,
        ));

        $detector = new Detector();

        $result = $detector->isAgentPresent();

        self::assertTrue($result);
    }

    /**
     * @return \Generator<string, array{0: string, 1: string}>
     */
    public static function provideAgentEnvironmentVariableAndValue(): iterable
    {
        foreach (self::AGENT_ENVIRONMENT_VARIABLE_VALUES as $variable => $value) {
            yield $variable => [
                $variable,
                $value,
            ];
        }
    }
}
