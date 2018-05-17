<?php
namespace Fivesqrd\Central\Storage;

use Fivesqrd\Central\Payload;

class Bego
{
    protected $_config;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function client()
    {
        return $this->_config['options']['client'];
    }

    public function put($values)
    {
        $item = $this->client()->put($values);

        return $values;
    }
}
