# Central
Central log for console scripts

```
composer require fivesqrd/central:1.1.*
```

## Setup ##

Using AWS SDK
```
$config = [
    'namespace' => 'My-App-Name',
    'adapter'   => 'Aws', 
    'options'   => [ // Adapter specific options
        'table'     => null, 
        'client'    => new Aws\DynamoDb\DynamoDbClient($aws),
        'marshaler' => new Aws\DynamoDb\Marshaler()
    ], 
];
```

Using the Bego library
```
$db = new Bego\Database(
    new Aws\DynamoDb\DynamoDbClient($config), new Aws\DynamoDb\Marshaler()
);

$config = [
    'namespace' => 'My-App-Name',
    'adapter'   => 'Bego',
    'options'   => [ // Adapter specific options
        'client'    => $db->table(new App\MyTables\Logs()),
    ], 
];
```

With Laravel 5
Setup is automatically done in Laravel

## Basic Example ##
```
use Fivesqrd\Central;

$job = Central\Factory::job($config);

$job->instance('MyScriptName')->start(function($log) {
         
     //do something here

     $log->debug('Something was done');

     return 'Everything was done ok';
});

/* Store log for 30 days */
$job->save(strtotime('+ 30 days'));

/* Get the job status */
echo $job->status();
```

## Executing inside a class ##
```
use Fivesqrd\Central;

$job = Central\Factory::job($config)
    ->instance(self::class)
    ->start(array($this, '_execute'))
    ->save(strtotime('+ 30 days'));
```

## Realtime debug output ##
```
/* Output debug info to stdout as well */
print_r($job->log()->toArray());

/* Output the same log entry saved to storage */
print_r($job->record());

```

## Atomic locking ##
```
use Fivesqrd\Central;

$job = Central\Factory::job($config)
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