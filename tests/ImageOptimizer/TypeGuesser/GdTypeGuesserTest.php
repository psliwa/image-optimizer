<?php


namespace ImageOptimizer\TypeGuesser;


class GdTypeGuesserTest extends AbstractTypeGuesserTest
{
    protected function createTypeGuesser()
    {
        try {
            return new GdTypeGuesser();
        } catch (\RuntimeException $e) {
            $this->markTestSkipped('Gd extension is disabled');
        }
    }
}
 