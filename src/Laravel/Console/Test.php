<?php

namespace Fivesqrd\Central\Laravel\Console;

use Illuminate\Console\Command;
use Config;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'central:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test log';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $job = $this->app->make('central')//resolve('central')
            ->instance(self::class)
            ->start(array($this, '_execute'))
            ->save(strtotime('+ 30 days'));

        /* Store log for 30 days */
        $job->save(strtotime('+ 30 days'));

        $this->info("Command completed with: {$job->status()}");
    }

    protected function _execute($log)
    {
        $log->debug('Job started at ' . date('Y-m-d H:i:s'));
        
        return 'It worked';
    }
}
