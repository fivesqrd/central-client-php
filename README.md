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
<<<<<<< Updated upstream
=======
$job = Central::job($config)
    ->instance(self::class)
    ->lock()
    ->start(self::class, array($this, '_execute'))
    ->save(strtotime('+ 30 days'));
```

## Laravel 5 ##

.env requirements
```
CENTRAL_TABLE="My-Table"
CENTRAL_ADAPTER="Aws"
CENTRAL_NAMESPACE="My-App"

AWS_KEY="my-key"
AWS_SECRET="my-secret"
AWS_REGION="eu-west-1"
AWS_ENDPOINT=
```

Using it in a command class:
 ```
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $job = resolve('central')
            ->instance(self::class)
            ->start(array($this, '_execute'))
            ->save(strtotime('+ 30 days'));

        /* logic here */

        $this->info("Command completed with: {$job->status()}");;
    }

    protected function _execute($log)
    {
        $log->debug('Job started at ' . date('Y-m-d H:i:s'));

        return 'It worked';
    }
```
>>>>>>> Stashed changes
