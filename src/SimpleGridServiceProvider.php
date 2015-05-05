<?php namespace Drastus\SimpleGrid;

use Drastus\SimpleGrid\SimpleGrid;
use Illuminate\Support\ServiceProvider;

class SimpleGridServiceProvider extends ServiceProvider {

	/**
	* Register the service provider.
	*
	* @return void
	*/
	public function register()
	{
		$this->app->bind('simplegrid', function ($app) {
			return new SimpleGrid($app);
		});
	}

	/**
	* Perform post-registration booting of services.
	*
	* @return void
	*/
	public function boot()
	{
		$this->loadViewsFrom(__DIR__.'/resources/views', 'simplegrid');
		$this->publishes([
			__DIR__.'/config/grid.php' => config_path('grid.php'),
		]);
	}
}
