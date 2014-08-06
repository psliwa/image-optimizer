<?php


namespace ImageOptimizer;

use ImageOptimizer\TypeGuesser\SmartTypeGuesser;

class ImageSimilarityJudge
{
    /**
     * Calculates similarity index of two images
     *
     * @param $image1
     * @param $image2
     * @return float images similarity - value from 0 to 1. 1 - images are the same, 0 - images are totally different,
     * 0.98 - images are very similar
     */
    public static function judge($image1, $image2)
    {
        list($width, $height) = getimagesize($image1);

        $resource1 = self::createResource($image1);
        $resource2 = self::createResource($image2);

        $delta = 0;

        for($x=0; $x<$width; $x++) {
            for($y=0; $y<$height; $y++) {
                //is faster about 30% than array_sum(array_map(...)) solution
                $color1 = self::colorAt($resource1, $x, $y);
                $color2 = self::colorAt($resource2, $x, $y);
                $delta += abs($color1['red'] - $color2['red']) +
                    abs($color1['green'] - $color2['green']) +
                    abs($color1['blue'] - $color2['blue']);
            }
        }

        return 1 - ($width && $height ? $delta/(3*255*$width*$height) : 0);
    }

    private static function createResource($image)
    {
        $typeGuesser = new SmartTypeGuesser();
        $type = $typeGuesser->guess($image);

        $function = 'imagecreatefrom'.$type;

        if(!function_exists($function)) {
            throw new \InvalidArgumentException(sprintf('Image "%s" is not supported', $type));
        }

        return $function($image);
    }

    private static function colorAt($image, $x, $y)
    {
        $colorIndex = imagecolorat($image, $x, $y);
        return imagecolorsforindex($image, $colorIndex);
    }
} 