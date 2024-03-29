<?php
namespace LaravelConfigurator\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class LaravelConfiguratorServiceProvider extends ServiceProvider
{

    protected $namespace;
	
    protected $commands = ['LaravelConfigurator\Console\InstallCommand'];	
	
    public function register(){
        $this->commands($this->commands);
    }
	
}
