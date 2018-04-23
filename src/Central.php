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

    public static function job(array $config)
    {
        return new self($config);
    }

    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * Create a new job instance
     */
    public function instance($name, $arg = [])
    {
        return new Central\Instance(
            $this->_config, new Central\Job($name, $arg), new Central\Log()
        ); 
    }
}
