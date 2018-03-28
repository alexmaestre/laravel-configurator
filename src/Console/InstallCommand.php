<?php

namespace LaravelConfigurator\Console;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-configurator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configures Laravel from terminal';

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
	* Check parameter in .env file
	*
	* @return boolean
	*/
    private static function checkEnv($parameter,$value = null)
    {
		$env = fopen('.env','r+');
		$return = false;
		while(!feof($env)) {
			$line = fgets($env);
			$line = str_replace(array("\n", "\r"), '', $line);
			if(strpos($line,$parameter) === 0){
				$values = explode('=',$line);
				if(!empty($value)){				
					if($values[1] == $value){ $return = true; };
				}else{
					if(empty($values[1])){ $return = true; };
				}
			}
		}
		fclose($env); 
		return $return;
    }	

	/** 
	* Update .env file value
	*
	* @return boolean
	*/
    private static function updateEnv($parameter,$value)
    {
		$env = fopen('.env','r+');
		$newEnv = '';
		$changed = false;
		while(!feof($env)) {
			$line = fgets($env);
			if(strpos($line,$parameter) === 0){
				$line = $parameter.'='.$value."\r\n";
				$changed = true;
			}
			$newEnv .= $line;
		}
		file_put_contents('.env',$newEnv);
		fclose($env); 
		return $changed;
    }
	
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	$startTime = microtime(true);
	
        $this->comment(PHP_EOL."---------------------------------------------");
        $this->comment("     Laravel configurator");
        $this->comment("---------------------------------------------");
	
	//Prepare permissions
	$this->comment(PHP_EOL."Configure Laravel folders permissions");	
	exec("chmod 755 -R * && chmod -R ug+rwx storage && chmod -R ug+rwx bootstrap/cache");
	$this->info("Done");

	//Prepare env file
	if(!file_exists('.env')){ 
		if(!file_exists('.env.example')){
			$this->error("Fatal error, there isn't .env example file. Please download it or create .env file manually.");
			die();
		}
		exec("cp .env.example .env");
		$this->comment(PHP_EOL."Creating .env file");	
		$this->info("Done");
	};
			
	//Configure database connection
	$this->comment(PHP_EOL."Configure database connection");	
	if(!self::checkEnv('DB_PASSWORD','secret')){
		$changeDB = !$this->confirm("There is already a DB connection data. Do you want to change it?");
	};
	if(empty($changeDB)){
		$connection=false;
		while(!$connection){
			$db_host = $this->ask('Insert your host');
			$db_name = $this->ask('Insert your DB name');
			$db_user = $this->ask('Insert your DB user');
			$db_pass = $this->secret('Insert your DB password');
			$link = @mysqli_connect($db_host, $db_user, $db_pass);
			if(empty($link)){
				$this->error("Database connection failed");
			}else{
				$db_conn = mysqli_select_db($link,$db_name);
				if(!$db_conn){
					$this->error("Defined database user cannot manage this database");
				}else{
					self::updateEnv('DB_HOST',$db_host);
					self::updateEnv('DB_DATABASE',$db_name);
					self::updateEnv('DB_USERNAME',$db_user);
					self::updateEnv('DB_PASSWORD',$db_pass);	
					$this->info("Database has been configured");
					$connection=true;
				}
			}
		}
	}
	
	//Configure app key
	$this->comment(PHP_EOL."Configure App key");	
	if(!self::checkEnv('APP_KEY')){
		$changeKey = !$this->confirm("There is already an APP key. Do you want to change it?");
	};
	if(empty($changeKey)){	
		$hasKey = $this->confirm("Do you have any App key?");
		if($hasKey){
			$key = $this->ask('Insert your key');
			self::updateEnv('APP_KEY',$key);
			$this->info("Your key has been stored in Laravel configuration");
		}else{
			$this->call('key:generate');
			$this->info("New key has been generated");
		}
	}
	
	//End
	$endTime = number_format((microtime(true)-$startTime), 2, ',', ' ');
	$this->comment(PHP_EOL."---------------------------------------------");		
    $this->comment("     Laravel has been configured");
	$this->comment("     Installation time: ".$endTime." secs");
	$this->comment("---------------------------------------------".PHP_EOL);
    }
}
?>
