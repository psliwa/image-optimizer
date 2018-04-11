<?php


namespace ImageOptimizer\TypeGuesser;


class SmartTypeGuesserTest extends AbstractTypeGuesserTest
{
    protected function createTypeGuesser()
    {
        return new SmartTypeGuesser();
    }

    public function validImageFileProvider()
    {
        $images = parent::validImageFileProvider();
        $images[] =  array(
            __DIR__.'/../Resources/sample.svg',
            TypeGuesser::TYPE_SVG,
        );

        return $images;
    }
}
 