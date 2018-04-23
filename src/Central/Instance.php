<?php

namespace Central;

class Instance
{
    protected $_job;

    protected $_log;

    protected $_config;

    protected $_mutex;

    protected $_lock = false;

    public function __construct($config, $job, $log, $mutex = null)
    {
        $this->_config = $config;
        $this->_job = $job;
        $this->_log = $log;
    }

    public function lock()
    {
         $this->_lock = $this->_mutex->lock($this->_job->getName())->acquire();

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

        $payload = new Central\Payload(
            ['interface' => $this->_job, 'log' => $this->_log], $expiry
        );

        /* TODO: add support for bego adapter */

        switch ($this->_config['adapter']) {
            case 'Aws':
                $storage = new Central\Storage\DynamoDb(
                    $this->_config
                );
                break;
            case 'Bego':
                $storage = new Central\Storage\Bego(
                    $this->_config
                );
                break;
        }

        $storage->put($payload);

        return $this;
    }
}
