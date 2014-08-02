<?php


namespace ImageOptimizer;


class CommandOptimizer implements Optimizer
{
    private $command;
    private $customArgs;

    public function __construct(Command $command, $extraArgs = null)
    {
        $this->command = $command;
        $this->customArgs = $extraArgs;
    }

    public function optimize($filepath)
    {
        $customArgs = array($filepath);

        if($this->customArgs) {
            $customArgs = array_merge(
                is_callable($this->customArgs) ? call_user_func($this->customArgs, $filepath) : $this->customArgs,
                $customArgs
            );
        }

        $this->command->execute($customArgs);
    }
}