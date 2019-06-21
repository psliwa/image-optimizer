<?php
declare(strict_types=1);

namespace ImageOptimizer\TypeGuesser;

interface TypeGuesser
{
    const TYPE_JPEG = 'jpeg';
    const TYPE_PNG = 'png';
    const TYPE_GIF = 'gif';
    const TYPE_SVG = 'svg';
    const TYPE_UNKNOWN = 'unknown';

    /**
     * @param string $filepath
     * @return string Image file type, value of one of the TYPE_* const
     */
    public function guess(string $filepath): string;
} 