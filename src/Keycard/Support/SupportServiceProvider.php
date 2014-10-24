<?php

namespace Keycard\Support;

use Illuminate\Support\ServiceProvider;

class SupportServiceProvider extends ServiceProvider
{

	protected $defer = false;

	public function register()
	{
		$this->app[ 'str' ] = $this->app->share( function($app)
		{
			return new Str( $app );
		} );

		$this->app[ 'arr' ] = $this->app->share( function($app)
		{
			return new Arr( $app );
		} );
	}

	public function provides()
	{
		return array();
	}

}
