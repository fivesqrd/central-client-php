<?php
namespace Central\Storage;

use Central\Payload;

class DynamoDb
{
    protected $_dynamo;

    protected $_config = array();

    protected $_marshaler;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function client()
    {
        return $this->_config['options']['client'];
    }

    public function marshaler()
    {
        return $this->_config['options']['marshaler'];
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

    public function put(Payload $payload)
    {
        $result = $this->getClient()->putItem([
            'Item'      => $this->marshaler()->marshalItem(
                $this->_getAttributes($payload->toArray())
            ),
            'TableName' => $this->_config['options']['table'],
        ]);

        $response = $result->get('@metadata');

        if ($response['statusCode'] != 200) {
            throw new Exception(
                "DynamoDb returned unsuccessful response code: {$response['statusCode']}"
            );
        }

        return true;
    }
}
