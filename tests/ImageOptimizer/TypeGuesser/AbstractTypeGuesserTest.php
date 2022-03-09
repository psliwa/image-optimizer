<?php

declare(strict_types=1);

namespace ImageOptimizer\TypeGuesser;

use PHPUnit\Framework\TestCase;

abstract class AbstractTypeGuesserTest extends TestCase
{
    /**
     * @var TypeGuesser
     */
    protected $typeGuesser;

    protected function setUp(): void
    {
        $this->typeGuesser = $this->createTypeGuesser();
    }

    /**
     * @test
     * @dataProvider validImageFileProvider
     */
    public function givenImageFile_returnType(string $filepath, string $expectedType)
    {
        $this->assertEquals($expectedType, $this->typeGuesser->guess($filepath));
    }

    public function validImageFileProvider()
    {
        return [
            [__DIR__ . '/../Resources/sample.png', TypeGuesser::TYPE_PNG],
            [__DIR__ . '/../Resources/sample.jpg', TypeGuesser::TYPE_JPEG],
            [__DIR__ . '/../Resources/sample.gif', TypeGuesser::TYPE_GIF],
            [__FILE__, TypeGuesser::TYPE_UNKNOWN],
        ];
    }

    abstract protected function createTypeGuesser(): TypeGuesser;
}
