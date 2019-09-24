<?php
declare(strict_types=1);

namespace ImageOptimizer;


use ImageOptimizer\Exception\Exception;
use Psr\Log\LoggerInterface;

class SuppressErrorOptimizer implements WrapperOptimizer
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
            $this->logger->error('Error during image optimization. See exception for more details.', [ 'exception' => $e ]);
        }
    }

    public function unwrap(): Optimizer
    {
        return $this->optimizer instanceof WrapperOptimizer ? $this->optimizer->unwrap() : $this->optimizer;
    }
}