<?php

declare(strict_types=1);

namespace ImageOptimizer;

use ImageOptimizer\TypeGuesser\TypeGuesser;
use PHPUnit\Framework\TestCase;

class SmartOptimizerTest extends TestCase
{
    public const SUPPORTED_TYPE = 'png';
    public const UNSUPPORTED_TYPE = 'gif';

    public const SUPPORTED_FILEPATH = 'somefilepath';
    public const UNSUPPORTED_FILEPATH = 'unsupportedFilepath';

    /**
     * @var SmartOptimizer
     */
    private $optimizer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $internalOptimizer;

    protected function setUp(): void
    {
        $this->internalOptimizer = $this->createMock('ImageOptimizer\\Optimizer');

        $this->optimizer = new SmartOptimizer([
            self::SUPPORTED_TYPE => $this->internalOptimizer,
        ], new SmartOptimizerTest_TypeGuesser([
            self::SUPPORTED_FILEPATH => self::SUPPORTED_TYPE,
            self::UNSUPPORTED_FILEPATH => self::UNSUPPORTED_TYPE,
        ]));
    }

    /**
     * @test
     */
    public function givenSupportedFilepath_executeInternalOptimizer()
    {
        //given

        $filepath = self::SUPPORTED_FILEPATH;

        $this->internalOptimizer->expects($this->once())
            ->method('optimize')
            ->with($filepath);

        //when

        $this->optimizer->optimize($filepath);
    }

    /**
     * @test
     */
    public function givenUnsupportedFilepath_throwException()
    {
        $this->expectException(\ImageOptimizer\Exception\Exception::class);

        //given

        $filepath = self::UNSUPPORTED_FILEPATH;

        $this->internalOptimizer->expects($this->never())
            ->method('optimize');

        //when

        $this->optimizer->optimize($filepath);
    }
}

class SmartOptimizerTest_TypeGuesser implements TypeGuesser
{
    private $filepathToType;

    public function __construct(array $filepathToType)
    {
        $this->filepathToType = $filepathToType;
    }

    public function guess(string $filepath): string
    {
        return isset($this->filepathToType[$filepath]) ? $this->filepathToType[$filepath] : 'unknown';
    }
}
