<?php

trait _error {


	function error($msg = "", $add_data = [], $die = true, $log = true, $code = 500){

		return static::_error($msg, $add_data, $die, $log, $code);
	}


	static function error_log( $err ) : void {

		// To be customized
		error_log( var_export($err, true) . str_repeat("\n",3) );
	}
	

	static function error_die( $err ) : void {

		// To be customized

		if( $is_dev_env = in_array(explode(':', $_SERVER['HTTP_HOST'])[0], ['localhost','127.0.0.1']) )
		{
			print_r($err);
		}

		die;
	}


	static function _error( $msg, array $add_data = [], $die = true, $log = true, $code = null ) : array {

		$trace = debug_backtrace();

		if(is_array($add_data) && isset($add_data['trace']))
		{
			$trace = $add_data['trace'];
			unset($add_data['trace']);
		}
		else
		{
			array_shift($trace);
		}

		$error = [
			'msg' => $msg,
			'add_data' => $add_data,
			'did_we_die' => $add_data['fatal'] ?? $die,
			'code' => $code,
			'to_be_logged' => $log,
			'trace' => $trace
		];

		if(is_int($code))
		{
			http_response_code($code);
		}

		if($log)
		{
			static::error_log($error);
		}

		if($die)
		{
			static::error_die($error);
		}

		return $error;
	}


	// To handle php errors
	// set_error_handler(['_error','_on_error']);
	static function _on_error($errno, $errstr, $errfile, $errline, $trace = null, $in_shutdown = false){

		$types = array_flip(array_slice(get_defined_constants(true)['Core'], 1, 15, true));

		$fatal = !in_array($errno, [ E_NOTICE, E_USER_WARNING, E_USER_NOTICE ]);

		$trace = debug_backtrace();
	
		array_shift($trace);

		$error = [
			'errn' => isset($types[$errno]) ? $types[$errno] : $errno,
			'errno' => $errno,
			'errstr' => $errstr,
			'errfile' => $errfile,
			'errline' => $errline,
			'fatal' => $fatal,
			'in_shutdown' => $in_shutdown,
			'trace' => $trace
		];

		static::_error($errstr, $error, $fatal, true, $fatal ? 500 : null);

		return true; // true = no further error handling
	}


	// To handle fatal errors:
	// register_on_shutdown(['_error','_on_shutdown']);
	static function _on_shutdown(){
	
		if( ! empty( $last_error = error_get_last() ) && array_shift( $trace = debug_backtrace() ) )
		{
			error_clear_last();

			static::_on_error($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line'], $trace, true);

			if(!empty(error_get_last()))
			{
				static::_on_shutdown();
			}
		}
	}
}
