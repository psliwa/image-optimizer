<?php

declare(strict_types=1);

namespace ImageOptimizer\TypeGuesser;

class ExtensionTypeGuesserTest extends AbstractTypeGuesserTest
{
    protected function createTypeGuesser(): TypeGuesser
    {
        return new ExtensionTypeGuesser();
    }

    public function validImageFileProvider()
    {
        $images = parent::validImageFileProvider();
        $images[] =  [
            __DIR__ . '/../Resources/sample.svg',
            TypeGuesser::TYPE_SVG,
        ];

        return $images;
    }
}
