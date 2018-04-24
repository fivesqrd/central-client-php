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

    public function put($values)
    {
        $result = $this->client()->putItem([
            'Item'      => $this->marshaler()->marshalItem($values),
            'TableName' => $this->_config['options']['table'],
        ]);

        $response = $result->get('@metadata');

        if ($response['statusCode'] != 200) {
            throw new Exception(
                "DynamoDb returned unsuccessful response code: {$response['statusCode']}"
            );
        }

        return $values;
    }
}
