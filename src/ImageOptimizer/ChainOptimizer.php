<?php


namespace ImageOptimizer;


class ChainOptimizer implements Optimizer
{
    /**
     * @var Optimizer[]
     */
    private $optimizers;

    public function __construct(array $optimizers)
    {
        $this->optimizers = $optimizers;
    }

    public function optimize($filepath)
    {
        foreach($this->optimizers as $optimizer) {
            $optimizer->optimize($filepath);
        }
    }
}