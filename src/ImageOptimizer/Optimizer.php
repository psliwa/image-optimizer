<?php
declare(strict_types=1);

namespace ImageOptimizer;


use ImageOptimizer\Exception\Exception;

interface Optimizer
{
    /**
     * @param string $filepath Filepath to file to optimize, it will be overwrite if optimization succeed
     * @return void
     * @throws Exception
     */
    public function optimize(string $filepath): void;
} 