<?php
declare(strict_types=1);

namespace ImageOptimizer;


use ImageOptimizer\Exception\Exception;
use Psr\Log\LoggerInterface;

class SuppressErrorOptimizer implements Optimizer
{
    private $optimizer;
    private $logger;

    public function __construct(Optimizer $optimizer, LoggerInterface $logger)
    {
        $this->optimizer = $optimizer;
        $this->logger = $logger;
    }

    public function optimize(string $filepath): void
    {
        try {
            $this->optimizer->optimize($filepath);
        } catch (Exception $e) {
            $this->logger->notice($e);
        }
    }

    public function unwrap(): Optimizer
    {
        return $this->optimizer;
    }
}