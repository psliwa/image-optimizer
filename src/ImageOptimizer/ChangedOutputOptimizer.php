<?php
declare(strict_types=1);

namespace ImageOptimizer;

class ChangedOutputOptimizer implements WrapperOptimizer
{
    private $outputPattern;
    private $optimizer;

    public function __construct(string $outputPattern, Optimizer $optimizer)
    {
        $this->outputPattern = $outputPattern;
        $this->optimizer = $optimizer;
    }

    public function optimize(string $filepath): void
    {
        $fileInfo = pathinfo($filepath);
        $outputFilepath = str_replace(
            ['%basename%', '%filename%', '%ext%'],
            [$fileInfo['dirname'], $fileInfo['filename'], isset($fileInfo['extension']) ? '.'.$fileInfo['extension'] : ''],
            $this->outputPattern
        );

        $outputChanaged = $outputFilepath !== $filepath;

        if ($outputChanaged) {
            copy($filepath, $outputFilepath);
            $filepath = $outputFilepath;
        }

        try {
            $this->optimizer->optimize($filepath);
        } catch (\Throwable $exception) {
            if ($outputChanaged) {
                unlink($filepath);
            }

            throw $exception;
        }
    }

    public function unwrap(): Optimizer
    {
        return $this->optimizer instanceof WrapperOptimizer ? $this->optimizer->unwrap() : $this->optimizer;
    }
}