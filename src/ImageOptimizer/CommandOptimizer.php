<?php


namespace ImageOptimizer;


class CommandOptimizer implements Optimizer
{
    private $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function optimize($filepath)
    {
        $this->command->execute(array($filepath));
    }
}