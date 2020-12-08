<?php
declare(strict_types=1);

namespace ImageOptimizer;

use ImageOptimizer\Assertion\ImageAssertion;
use ImageOptimizer\Exception\CommandNotFound;
use PHPUnit\Framework\TestCase;

class OptimizersTest extends TestCase
{
    const TMP_DIR = 'tmp';

    /**
     * @test
     * @dataProvider optimizerProvider
     */
    public function givenOptimizerAndSampleFile_runOptimization_fileShouldBeSmallerAndTheSame(string $optimizerName, string $originalFile, float $expectedSizeOfOriginalFile = 70)
    {
        //given

        $factory = new OptimizerFactory([
            'ignore_errors' => $optimizerName === 'smart',
            'custom_optimizers' => [
                'custom_optimizer' => [
                    'command' => 'optipng',
                    'args' => ['-i0', '-o2', '-quiet']
                ]
            ]
        ]);
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
        $svgFile = __DIR__.'/Resources/sample.svg';

        return [
            ['optipng', $pngFile, 98.5],
            ['pngquant', $pngFile,],
            ['advpng', $pngFile, 101],
            ['pngquant', $pngWithoutExtension,],
            ['png', $pngFile,],
            ['pngcrush', $pngFile, 98.5,],
            ['pngout', $pngFile, 98.5,],
            ['gifsicle', $gifFile, 105,],
            ['jpegoptim', $jpgFile, 95,],
            ['jpegtran', $jpgFile, 95,],
            ['jpg', $jpgFile, 95,],
            ['svg', $svgFile, 90,],
            ['smart', $pngFile,],
            ['custom_optimizer', $pngFile, 98.5],
        ];
    }

    /**
     * @test
     * @expectedException \ImageOptimizer\Exception\Exception
     */
    public function givenUnsupportedFileForOptimizer_givenIgnoreErrorDisabled_throwEx()
    {
        $factory = new OptimizerFactory(['ignore_errors' => false]);

        $optimizer = $factory->get('png');

        $optimizer->optimize(__DIR__.'/Resources/sample.jpg');
    }

    /**
     * @test
     */
    public function givenUnsupportedFileForOptimizer_givenIgnoreErrorEnabled_ok()
    {
        $factory = new OptimizerFactory(['ignore_errors' => true]);

        $optimizer = $factory->get('png');

        $optimizer->optimize(__DIR__.'/Resources/sample.jpg');
    }

    /**
     * @test
     */
    public function givenOutputPath_optimizeOutputFile()
    {
        $factory = new OptimizerFactory(['output_filepath_pattern' => '%basename%/%filename%-optimized%ext%']);

        $optimizer = $factory->get('jpg');

        $sampleFile = $this->prepareSampleFile(__DIR__.'/Resources/sample.jpg');

        $optimizer->optimize($sampleFile);

        ImageAssertion::create($sampleFile, __DIR__.'/Resources/'.self::TMP_DIR.'/sample-optimized.jpg')
            ->imagesHaveTheSameDimensions()
            ->optimizedFileIsSmallerThanPercent(99)
            ->imagesAreSimilarInPercent(98.7)
        ;
    }

    protected function tearDown(): void
    {
        foreach(['sample.gif', 'sample.jpg', 'sample.png', 'samplepng', 'sample.svg', 'sample-optimized.jpg'] as $file) {
            @unlink(__DIR__.'/Resources/'.self::TMP_DIR.'/'.$file);
        }
    }

    private function prepareSampleFile(string $originalFile)
    {
        $destination = __DIR__.'/Resources/'.self::TMP_DIR.'/'.basename($originalFile);
        if(!@copy($originalFile, $destination)) {
            $this->fail(sprintf('Preparing sample file "%s" failed.', $originalFile));
        }

        return $destination;
    }
} 