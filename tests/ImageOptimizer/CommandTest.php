<?php
declare(strict_types=1);

namespace ImageOptimizer;

use PHPUnit\Framework\TestCase;

/**
 * Test Command in- and outputs.
 */
class CommandTest extends TestCase
{
    /**
     * Check if a CommandNotFound exception is thrown when the exit code is not 127.
     *
     * @expectedException \ImageOptimizer\Exception\CommandNotFound
     * @expectedExceptionMessage Command "command-does-not-exist" not found.
     */
    public function testCommandNotFound()
    {
        $command = new Command('command-does-not-exist');
        $command->execute();
    }

    /**
     * Check if an exception is thrown when the exit code is not 0, but the command is found.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Command failed, return code: 2, command: 'ls' '/dir/does/not/exist'.
     */
    public function testCommandFailed()
    {
        $command = new Command('ls', ['/dir/does/not/exist']);
        $command->execute();
    }

    /**
     * Check if an exception is thrown when the output contains the word "error".
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Command failed, return code: 0, command: 'echo' 'error', stderr: error.
     */
    public function testCommandSucceededButErrorInOutput()
    {
        $command = new Command('echo', ['error']);
        $command->execute();
    }

    /**
     * Check if an exception is thrown when the command times out.
     *
     * @expectedException \Exception
     * @expectedExceptionMessage The process "'sleep' '2'" exceeded the timeout of 0.1 seconds.
     */
    public function testCommandTimeout()
    {
        $command = new Command('sleep', ['2'], 0.1);
        $command->execute();
    }


    /**
     * Check if an exception is thrown when the command times out.
     */
    public function testCommandTimeout_timeoutNotExceeded()
    {
        $command = new Command('ls', [], 1.);
        $command->execute();
    }

    /**
     * Check if a valid command does not give an exception.
     */
    public function testCommandSucceeds()
    {
        $exception = false;

        try {
            $command = new Command('pwd');
            $command->execute();
        } catch (\Exception $ex) {
            $exception = $ex;
        }

        $this->assertSame(false, $exception);
    }
} 