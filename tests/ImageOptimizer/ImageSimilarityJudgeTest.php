<?php


namespace ImageOptimizer;


class ImageSimilarityJudgeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider judgeProvider
     */
    public function testJudge($image1, $image2, $expectedGreaterThan, $expectedLessThan = 1)
    {
        $actual = ImageSimilarityJudge::judge(__DIR__.'/Resources/'.$image1, __DIR__.'/Resources/'.$image2);

        $this->assertGreaterThanOrEqual($expectedGreaterThan, $actual);
        $this->assertLessThanOrEqual($expectedLessThan, $actual);
    }

    public function judgeProvider()
    {
        return array(
            array(
                'sample.png', 'sample.png', 1, 1,
            ),
            array(
                'sample.png', 'sample-negative.png', 0, 0.6,
            ),
            array(
                'sample.png', 'sample-flip.png', 0, 0.85,
            ),
            array(
                'sample.jpg', 'sample-low-quality.jpg', 0.9, 0.989,
            ),
        );
    }
}
 