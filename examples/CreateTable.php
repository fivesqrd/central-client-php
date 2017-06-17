<?php
use Aws\Dynamodb;

require_once realpath(__DIR__ . '/../vendor/autoload.php');

$options = array(
    'aws' => array(
        'version'  => 'latest',
        'region'   => 'eu-west-1',
        'endpoint' => 'http://192.168.254.10:8000',
        'credentials' => array(
            'key'    => 'test',
            'secret' => 'test',
        )
    ),
    'table'     => 'Five-Nines-Logs' 
);


$client = new DynamoDb\DynamoDbClient($options['aws']);

/*
$result = $client->deleteTable([
    'TableName' => $options['table'],
]);
*/

$result = $client->createTable([
    'TableName' => $options['table'],
    'ProvisionedThroughput' => array(
        'ReadCapacityUnits'  => (int) 5,
        'WriteCapacityUnits' => (int) 5,
    ),
    'AttributeDefinitions' => array(
        array(
            'AttributeName' => 'Namespace',
            'AttributeType' => 'S'
        ),
        array(
            'AttributeName' => 'Id',
            'AttributeType' => 'S'
        )
    ),
    'KeySchema' => array(
        array(
            'AttributeName' => 'Namespace',
            'KeyType'       => 'HASH'
        ),
        array(
            'AttributeName' => 'Id',
            'KeyType'       => 'RANGE'
        )
    )
]);
