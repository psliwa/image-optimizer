<?php


namespace ImageOptimizer;


use ImageOptimizer\Exception\CommandNotFound;
use ImageOptimizer\Exception\Exception;

final class Command
{
    private $cmd;
    private $args = array();

    public function __construct($bin, array $args = array())
    {
        $this->cmd = $bin;
        $this->args = $args;
    }

    public function execute(array $customArgs = array())
    {
        if(!is_executable($this->cmd)) {
            throw new CommandNotFound(sprintf('Command "%s" not found.', $this->cmd));
        }

        $args = array_merge($this->args, $customArgs);

        $isWindowsPlatform = defined('PHP_WINDOWS_VERSION_BUILD');

        if($isWindowsPlatform) {
            $suppressOutput = '';
            $escapeShellCmd = 'escapeshellarg';
        } else {
            $suppressOutput = '  2>&1';
            $escapeShellCmd = 'escapeshellcmd';
        }

        $command = $escapeShellCmd($this->cmd).' '.implode(' ', array_map($escapeShellCmd, $args)).$suppressOutput;

        exec($command, $outputLines, $result);
        $output = join(PHP_EOL,$outputLines);

        if($result == 127) {
            throw new CommandNotFound(sprintf('Command "%s" not found.', $command));
        } else if($result !== 0) {
            throw new Exception(sprintf('Command failed, return code: %d, command: %s', $result, $command));
        } else if($result === 0 && stripos(strtolower($output), 'error') !== false) {
            throw new Exception(sprintf('Command failed, return code: %d, command: %s, stderr: %s', $result, $command, $output));
        }
    }
} 
