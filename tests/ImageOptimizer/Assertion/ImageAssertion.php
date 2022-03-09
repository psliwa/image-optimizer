<?php

declare(strict_types=1);

namespace ImageOptimizer\Assertion;

use ImageOptimizer\ImageSimilarityJudge;
use PHPUnit\Framework\Assert;

class ImageAssertion
{
    private $originalImage;
    private $optimizedImage;

    public function __construct(string $originalImage, string $optimizedImage)
    {
        $this->originalImage = $originalImage;
        $this->optimizedImage = $optimizedImage;
    }

    public static function create(string $originalImage, string $optimizedImage): ImageAssertion
    {
        return new self($originalImage, $optimizedImage);
    }

    public function optimizedFileIsSmallerThanPercent(float $percent): ImageAssertion
    {
        $originalFilesize = filesize($this->originalImage);
        $actualPercent = filesize($this->optimizedImage) / $originalFilesize * 100;

        Assert::assertLessThan($percent, $actualPercent, 'compression level is too small');

        return $this;
    }

    public function imagesHaveTheSameDimensions(): ImageAssertion
    {
        list($width1, $height1) = getimagesize($this->originalImage);
        list($width2, $height2) = getimagesize($this->optimizedImage);

        Assert::assertEquals($width1, $width2);
        Assert::assertEquals($height1, $height2);

        return $this;
    }

    public function imagesAreSimilarInPercent(float $percent): ImageAssertion
    {
        $similarity = ImageSimilarityJudge::judge($this->originalImage, $this->optimizedImage);
        $percent = $percent / 100;

        Assert::assertGreaterThan($percent, $similarity);

        return $this;
    }
}
