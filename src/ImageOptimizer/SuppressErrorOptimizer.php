<?php


namespace ImageOptimizer;


use ImageOptimizer\Exception\Exception;

class SuppressErrorOptimizer implements Optimizer
{
    private $optimizer;

    public function __construct(Optimizer $optimizer)
    {
        $this->optimizer = $optimizer;
    }

    public function optimize($filepath)
    {
        try {
            $this->optimizer->optimize($filepath);
        } catch (Exception $e) {
            //suppress
        }
    }

    public function unwrap()
    {
        return $this->optimizer;
    }
}