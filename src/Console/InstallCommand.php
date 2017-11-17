<?php

namespace VivaCMS\Installer\Console;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vivacms-installer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configures Laravel and installs VivaCMS core';

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
		$startTime = microtime(true);
		
		$this->comment(PHP_EOL."Installing");
		
		$endTime = number_format((microtime(true)-$startTime), 2, ',', ' ');
		$this->comment(PHP_EOL."---------------------------------------------");		
        $this->comment("     Laravel has being configured");
		$this->comment("     Installation time: ".$endTime." secs");
		$this->comment("---------------------------------------------".PHP_EOL);
    }
}
?>
