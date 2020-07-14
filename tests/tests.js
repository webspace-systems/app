
export class tests {

	static default(){

		this.ÆØÅ_ÆÅT_TESTER_HERtest = 1;

	}

	static constructor(){

		this.ÆØÅ_ÆÅT_TESTER_HERtest = 1;

	}

}

/*
class test {

	// Simply use like this:
	//
	//		require 'tests.php';
	//		
	//		new test ( [ 'file' => 'router.php',  'function' => 'router::route' ],
	//			[
	//				[ 'parameters' => [''], 		'must_result' => [ 'null', null ]  ],
	//				[ 'parameters' => ['index'],	'must_result' => [ 'null', null ]  ],
	//				[ 'parameters' => ['router'],	'must_result' => [ 'null', null ]  ],
	//			]
	// 	);
	//
	//		// And thats it. - The tests is made immediately when instantiated
	//		// If no errors found, the php test script outputs string fx "OK - NO ERRORS FOUND"
	// 	// - To be grep'ed in bash script, to have bash script stop and show error



	// To-do: Param. (function) "env-init" - to customize how function is run


	function __construct( array $what_to_test, array $expectations ) {

		$this->what_to_test = $what_to_test;

		$function = $what_to_test['function'] OR $this->test_spec_error_exit('Missing param. "function" (to test) in 1nd. arg. array "$what_to_test_to_test".');


		$type = 'function'; // default, to be overwritten by:

		if(preg_match('/[->,::]+/', $function, $function_string_components, PREG_OFFSET_CAPTURE))
		{
			$type = $function_string_components[0][0] == '::' ? 'method' : 'method';
		}

		$what_to_test['type'] = $type;



		$this->expectations = $expectations;

		// To-do: array_structure_valid  


		$this->src_root_path = realpath($this->src_root_path);


		require_once $this->src_root_path . '/router.php';

		router::$base_paths = [ $this->src_root_path ];

		spl_autoload_register(['router','route']);

		set_error_handler(['_error','_on_error']);
		register_shutdown_function(['_error','_on_shutdown']);


		if( $type == 'function' )
		{
			include ( $filepath = realpath($this->src_root_path) . $file ) OR $this->test_spec_error_exit('File not found: "'.$filepath.'".');

			if( ! function_exists( $function ) ) $this->test_spec_error_exit('File was found, but the function was not: "'.$function.'".');

			foreach( $this->expectations as list( $parameters, $ret_type_val ) )
			{
					

				exit(':'.print_r($parameters,1));
				call_user_func_array($function, $s);

			}
		}
		else if( $type == 'method' )
		{
			$function = $what_to_test['function'] OR $this->test_spec_error_exit('Missing param. "function" (to test) in 1nd. arg. array "$what_to_test_to_test".');
			$obj_meth = explode( strpos($function,'::') ? '::' : '->',  $function );

			if ( count($obj_meth) !== 2 ) $this->test_spec_error_exit('1st arg. "$what_to_test" param. "$function" must be format either: "classname->methodname" or "classname::methodname" for static.');

			try {
				include_once $filepath = realpath($this->src_root_path) . $file;
			}
			catch(Exception $e) { $this->test_spec_error_exit('File not found: "'.$filepath.'".'); }

			if( ! class_exists( $obj_meth[0] ) ) $this->test_spec_error_exit('File was found, but the class was not: "'.$obj_meth[0].'".');
			if( ! method_exists( $obj_meth[0], $obj_meth[1] ) ) $this->test_spec_error_exit('File and class was found, but the method was not: "'.$obj_meth[1].'".');

			foreach( $this->expectations as $scene_idx_k => $exp )
			{
				if( ! is_array( $exp['must_result'] ) ) $this->test_spec_error_exit("Expectation '$scene_idx_k': Malformed specification of 'must_result'; Expected: array [ 'type', exact_result_val ] or just [ exact_result_val ]. Got: ".print_r($exp['must_result'],1));

				$exp_type = strtolower($exp['must_result'][0]) ?? 'UNSET';
				$exp_returned = $exp['must_result'][1] ?? 'UNSET';

				if ( ! is_string( $exp_type ) )
				{
					if ( count( $exp['must_result'] ) == 1 )
					{
						$exp_returned = $exp['must_result'][0];
						$exp_type = 'UNSET';
					}
					else
					{
						$this->test_spec_error_exit("Malformed \$expectations[ $scene_idx_k ][ \"must_result\" ]. Expected: array [ 'type', exact_result_val ] or just [ exact_result_val ]. Got: ".print_r($exp['must_result'],1));
					}
				}
				
				$returned = call_user_func_array([$obj_meth[0], $obj_meth[1]], $exp['parameters']);
				$returned_type = strtolower(gettype($returned));

				if ( $exp_type != 'UNSET' && $returned_type !== $exp_type )
				{
					$this->fail_exit("Expectation '$scene_idx_k': Unexpected type of result. Expected: '$exp_type'. Got: '$returned_type'. Returned: \"".print_r($returned,1).'".');
				}

				if ( $exp_returned != 'UNSET' && $returned !== $exp_returned )
				{
					$this->fail_exit("Expectation '$scene_idx_k': Unexpected result. Expected: \"".print_r($exp_returned,1)."\". Got: ".print_r($returned,1));
				}
			}
		}
		else if( $type == 'file' )
		{

			
		}
		else $this->test_spec_error_exit("Missing a case for type: '$type'?? Maybe letter casing issue?");
	}


	function success_exit ( ) {

		print "OK";

		exit;
	}


	function fail_exit ( string $msg, $incl_spec_data = false, bool $error_output_in_html = null ) {
		
		if( is_null( $error_output_in_html ) && php_sapi_name() != 'cli' )
		{
			$error_output_in_html = true;
		}

		$interpreted_filepath = realpath($this->src_root_path) . $this->what_to_test['file'];

		$o = "\nFailed test:\n"
			. "\nType: \n\t{$this->what_to_test['type']}\n"
			. "\nFile: \n\t{$this->what_to_test['file']} {$interpreted_filepath}\n"
			. "\nFunction: \n\t{$this->what_to_test['function']}\n"
			. "\nMsg, if any: \n\t{$msg}\n"
			. ( $incl_spec_data ? 
					"\n\n"
				. "\nTest specification: " . ($error_output_in_html?'<pre>':'') . "\n\n"
				. print_r([
					'what_to_test' => $this->what_to_test,
					'expectations' => $this->expectations,
					'stacktrace' => debug_backtrace()
				  ],1)
				. "\n\n" . ($error_output_in_html?'<pre>':'') . "\n"
			 : '')
		;

		if ( $error_output_in_html )
		{
			$o = strtr($o, [ "\n" => "<br />\n",  "\t" => str_repeat('&nbsp;', 3) ]);
		}

		print $o;

		exit;
	}

	function test_spec_error_exit ( ...$args ) {

		$args[0] = "TEST SPEC ERROR: " . $args[0];
		$args[1] = true;

		return $this->fail_exit(...$args);
	}



	function array_structure_valid ( $array_to_validate, ...$structure_or_alt ) :? array {
		// $structure defined like: [ 'KEY_NAME' => "TYPE", 'KEY_NAME' => [ 'SUB_AR_KEY_NAME' => "SUB_AR_VALUE" ] ]
		// e.g.: [ 'success' => "bool", 'data' => [ 'rows' => "array"], 'some_o_key' => "int" ] // can also use "integer"
		$returns_if_valid = $array_to_validate;
		$returns_if_invalid = FALSE;

		print_r("ss".$structure_or_alt);
		exit(';');

		foreach ( $structure as $s_k => $s_v ):



		endforeach;

		return $if_valid();
	}
	// Alias to f.  array_structure_valid:
	function validate_array_structure ( $args... ) :? array { call_user_func_array([$this, 'array_structure_valid'], $args); }

}

*/