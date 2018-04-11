<?php
namespace Central\Storage;

use Central\Payload;
use Aws\DynamoDb as Aws;

class DynamoDb
{
    protected $_dynamo;

    protected $_config = array();

    protected $_marshaler;

    public function __construct($config)
    {
        $this->_config = $config;
        $this->_marshaler = new Aws\Marshaler();
    }

    public function getClient()
    {
        if ($this->_dynamo) {
            return $this->_dynamo;
        }

        $this->_dynamo = new Aws\DynamoDbClient($this->_config['aws']);

        return $this->_dynamo;
    }

    protected function _getAttributes($values)
    {
        if (!isset($this->_config['namespace'])) {
            throw new Exception('Namespace not provided in configuration');
        }

        $attributes = array(
            'Namespace' => (string) $this->_config['namespace']
        );

        foreach ($values as $key => $value) {
            
            if ($value === null || $value == '') {
                /* empty strings are not allowed in DynamoDb */
                continue;
            }

            $attributes[$key] = $value;
        }

        return $attributes;
    }

    public function add(Payload $payload)
    {
        $result = $this->getClient()->putItem([
            'Item'      => $this->_marshaler->marshalItem(
                $this->_getAttributes($payload->toArray())
            ),
            'TableName' => $this->_config['table'],
        ]);

        $response = $result->get('@metadata');

        if ($response['statusCode'] != 200) {
            throw new Exception("DynamoDb returned unsuccessful response code: {$response['statusCode']}");
        }

        return true;
    }
}
