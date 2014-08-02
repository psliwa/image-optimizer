<?php


namespace ImageOptimizer\TypeGuesser;


abstract class AbstractTypeGuesserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TypeGuesser
     */
    protected $typeGuesser;

    protected function setUp()
    {
        $this->typeGuesser = $this->createTypeGuesser();
    }

    /**
     * @test
     * @dataProvider validImageFileProvider
     */
    public function givenImageFile_returnType($filepath, $expectedType)
    {
        $this->assertEquals($expectedType, $this->typeGuesser->guess($filepath));
    }

    public function validImageFileProvider()
    {
        return array(
            array(
                __DIR__.'/../Resources/sample.png',
                TypeGuesser::TYPE_PNG,
            ),
            array(
                __DIR__.'/../Resources/sample.jpg',
                TypeGuesser::TYPE_JPEG,
            ),
            array(
                __DIR__.'/../Resources/sample.gif',
                TypeGuesser::TYPE_GIF,
            ),
            array(
                __FILE__,
                TypeGuesser::TYPE_UNKNOWN,
            ),
        );
    }

    abstract protected function createTypeGuesser();
}