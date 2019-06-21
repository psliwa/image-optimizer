<?php
declare(strict_types=1);

namespace ImageOptimizer\TypeGuesser;


class GdTypeGuesser implements TypeGuesser
{
    public function __construct()
    {
        if(!function_exists('gd_info')) {
            throw new \RuntimeException(sprintf('%s class require gd extension to be enabled', __CLASS__));
        }
    }

    public function guess(string $filepath): string
    {
        list(,,$type) = getimagesize($filepath);

        switch($type) {
            case \IMAGETYPE_PNG:
                return self::TYPE_PNG;
            case \IMAGETYPE_GIF:
                return self::TYPE_GIF;
            case \IMAGETYPE_JPEG:
            case \IMAGETYPE_JPEG2000:
                return self::TYPE_JPEG;
            default:
                return self::TYPE_UNKNOWN;
        }
    }
}