<?php
namespace VivaCMS\Installer\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class VivaCMSInstallerServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace;
	
	  protected $commands = [
        'VivaCMS\Installer\Console\Install'
    ];	
	
    public function register(){
        $this->commands($this->commands);
    }
}
