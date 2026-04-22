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

    public function testIsAgentPresentReturnsFalseWhenNoAgentEnvironmentVariableIsSet(): void
    {
        $detector = new Detector();

        $result = $detector->isAgentPresent([]);

        self::assertFalse($result);
    }

    public function testIsAgentPresentReturnsFalseWhenUnknownEnvironmentVariableIsSet(): void
    {
        $variable = self::faker()->slug();

        $detector = new Detector();

        $result = $detector->isAgentPresent([
            $variable => '1',
        ]);

        self::assertFalse($result);
    }

    /**
     * @dataProvider provideAgentEnvironmentVariable
     */
    public function testIsAgentPresentReturnsTrueWhenAgentEnvironmentVariableIsSet(string $variable): void
    {
        $detector = new Detector();

        $result = $detector->isAgentPresent([
            $variable => '1',
        ]);

        self::assertTrue($result);
    }

    /**
     * @return \Generator<string, array{0: string}>
     */
    public static function provideAgentEnvironmentVariable(): iterable
    {
        $variables = [
            'AI_AGENT',
            'AMP_CURRENT_THREAD_ID',
            'ANTIGRAVITY_AGENT',
            'AUGMENT_AGENT',
            'CLAUDECODE',
            'CLAUDE_CODE',
            'CLAUDE_CODE_IS_COWORK',
            'CODEX_CI',
            'CODEX_SANDBOX',
            'CODEX_THREAD_ID',
            'COPILOT_ALLOW_ALL',
            'COPILOT_GITHUB_TOKEN',
            'COPILOT_MODEL',
            'COPILOT_CLI',
            'CURSOR_AGENT',
            'CURSOR_EXTENSION_HOST_ROLE',
            'CURSOR_TRACE_ID',
            'GEMINI_CLI',
            'OPENCODE',
            'OPENCODE_CLIENT',
            'REPL_ID',
            'PI_CODING_AGENT',
        ];

        foreach ($variables as $variable) {
            yield $variable => [
                $variable,
            ];
        }
    }
}
