<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class DrivePublicLinkButtonStyleTest extends TestCase
{
    public function test_button_uses_static_high_priority_classes_when_rendered_in_production(): void
    {
        // Given
        $view = file_get_contents(
            dirname(__DIR__, 2).'/resources/views/drive/index.blade.php'
        );

        // When
        $matched = preg_match(
            '/<button[^>]*class="([^"]*)"[^>]*>\s*Create Public Link\s*<\/button>/s',
            $view,
            $matches
        );

        // Then
        $this->assertSame(1, $matched);

        foreach ([
            '!bg-emerald-600',
            '!text-white',
            'hover:!bg-emerald-700',
            'disabled:!bg-emerald-300',
            'disabled:cursor-not-allowed',
            'disabled:opacity-60',
        ] as $className) {
            $this->assertStringContainsString($className, $matches[1]);
        }
    }
}
