<?php

require_once realpath(__DIR__ . '/../vendor/autoload.php');

$aws = [
    'version'     => 'latest',
    'region'      => 'eu-west-1',
    'endpoint'    => 'http://192.168.254.10:8000',
    'credentials' => ['key' => 'my-key', 'secret' => 'my-secret']
];

Mutex::$options = array(
    'namespace' => 'My-Example-App',
    'table'     => 'Five-Nines-Locks',
    'aws'       => $aws
);

Central::$options = array(
    'namespace' => 'My-Example-App',
    'table'     => 'Five-Nines-Logs',
    'aws'       => $aws
);
