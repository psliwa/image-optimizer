<?php

declare(strict_types=1);

namespace ImageOptimizer;

use PHPUnit\Framework\TestCase;

class ImageSimilarityJudgeTest extends TestCase
{
    /**
     * @test
     * @dataProvider judgeProvider
     */
    public function testJudge(string $image1, string $image2, float $expectedGreaterThan, float $expectedLessThan = 1.)
    {
        $actual = ImageSimilarityJudge::judge(__DIR__ . '/Resources/' . $image1, __DIR__ . '/Resources/' . $image2);

        $this->assertGreaterThanOrEqual($expectedGreaterThan, $actual);
        $this->assertLessThanOrEqual($expectedLessThan, $actual);
    }

    public function judgeProvider()
    {
        return [
            ['sample.png', 'sample.png', 1., 1.],
            ['sample.png', 'sample-negative.png', 0, 0.6],
            ['sample.png', 'sample-flip.png', 0., 0.85],
            ['sample.jpg', 'sample-low-quality.jpg', 0.9, 0.989],
        ];
    }
}
