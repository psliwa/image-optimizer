<?php

declare(strict_types=1);

namespace ImageOptimizer\TypeGuesser;

class GdTypeGuesserTest extends AbstractTypeGuesserTest
{
    protected function createTypeGuesser(): TypeGuesser
    {
        try {
            return new GdTypeGuesser();
        } catch (\RuntimeException $e) {
            $this->markTestSkipped('Gd extension is disabled');
        }
    }
}
