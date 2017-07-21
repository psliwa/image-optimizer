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
        $args = array_merge($this->args, $customArgs);

        $isWindowsPlatform = defined('PHP_WINDOWS_VERSION_BUILD');

        if($isWindowsPlatform) {
            $suppressOutput = '';
            $escapeShellCmd = 'escapeshellarg';
        } else {
            $suppressOutput = ' 2>&1';
            $escapeShellCmd = 'escapeshellcmd';
        }

        $commandArgs = 0 === count($args) ? '' : ' '.implode(' ', array_map('escapeshellarg', $args));
        $command = $escapeShellCmd($this->cmd).$commandArgs.$suppressOutput;

        exec($command, $outputLines, $result);
        $output = implode(PHP_EOL,$outputLines);

        if($result == 127) {
            throw new CommandNotFound(sprintf('Command "%s" not found.', $this->cmd));
        }

        if($result !== 0) {
            throw new Exception(sprintf('Command failed, return code: %d, command: %s.', $result, $command));
        }

        if(stripos($output, 'error') !== false || stripos($output, 'permission') !== false) {
            throw new Exception(sprintf('Command failed, return code: %d, command: %s, stderr: %s.', $result, $command, $output));
        }
    }
} 
