<?php
if (count($argv) < 3) {
    echo "Usage: {$argv[0]} --env=production --job=<name> --debug --lock --log=30 --profile=1 <param1> <param2>...\n";
    exit;
}

$switches = getopt(
    null, array('env:', 'job:', 'lock', 'debug', 'log::', 'profile::')
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

/* Set the minimum and maximum lock duration */
define('LOCK_TIME_MIN', 30);
define('LOCK_TIME_MAX', 600);

/* Bootstrap the application */
require_once(__DIR__ . '/Bootstrap.php');

$lock = Mutex::lock(basename($argv[0]) . ':' . $switches['job']);

/* Acquire a lock if the switch was provided */
if (isset($switches['lock']) && !$lock->acquire(LOCK_TIME_MAX)) {
    if (isset($switches['debug'])) {
        echo "Lock could not be acquired, exiting...\n";
    }
    exit;
}

/* Start the job logging */
$job = Central::job($switches['job'], $argv)->started();

/* Instantiate the logger */
$logger = Central::log();

/* Instantiate the db profiler */
if (isset($switches['profile'])) {
    $profile = new Central\Profile(
        Zend_Registry::get('db-read')->getProfiler(), $switches['profile']
    );
}

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
        $logger, array_slice($argv, $optindex)
    );

    /* Stop the recording */
    $job->finished($logger, true);

    /* Log the job's summary message, if one was provided */
    if ($result) {
        $job->setExitMessage($result);
    }
} catch (Exception $e) {
    $job->finished($logger, $e);
}

/* Persist logs for x days */
if (isset($switches['log'])) {
    Central::save(
        ['interface' => $job, 'log' => $logger, 'profile' => isset($profile) ? $profile : null],
        strtotime('+' . $switches['log'] . ' days')
    );
}

if (isset($switches['lock'])) {
    if ($job->getDuration() > LOCK_TIME_MIN) {
        /* Release the lock, if job ran longer than the minimum duration */
        $lock->release();
    } else {
        /* If job duration was short, shorten lock expiry as well */
        $lock->extend(LOCK_TIME_MIN - $job->getDuration());
    }
}

if (isset($switches['debug'])) {
    print_r($logger->toArray());
    //print_r($profile->toArray());
    echo $job->getSummary() . "\n";
}
