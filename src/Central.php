<?php

class Central
{
    /* Storage config */
    protected $_config = [
        'namespace' => null,
        'adapter'   => null, // Aws|Bego
        'options'   => [ // Adapter specific options
            'table'  => null, // Required by both Bego and Aws
            'client' => null
        ], 
    ];

    protected $_job;

    protected $_log;

    public static function job(array $config)
    {
        return new self($config);
    }

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function lock()
    {
        //TODO: add Mutex
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

    public function start($name, callable $callback)
    {
        try {
            $this->_job = (new Central\Job($name, array()))->started();
            $this->_log = new Central\Log();

            $message = call_user_func(
                $callback, $this->_log
            );

            $this->_job->setExitMessage($message);

            /* Stop the recording */
            $this->_job->finished($log, true);

        } catch (\Exception $e) {
            $this->_job->finished($log, $e);
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
