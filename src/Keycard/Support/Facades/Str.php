<?php

namespace Keycard\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Str extends Facade
{

	protected static function getFacadeAccessor()
	{
		return 'str';
	}

}
