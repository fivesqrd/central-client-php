# Central
Central log for console scripts

```
composer require fivesqrd/central:1.0.*
```

## Setup ##

```
$config = [
    'namespace' => null,
    'adapter'   => null, // Aws|Bego
    'options'   => [ // Adapter specific options
        'table'     => null, // Required by both Bego and Aws
        'client'    => new Aws\DynamoDb\DynamoDbClient($aws),
        'marshaler' => new Aws\DynamoDb\Marshaler()
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
```

## Atomic locking ##
```
$job = Central::job($config)
    ->instance(self::class)
    ->lock()
    ->start(self::class, array($this, '_execute'))
    ->save(strtotime('+ 30 days'));
```