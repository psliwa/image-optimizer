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
     */
    public function testCommandNotFound()
    {
        $this->expectException(\ImageOptimizer\Exception\CommandNotFound::class);
        $this->expectExceptionMessage('Command "command-does-not-exist" not found.');

        $command = new Command('command-does-not-exist');
        $command->execute();
    }

    /**
     * Check if an exception is thrown when the exit code is not 0, but the command is found.
     */
    public function testCommandFailed()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Command failed, return code: 2/');

        $command = new Command('ls', ['/dir/does/not/exist']);
        $command->execute();
    }

    /**
     * Check if an exception is thrown when the output contains the word "error".
     */
    public function testCommandSucceededButErrorInOutput()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Command failed, return code: 0/');

        $command = new Command('echo', ['error']);
        $command->execute();
    }

    /**
     * Check if an exception is thrown when the command times out.
     */
    public function testCommandTimeout()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/exceeded the timeout/');

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
