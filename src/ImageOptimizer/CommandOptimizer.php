<?php


namespace ImageOptimizer;


class CommandOptimizer implements Optimizer
{
    private $command;
    private $extraArgs;

    public function __construct(Command $command, $extraArgs = null)
    {
        $this->command = $command;
        $this->extraArgs = $extraArgs;
    }

    public function optimize($filepath)
    {
        $customArgs = array($filepath);

        if($this->extraArgs) {
            $customArgs = array_merge(
                is_callable($this->extraArgs) ? call_user_func($this->extraArgs, $filepath) : $this->extraArgs,
                $customArgs
            );
        }

        $this->command->execute($customArgs);
    }
}