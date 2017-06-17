<?php
require_once realpath(__DIR__ . '/../vendor/autoload.php');

Central::$options = array(
    'aws' => array(
        'version'  => 'latest',
        'region'   => 'eu-west-1',
        'endpoint' => 'http://192.168.254.10:8000',
        'credentials' => array(
            'key'    => 'my-key',
            'secret' => 'my-secret',
        )
    ),
    'namespace' => 'My-Example-App',
    'table'     => 'Five-Nines-Logs' 
);

echo date('Y-m-d H:i:s') . " Job started\n";

$job = Central::job('CreateExampleStats', $argv)->started();

try {
    //work done in between the lines

    $job->log()->debug('Just saying');
    $job->log()->debug('Something is going to happen');
    $job->log()->append('Failed', 'error');
    
    $job->finished(true);
} catch (\Exception $e) {
    $job->finished($e);
}

$job->save(strtotime('+7 days'));

echo $job->getSummary() . "\n";
