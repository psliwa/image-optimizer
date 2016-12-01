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
        // chain exceptions stack
        $exceptions = array();

        foreach($this->optimizers as $optimizer) {
            try {
                $optimizer->optimize($filepath);
            } catch (CommandNotFound $e) {
                // remember our exception and skip current optimization method
                array_push($exceptions, $e);
                continue;
            }

            if($this->executeFirst) break;
        }

        // if we have some exceptions - throw them for save library functionality
        foreach ($exceptions as $e) {
            throw $e;
        }
    }
}