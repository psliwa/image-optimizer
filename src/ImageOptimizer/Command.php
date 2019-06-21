<?php


namespace ImageOptimizer;

use function function_exists;
use ImageOptimizer\Exception\CommandNotFound;
use ImageOptimizer\Exception\Exception;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

final class Command
{
    private $cmd;
    private $args = array();
    private $timeout;

    public function __construct($bin, array $args = array(), $timeout = null)
    {
        $this->cmd = $bin;
        $this->args = $args;
        $this->timeout = $timeout;

        if(!function_exists('exec')) {
            throw new Exception('"exec" function is not available. Please check if it is not listed as "disable_functions" in your "php.ini" file.');
        }

        if(!function_exists('proc_open')) {
            throw new RuntimeException('"proc_open" function is not available. Please check if it is not listed as "disable_functions" in your "php.ini" file.');
        }
    }

    public function execute(array $customArgs = array())
    {
        $process = new Process(array_merge(array($this->cmd), $this->args, $customArgs));
        $process->setTimeout($this->timeout);

        try {
            $exitCode = $process->run();
            $commandLine = $process->getCommandLine();
            $output = $process->getOutput().PHP_EOL.$process->getErrorOutput();

            if($exitCode == 127) {
                throw new CommandNotFound(sprintf('Command "%s" not found.', $this->cmd));
            }

            if($exitCode !== 0) {
                throw new Exception(sprintf('Command failed, return code: %d, command: %s.', $exitCode, $commandLine));
            }

            if(stripos($output, 'error') !== false || stripos($output, 'permission') !== false) {
                throw new Exception(sprintf('Command failed, return code: %d, command: %s, stderr: %s.', $exitCode, $commandLine, trim($output)));
            }
        } catch(RuntimeException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}