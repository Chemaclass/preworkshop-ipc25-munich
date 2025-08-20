<?php
declare(strict_types=1);

namespace KataTest\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final  class GameTest extends TestCase
{
    #[DataProvider('provideRunner')]
    public function test_runner(int $seed): void
    {
        $filename = __DIR__.'/snapshots/game-'.$seed.'.txt';

        mt_srand($seed);
        ob_start();
        require __DIR__.'/../../GameRunner.php';
        $actual = ob_get_clean();

        if(!file_exists($filename)) {
            file_put_contents($filename, $actual);
        }

        $expected = file_get_contents($filename);

        self::assertSame($expected, $actual);
    }

    public static function provideRunner(): iterable
    {
        foreach(range(1, 10) as $seed) {
            yield [$seed];
        }
    }
}
