<?php

namespace ImageOptimizer;

use ImageOptimizer\Assertion\ImageAssertion;
use ImageOptimizer\Exception\CommandNotFound;

class OptimizersTest extends \PHPUnit_Framework_TestCase
{
    const TMP_DIR = 'tmp';

    /**
     * @test
     * @dataProvider optimizerProvider
     */
    public function givenOptimizerAndSampleFile_runOptimization_fileShouldBeSmallerAndTheSame($optimizerName, $originalFile, $expectedSizeOfOriginalFile = 70)
    {
        //given

        $factory = new OptimizerFactory(array(
            'ignore_errors' => $optimizerName === 'smart',
        ));
        $optimizer = $factory->get($optimizerName);

        $sampleFile = $this->prepareSampleFile($originalFile);

        //when

        try {
            $optimizer->optimize($sampleFile);
        } catch (CommandNotFound $e) {
            $this->markTestSkipped(sprintf('%s is not executable', $optimizerName));
        }

        //then

        ImageAssertion::create($originalFile, $sampleFile)
            ->imagesHaveTheSameDimensions()
            ->optimizedFileIsSmallerThanPercent($expectedSizeOfOriginalFile)
            ->imagesAreSimilarInPercent(98.7)
            ;
    }

    public function optimizerProvider()
    {
        $pngFile = __DIR__.'/Resources/sample.png';
        $pngWithoutExtension = __DIR__.'/Resources/samplepng';
        $gifFile = __DIR__.'/Resources/sample.gif';
        $jpgFile = __DIR__.'/Resources/sample.jpg';

        return array(
            array(
                'optipng', $pngFile, 98.5
            ),
            array(
                'pngquant', $pngFile,
            ),
            array(
                'advpng', $pngFile, 101
            ),
            array(
                'pngquant', $pngWithoutExtension,
            ),
            array(
                'png', $pngFile,
            ),
            array(
                'pngcrush', $pngFile, 98.5,
            ),
            array(
                'pngout', $pngFile, 98.5,
            ),
            array(
                'gifsicle', $gifFile, 105,
            ),
            array(
                'jpegoptim', $jpgFile, 95,
            ),
            array(
                'jpegtran', $jpgFile, 95,
            ),
            array(
                'jpg', $jpgFile, 95,
            ),
            array(
                'smart', $pngFile,
            ),
        );
    }

    /**
     * @test
     * @expectedException \ImageOptimizer\Exception\Exception
     */
    public function givenUnsupportedFileForOptimizer_givenIgnoreErrorDisabled_throwEx()
    {
        $factory = new OptimizerFactory(array(
            'ignore_errors' => false,
        ));

        $optimizer = $factory->get('png');

        $optimizer->optimize(__DIR__.'/Resources/sample.jpg');
    }

    /**
     * @test
     */
    public function givenUnsupportedFileForOptimizer_givenIgnoreErrorEnabled_ok()
    {
        $factory = new OptimizerFactory(array(
            'ignore_errors' => true,
        ));

        $optimizer = $factory->get('png');

        $optimizer->optimize(__DIR__.'/Resources/sample.jpg');
    }

    protected function tearDown()
    {
        foreach(array('sample.gif', 'sample.jpg', 'sample.png', 'samplepng') as $file) {
            @unlink(__DIR__.'/Resources/'.self::TMP_DIR.'/'.$file);
        }
    }

    private function prepareSampleFile($originalFile)
    {
        $destination = __DIR__.'/Resources/'.self::TMP_DIR.'/'.basename($originalFile);
        if(!@copy($originalFile, $destination)) {
            $this->fail(sprintf('Preparing sample file "%s" failed.', $originalFile));
        }

        return $destination;
    }
} 