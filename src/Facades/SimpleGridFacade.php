<?php namespace Drastus\SimpleGrid\Facades;

use Illuminate\Support\Facades\Facade;

class SimpleGridFacade extends Facade {

	/**
	* Get the registered name of the component.
	*
	* @return string
	*/
	protected static function getFacadeAccessor()
	{
		return 'simplegrid';
	}
}
