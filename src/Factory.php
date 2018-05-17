<?php
namespace Fivesqrd\Central;

class Factory
{
    /* Storage config */
    protected $_config = [
        'namespace' => null,
        'adapter'   => null, // Aws|Bego
        'options'   => [ // Adapter specific options
            'table'  => null, // Required by Aws
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
        return new Instance(
            $this->_config, new Job($name, $arg), new Log()
        ); 
    }
}
