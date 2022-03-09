<?php

declare(strict_types=1);

namespace ImageOptimizer\TypeGuesser;

interface TypeGuesser
{
    public const TYPE_JPEG = 'jpeg';
    public const TYPE_PNG = 'png';
    public const TYPE_GIF = 'gif';
    public const TYPE_SVG = 'svg';
    public const TYPE_UNKNOWN = 'unknown';

    /**
     * @param string $filepath
     * @return string Image file type, value of one of the TYPE_* const
     */
    public function guess(string $filepath): string;
}
