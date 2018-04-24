<?php

namespace Central;

class Instance
{
    protected $_job;

    protected $_log;

    protected $_config;

    protected $_mutex;

    protected $_lock = false;

    protected $_record;

    public function __construct($config, $job, $log, $mutex = null)
    {
        $this->_config = $config;
        $this->_job = $job;
        $this->_log = $log;
    }

    public function lock($object)
    {
        /* Todo make locking work */
        $this->_lock = $object->lock($this->_job->getName())->acquire();

        return $this;
    }

    public function log()
    {
        if (!$this->_log) {
            throw new \Exception('Job not started yet');
        }

        return $this->_log;
    }

    public function status()
    {
        if (!$this->_job) {
            return 'not started';
        }

        return $this->_job->getExitStatus()
            . ':' . $this->_job->getExitMessage();
    }

    public function start(callable $callback)
    {
        try {
            $this->_job->started();

            $message = call_user_func(
                $callback, $this->_log
            );

            $this->_job->setExitMessage($message);

            /* Stop the recording */
            $this->_job->finished($this->_log, true);

        } catch (\Exception $e) {
            $this->_job->finished($this->_log, $e);
        }

        return $this;
    }

    public function save($expiry = null)
    {
        if (!isset($this->_config['adapter'])) {
            throw new Exception(
              "Save operation is not possible if no storage adapter is provided"
            );
        }

        $payload = new Payload(
            ['interface' => $this->_job, 'log' => $this->_log], $expiry
        );

        $this->_record = $this->_storage()->put($payload->attributes());

        return $this;
    }

    protected function _storage()
    {
        switch ($this->_config['adapter']) {
            case 'Aws':
                $storage = new Storage\DynamoDb($this->_config);
                break;
            case 'Bego':
                $storage = new Storage\Bego($this->_config);
                break;
            default:
                throw new \Exception(
                    "Invalid storage adapter: '{$this->_config['adapter']}'"
                );
                break;
        }

        return $storage;
    }

    public function record()
    {
        return $this->_record;
    }
}
