# Central
Utility for managing console scripts

## Basic Example ##
```
/* Start the job logging */
$job = Central::job('My-Test-Job', $argv)->started();

/* Instantiate the logger */
$log = Central::log();

/* Perform the work required */
try {
    $log->debug("Starting up");

    foreach ($i = 0; $i < 10; $i++) {
        $log->append("Working on line {$i}");
    }

    $job->setExitMessage("Completed {$i} steps}");

    /* Stop the recording */
    $job->finished($log, true);

} catch (Exception $e) {
    $job->finished($log, $e);
}

/* Persist logs for 30 days */
Central::save(
    ['interface' => $job, 'log' => $log], strtotime('+ 30 days')
);

/* Output debug info to stdout as well */
print_r($log->toArray());
echo $job->getSummary() . "\n";
```
