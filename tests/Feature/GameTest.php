<?php
declare(strict_types=1);

namespace KataTest\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class GameTest extends TestCase
{
    #[DataProvider('seedAndExpectedOutput')]
    public function test_game(int $seed): void
    {
        $filename = __DIR__."/snapshots/game-$seed.txt";
        mt_srand($seed);

        ob_start();
        require __DIR__.'/../../GameRunner.php';
        $output = ob_get_clean();
        if (!file_exists($filename)) {
            file_put_contents($filename, $output);
        }

        $expected = file_get_contents($filename);

        self::assertSame($expected, $output);
    }

    public static function seedAndExpectedOutput(): iterable
    {
        foreach (range(1, 50) as $seed) {
            yield [$seed];
        }
    }
}
