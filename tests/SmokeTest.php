<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

#[\PHPUnit\Framework\Attributes\CoversNothing]
final class SmokeTest extends TestCase
{
    public function testTrue(): void
    {
        $this->assertTrue(true);
    }
}
