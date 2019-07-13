<?php
declare(strict_types=1);

namespace ImageOptimizer;

interface WrapperOptimizer extends Optimizer {
    public function unwrap(): Optimizer;
}