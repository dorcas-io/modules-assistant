<?php

namespace Dorcas\ModulesAssistant;
use Illuminate\Support\ServiceProvider;

class ModulesAssistantServiceProvider extends ServiceProvider {

	public function boot()
	{
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
		$this->loadViewsFrom(__DIR__.'/resources/views', 'modules-assistant');
		$this->publishes([
			__DIR__.'/config/modules-assistant.php' => config_path('modules-assistant.php'),
		], 'config');
		/*$this->publishes([
			__DIR__.'/assets' => public_path('vendor/modules-assistant')
		], 'public');*/
	}

	public function register()
	{
		//add menu config
		$this->mergeConfigFrom(
	        __DIR__.'/config/navigation-menu.php', 'navigation-menu.modules-assistant.sub-menu'
	     );
	}

}


?>