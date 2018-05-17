<?php
namespace Fivesqrd\Central;

class Job
{
    protected $_started;

    protected $_finished;

    protected $_timestamp;

    protected $_name;

    protected $_status;

    protected $_message;

    protected $_memory;

    const STATUS_ERROR     = 'error';
    const STATUS_MIXED     = 'mixed';
    const STATUS_SUCCESS   = 'success';
    const STATUS_EXCEPTION = 'exception';

    public function __construct($name, $arguments)
    {
        $this->_name = $name;
        $this->_arguments = $arguments;
    }

    public function started()
    {
        $this->_timestamp = time();
        $this->_started = microtime(true);

        return $this;
    }

    public function finished($log, $status = true)
    {
        $this->_finished = microtime(true);
        $this->_memory = memory_get_peak_usage();

        if ($status instanceof \Exception) {
            $this->_status = self::STATUS_EXCEPTION;
            $this->_message = $status->getMessage();
        } elseif (is_string($status)) {
            $this->_status = self::STATUS_ERROR;
            $this->_message = $status;
        } elseif (count($log->getErrors()) > 0) {
            $this->_status = self::STATUS_MIXED;
            $this->_message = 'Log entries with errors detected';
        } else {
            $this->_status = self::STATUS_SUCCESS;
        }

        return $this;
    }

    public function getDuration()
    {
        if ($this->_finished === null) {
            return null;
        }

        if ($this->_started === null) {
            return null;
        }

        return round($this->_finished - $this->_started, 2);
    }

    public function setExitMessage($string)
    {
        $this->_message = $string;
    }

    public function getExitMessage()
    {
        return $this->_message;
    }

    public function getExitStatus()
    {
        return $this->_status;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getArguments()
    {
        return $this->_arguments;
    }

    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    public function getPeakMemoryUsed()
    {
        return $this->_memory;
    }

    public function getSummary()
    {
        $message = null;

        if ($this->getExitMessage()) {
            $message = ": '{$this->getExitMessage()}'";
        }

        return date('Y-m-d H:i:s')
            . " {$this->getName()} completed with {$this->getExitStatus()}"
            . " status {$message}."
            . " Execution time was {$this->getDuration()} seconds."
            . " Peak memory usage was {$this->_memory} bytes.";
    }
}
