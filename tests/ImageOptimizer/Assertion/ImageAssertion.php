<?php

namespace ImageOptimizer\Assertion;

use ImageOptimizer\ImageSimilarityJudge;
use PHPUnit\Framework\Assert;

class ImageAssertion
{
    private $originalImage;
    private $optimizedImage;

    public function __construct($originalImage, $optimizedImage)
    {
        $this->originalImage = $originalImage;
        $this->optimizedImage = $optimizedImage;
    }

    public static function create($originalImage, $optimizedImage)
    {
        return new self($originalImage, $optimizedImage);
    }

    public function optimizedFileIsSmallerThanPercent($percent)
    {
        $originalFilesize = filesize($this->originalImage);
        $actualPercent = filesize($this->optimizedImage)/ $originalFilesize * 100;

        Assert::assertLessThan($percent, $actualPercent, 'compression level is too small');

        return $this;
    }

    public function imagesHaveTheSameDimensions()
    {
        list($width1, $height1) = getimagesize($this->originalImage);
        list($width2, $height2) = getimagesize($this->optimizedImage);

        Assert::assertEquals($width1, $width2);
        Assert::assertEquals($height1, $height2);

        return $this;
    }

    public function imagesAreSimilarInPercent($percent)
    {
        $similarity = ImageSimilarityJudge::judge($this->originalImage, $this->optimizedImage);
        $percent = $percent/100;

        Assert::assertGreaterThan($percent, $similarity);

        return $this;
    }
} 