<?php
namespace Central;

class Job
{
    protected $_log;

    protected $_storage;

    protected $_started;

    protected $_finished;

    protected $_name;

    protected $_status;

    protected $_message;

    const STATUS_ERROR     = 'error';
    const STATUS_SUCCESS   = 'success';
    const STATUS_EXCEPTION = 'exception';

    public function __construct($name, $arguments)
    {
        $this->_name = $name;
        $this->_arguments = $arguments;
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

    public function save($expiry = null)
    {
        if (!$this->_storage) {
            throw new Exception("Save operation is not possible if no config is provided");
        }

        $this->_storage->add(array(
            'id'        => uniqid(),
            'job'       => $this->_name,
            'script'    => $this->_arguments[0],
            'arguments' => implode(' ', array_slice($this->_arguments, 1)),
            'timestamp' => date('Y-m-d H:i:s', $this->_timestamp),
            'duration'  => $this->getDuration(),
            'host'      => gethostname(),
            'status'    => $this->getExitStatus(),
            'message'   => $this->getExitMessage(),
            'expires'   => $expiry,
            //'logs'    => ($this->log()->getErrors(),
        ));
    }
}
