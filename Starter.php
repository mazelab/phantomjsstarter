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

    private $options = '--proxy-type=none --ignore-ssl-errors=true';

    /** @var string */
    private $phantomjspath;

    private $process;

    /**
     * Starter constructor.
     *
     * @param int $port webdriver port number which is passed to the --webdriver option
     * @param string $options other additional options. Defaults to '--proxy-type=none --ignore-ssl-errors=true'
     * @param string $phantomjspath path to the phantomjs executable. Defaults to global 'phantomjs'
     */
    public function __construct($port = null, $options = null, $phantomjspath = 'phantomjs')
    {
        !isset ($port) ?: $this->port = $port;
        !isset ($options) ?: $this->options .= ' ' . $options;
        $this->phantomjspath = $phantomjspath;
    }

    /**
     * Starts a new phantomjs process in background
     *
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     */
    public function up()
    {
        $this->killAllRunning();
        $this->process = new Process($this->phantomjspath . ' --webdriver=' . $this->port . ' '  . $this->options);
        $process = $this->process;
        $output = new GenericEvent();
        $process->setTimeout(null);
        $process->start(function () use ($process, $output) {
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
                throw new RuntimeException('Phantomjs could not be started with webdriver on port ' . $this->port);
            }
        }
    }

    /**
     * Search and destroy all running phantoms on the same port
     */
    public function killAllRunning()
    {
        exec("pkill -f '" . $this->phantomjspath . " --webdriver=" . $this->port . " '");
    }

    public function __destruct()
    {
        $this->killAllRunning();
    }
}
