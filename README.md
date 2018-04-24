# Central
Central log for console scripts

```
composer require fivesqrd/central:1.0.*
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

## Basic Example ##
```
$job = Central::job($config);

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
$job = Central::job($config)
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
$job = Central::job($config)
    ->instance(self::class)
    ->lock()
    ->start(self::class, array($this, '_execute'))
    ->save(strtotime('+ 30 days'));
```