<?php
if (count($argv) < 3) {
    echo "Usage: {$argv[0]} --env=production --job=<name> --debug --lock --log=30 <param1> <param2>...\n";
    exit;
}

$switches = getopt(
    null, array('env:', 'job:', 'lock', 'debug', 'log::')
);

/* Check the required values */
$missing = array_diff(array('env', 'job'), array_keys($switches));

if (count($missing) > 0) {
    echo "Required options missing: " . implode(', ', $missing) . "\n";
    exit;
}

/* Calculate end of the regular options */
$optindex = count($switches) + 1;

/* Set the application environment */
define('APPLICATION_ENV', $switches['env']);

/* Bootstrap the application */
require_once(__DIR__ . '/Bootstrap.php');

$lock = Mutex::lock(basename($argv[0]) . ':' . $switches['job']);

/* Acquire a lock if the switch was provided */
if (isset($switches['lock']) && !$lock->acquire()) {
    if (isset($switches['debug'])) {
        echo "Lock could not be acquired, exiting...\n";
    }
    exit;
}

/* Start the job logging */
$job = Central::job($switches['job'], $argv)->started();

try {
    /* Construct the class name to handle the job */
    $class = 'Application_Job_' . $switches['job'];

    if (!class_exists($class)) {
        throw new Exception(
            "A class could not be found for the requested job {$switches['job']}"
        );
    }

    $config = Zend_Registry::get('config');

    /* Invoke the job handler */
    $result = (new $class($config))->run(
        $job->log(), array_slice($argv, $optindex)
    );

    /* Stop the recording */
    $job->finished(true);
} catch (Exception $e) {
    $job->finished($e);
}

/* Persist logs for x days */
if (isset($switches['log'])) {
    $job->save(strtotime('+' . $switches['log'] . ' days'));
}

/* Release the lock */
if (isset($switches['lock'])) {
    $lock->release();
}

if (isset($switches['debug'])) {
    print_r($job->log()->toArray());
    echo $job->getSummary() . "\n";
}
