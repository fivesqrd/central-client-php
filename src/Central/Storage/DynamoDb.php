<?php 
namespace Central\Storage;

use Aws\DynamoDb as Aws;

class DynamoDb
{
    protected $_dynamo;

    protected $_config = array();

    protected $_map = array(
        'id'        => ['name' => 'Id', 'type' => 'S'],
        'job'       => ['name' => 'Job', 'type' => 'S'],
        'script'    => ['name' => 'Script', 'type' => 'S'],
        'arguments' => ['name' => 'Arguments', 'type' => 'S'],
        'timestamp' => ['name' => 'Timestamp', 'type' => 'S'],
        'duration'  => ['name' => 'Duration', 'type' => 'N'],
        'host'      => ['name' => 'Host', 'type' => 'S'],
        'status'    => ['name' => 'Status', 'type' => 'S'],
        'message'   => ['name' => 'Message', 'type' => 'S'],
        'expires'   => ['name' => 'Expires', 'type' => 'N'],
    );

    public function __construct($config) 
    {
        $this->_config = $config; 
    }

    public function getClient()
    {
        if ($this->_dynamo) {
            return $this->_dynamo;
        }

        $this->_dynamo = new Aws\DynamoDbClient($this->_config['aws']);
        
        return $this->_dynamo;
    }

    protected function _getAttribute($key, $value)
    {
        if (!isset($this->_map[$key])) {
            throw new Exception("{$key} is not a mapped attribute");
        }

        $map = $this->_map[$key];

        return array($map['name'] => array($map['type'] => (string) $value));
    }

    protected function _getAttributes($values)
    {
        if (!isset($this->_config['namespace'])) {
            throw new Exception('Namespace not provided in configuration');
        }

        $attributes = array(
            'Namespace' => ['S' => (string) $this->_config['namespace']],
        );
    
        foreach ($values as $key => $value) {
            if ($value === null || $value == '') {
                continue;
            }

            $attributes = array_merge(
                $attributes, $this->_getAttribute($key, $value)
            );
        }

        return $attributes;
    }
    
    public function add($values)
    {
        $result = $this->getClient()->putItem([
            'Item'      => $this->_getAttributes($values),
            'TableName' => $this->_config['table'],
        ]);

        $response = $result->get('@metadata');

        if ($response['statusCode'] != 200) {
            throw new Exception("DynamoDb returned unsuccessful response code: {$response['statusCode']}");
        }

        return true;
    }
}
