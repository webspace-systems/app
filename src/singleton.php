<?php

class singleton extends model {
 
	// Keeping always one instance only
	// proxying calls to that

	static $instance = null;

	function __construct(){

		if(!is_null(static::$instance))
		{
			return static::$instance;
		}

		static::$instance = $this;
	}

	static function __callStatic($fn, $args){

		if(is_null(static::$instance))
		{
			$cn = get_class();

			static::$instance = new $cn();
		}

		if(method_exists(static::$instance, $fn))
		{
			return call_user_func_array([static::$instance, $fn], $args);
		}
	}

	function __call($fn, $args){

		if(is_null(static::$instance))
		{
			static::$instance = $this;
		}

		if(method_exists(static::$instance, $fn))
		{
			return call_user_func_array([static::$instance, $fn], $args);
		}
	}
}
