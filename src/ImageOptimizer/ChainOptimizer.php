<?php


namespace ImageOptimizer;

use ImageOptimizer\Exception\Exception;
use Psr\Log\LoggerInterface;

class ChainOptimizer implements Optimizer
{
    /**
     * @var Optimizer[]
     */
    private $optimizers;
    private $executeFirst;
    private $logger;

    public function __construct(array $optimizers, $executeFirst, LoggerInterface $logger)
    {
        $this->optimizers = $optimizers;
        $this->executeFirst = (boolean) $executeFirst;
        $this->logger = $logger;
    }

    public function optimize($filepath)
    {
        $exceptions = array();
        foreach($this->optimizers as $optimizer) {
            try {
                $optimizer->optimize($filepath);

                if($this->executeFirst) break;
            } catch (Exception $e) {
                $this->logger->notice($e);
                $exceptions[] = $e;
            }
        }

        if(count($exceptions) === count($this->optimizers)) {
            throw new Exception(sprintf('All optimizers failed to optimize the file: %s', $filepath));
        }
    }
}