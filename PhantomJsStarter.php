<?php
/**
 * Mazelab\Phantomjs\Starter
 *
 * @author Jens Klose <jens.klose@googlemail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Mazelab\Phantomjs;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class Starter
{
    private $port = 8643;

    public function _construct($port = null)
    {
        if ($port) {
            $this->port = $port;
        }
    }

    /**
     * Starts a new phantomjs process in background
     *
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function up()
    {
        $this->killAllRunning();
        $process = new Process('phantomjs --webdriver=' . $this->port . ' --proxy-type=none --ignore-ssl-errors=true');
        $output = new GenericEvent();
        $process->setTimeout(null);
        $process->start(function ($type, $buffer) use ($process, $output) {
            $output->setArgument('output', $process->getIncrementalOutput());
        });
        $phantomjsOnline = false;
        $portScan = false;
        while (! $phantomjsOnline) {
            if ($output->hasArgument('output')) {
                $portScan = strpos($output->getArgument('output'), 'running on port ' . $this->port);
            }
            if ($portScan) {
                echo $output->getArgument('output');
            }
            $phantomjsOnline = $process->isStarted() && $process->isRunning() && $portScan;
            if ($process->isTerminated()) {
                throw new RuntimeException('Phantomjs could not been started with webdriver on port ' . $this->port);
            }
        }
    }

    /**
     * Search and destroy all running phantoms on the same port
     */
    public function killAllRunning()
    {
        exec("pgrep -f 'phantomjs.*".$this->port."' && pgrep -f 'phantomjs.*".$this->port."' | xargs kill");
    }

    public function __destruct()
    {
        $this->killAllRunning();
    }
}
