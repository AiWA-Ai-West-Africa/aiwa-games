<?php
use PHPUnit\Framework\TestCase;

final class PluginTest extends TestCase
{
    public function testFlashcardsFileExists(): void
    {
        $this->assertFileExists(__DIR__ . '/../games/MandinkaFlashcards.php');
    }
}
