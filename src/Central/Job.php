<?php
namespace Central;

class Job
{
    protected $_storage;

    protected $_started;

    protected $_finished;

    protected $_name;

    protected $_status;

    protected $_message;

    protected $_memory;

    protected $_logger;

    const STATUS_ERROR     = 'error';
    const STATUS_SUCCESS   = 'success';
    const STATUS_EXCEPTION = 'exception';

    public function __construct($name, $arguments, $logger)
    {
        $this->_name = $name;
        $this->_arguments = $arguments;
        $this->_logger = $logger;
    }

    public function setStorage($value)
    {
        $this->_storage = $value;
    }

    public function started()
    {
        $this->_timestamp = time();
        $this->_started = microtime(true);

        return $this;
    }

    public function finished($status = true)
    {
        $this->_finished = microtime(true);
        $this->_memory = memory_get_peak_usage();

        if ($status instanceof \Exception) {
            $this->_status = self::STATUS_EXCEPTION;
            $this->_message = $message;
        } elseif (is_string($status)) {
            $this->_status = self::STATUS_ERROR;
            $this->_message = $status;
        } else {
            $this->_status = self::STATUS_SUCCESS;
        }

        return $this;
    }

    public function getDuration()
    {
        return round($this->_finished - $this->_started, 2);
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

    public function log()
    {
        return $this->_logger;
    }

    public function getSummary()
    {
        $message = null;

        if ($this->getExitMessage()) {
            $message = ": '{$this->getExitMessage}'";
        }

        return date('Y-m-d H:i:s') 
            . " {$this->getName()} completed with {$this->getExitStatus()} status" 
            . $message . '.' 
            . " Execution time was {$this->getDuration()} seconds."
            . " Peak memory usage was {$this->_memory} bytes.";
    }

    public function save($expiry = null)
    {
        if (!$this->_storage) {
            throw new Exception("Save operation is not possible if no config is provided");
        }

        $this->_storage->add(array(
            'Id'        => uniqid(),
            'Job'       => $this->_name,
            'Script'    => $this->_arguments[0],
            'Arguments' => implode(' ', array_slice($this->_arguments, 1)),
            'Timestamp' => date('Y-m-d H:i:s', $this->_timestamp),
            'Duration'  => $this->getDuration(),
            'Memory'    => $this->_memory,
            'Host'      => gethostname(),
            'Status'    => $this->getExitStatus(),
            'Message'   => $this->getExitMessage(),
            'Expires'   => $expiry,
            'Logs'      => $this->log()->toArray(),
        ));
    }
}
