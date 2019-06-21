<?php
declare(strict_types=1);

namespace ImageOptimizer;

use function function_exists;
use ImageOptimizer\Exception\CommandNotFound;
use ImageOptimizer\Exception\Exception;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

final class Command
{
    private $cmd;
    private $args;
    private $timeout;

    public function __construct(string $bin, array $args = [], ?float $timeout = null)
    {
        if(!function_exists('exec')) {
            throw new Exception('"exec" function is not available. Please check if it is not listed as "disable_functions" in your "php.ini" file.');
        }

        if(!function_exists('proc_open')) {
            throw new RuntimeException('"proc_open" function is not available. Please check if it is not listed as "disable_functions" in your "php.ini" file.');
        }

        $this->cmd = $bin;
        $this->args = $args;
        $this->timeout = $timeout;
    }

    public function execute(array $customArgs = []): void
    {
        $process = new Process(array_merge([$this->cmd], $this->args, $customArgs));
        $process->setTimeout($this->timeout);

        try {
            $exitCode = $process->run();
            $commandLine = $process->getCommandLine();
            $output = $process->getOutput().PHP_EOL.$process->getErrorOutput();

            if($exitCode == 127) {
                throw new CommandNotFound(sprintf('Command "%s" not found.', $this->cmd));
            }

            if($exitCode !== 0 || stripos($output, 'error') !== false || stripos($output, 'permission') !== false) {
                throw new Exception(sprintf('Command failed, return code: %d, command: %s, stderr: %s', $exitCode, $commandLine, trim($output)));
            }
        } catch(RuntimeException $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}