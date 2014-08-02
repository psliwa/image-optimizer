<?php


namespace ImageOptimizer;


class ChainOptimizer implements Optimizer
{
    /**
     * @var Optimizer[]
     */
    private $optimizers;
    private $executeFirst;

    public function __construct(array $optimizers, $executeFirst = false)
    {
        $this->optimizers = $optimizers;
        $this->executeFirst = (boolean) $executeFirst;
    }

    public function optimize($filepath)
    {
        foreach($this->optimizers as $optimizer) {
            $optimizer->optimize($filepath);

            if($this->executeFirst) break;
        }
    }
}