<?php


namespace ImageOptimizer;

use ImageOptimizer\Exception\Exception;
use ImageOptimizer\TypeGuesser\SmartTypeGuesser;
use ImageOptimizer\TypeGuesser\TypeGuesser;

class SmartOptimizer implements Optimizer
{
    /**
     * @var Optimizer[]
     */
    private $optimizers;
    private $typeGuesser;

    public function __construct(array $optimizers, TypeGuesser $typeGuesser = null)
    {
        $this->optimizers = $optimizers;
        $this->typeGuesser = $typeGuesser ?: new SmartTypeGuesser();
    }

    public function optimize($filepath)
    {
        $type = $this->typeGuesser->guess($filepath);

        if(!isset($this->optimizers[$type])) {
            throw new Exception(sprintf('Optimizer for type "%s" not found.', $type));
        }

        $this->optimizers[$type]->optimize($filepath);
    }
}