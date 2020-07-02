<?php

include_once 'error/trait.php';

class router {

	static public $accept_as_response = [ 'class'=> 1, 'trait'=> 1, 'interface'=> 1 ];

	static public $test_mode = true;

	static function get_existing_by_name_per_type ( string $name ) : array {

		$exist_type_names = [ $name => [] ];

		foreach(static::$accept_as_response as $type => $do)
		{
			if(call_user_func($type.'_exists', $name, 0))
			{
				array_push($exist_type_names[$name], $type);
			}
		}

		return $exist_type_names;
	}

	static function route(string $pathname, array $base_paths = [ __DIR__ ] ) :? string {

		$pathname = strtr( trim($pathname,'/'),  ['//'=>'_', '/'=>'_'] );
		
		$paths = static::resolve_paths($pathname); // alt: preg_split('/[_,\/]+/', trim($pathname,'/'));

		$exclude_paths = [
			__DIR__,
			__DIR__.'/entry.php',
			__DIR__.'/index.php'
		];

		$included_files = null;

		$search_types = array_keys(array_filter(static::$accept_as_response));

		$search_names = [ $pathname, 'Tester2' ];

		$exist_type_names = static::get_existing_by_name_per_type( $pathname );

		$found_name = null;

		$found_names = [];

		foreach(static::search_methods() as $mn=>$search_m)
		{
			foreach($base_paths as $base_path)
			{
				if( ( $find = $search_m ($pathname, rtrim($base_path,'/'), $paths) ) )
				{
					$sm_proposed_name = ( $_1 = ($find[1] ?? $find['name']) ) != $pathname ? $_1 : null;
					$sm_proposed_type = ( $_1 = ($find[2] ?? $find['type']) ) != $type ? $_1 : null;
					$sm_found_filepath = realpath( is_string($find) ? $find : ( $find[0] ?? $find['path'] ) );


					if( in_array( $sm_found_filepath, $exclude_paths )
						 or
						 in_array( $sm_found_filepath, $included_files ?? ( $included_files = get_included_files() ) )
					)
						continue;


					if($sm_proposed_name) // Prioritize proposed
					{
						if( ( $p_n_idx = array_search($sm_proposed_name, $search_names) ) !== false )
						{
							unset($search_names[$p_n_idx]);

							exit('r');

							$out = array_splice($array, $a, 1);
							array_splice($array, $b, 0, $out);
						}

						array_unshift($search_names, $sm_proposed_name);
					}

					if( $sm_proposed_type ) // Prioritize proposed
					{
						if( ( $p_t_idx = array_search($sm_proposed_type, $search_types) ) !== false )
						{
							unset($search_types[$p_t_idx]);
						}

						array_unshift($search_types, $sm_proposed_type);
					}


					try
					{
						ob_start();

						include_once ( $sm_found_filepath );

						$incl_output = ob_get_clean();
						
						$included_files[] = $sm_found_filepath;


						$combos = [];

						foreach($search_names as $pni => $name)
						{
							foreach($search_types as $type)
							{
								$combos[] = [$name, $type];
							}
						}

						foreach($combos as list($name, $type))
						{
							if(!in_array($type, $exist_type_names[$name]??[]) && call_user_func($type.'_exists', $name, 0))
							{
								$found_name = 
								$found_names[] = $name;
								$exist_type_names[$name][] = $type;

								if(!static::$test_mode)
								{
									break;
								}
							}
						}

						if($found_name && !static::$test_mode)
						{
							break;
						}
					}
					catch(Exception $e)
					{
						trigger_error('Failing attempt to include $sm_found_filepath: '.$sm_found_filepath, E_USER_NOTICE);
						continue;
					}
				}
			}

			if($found_name && !static::$test_mode) break;
		}

		return $found_name ?? $incl_output ?? null;
	}


	static function resolve_paths ( string $path, array $paths = [] ) : array {

		preg_match('/[a-z]/i', $path, $first_letter, PREG_OFFSET_CAPTURE);

		$first_letter_i = isset($first_letter[0]) ? $first_letter[0][1] : 0;

		if( ( $last_letter_i = strpos($path, '_', $first_letter_i) ) > 0 )
		{
			$paths[] = substr($path, 0, $last_letter_i);

			return static::resolve_paths( substr($path, $last_letter_i+1), $paths);
		}

		return array_merge( $paths, [ $path ] );
	}


	static function search_methods() : array {

		return [

			'exact.php' => function(string $pathname, string $base_path, array $paths) :? array {

				$test_path = $base_path.'/'.implode('/',$paths) . '.php';

				return file_exists($test_path) ? [ $test_path, $pathname ] : null;
			},

			'index popped' => function(string $pathname, string $base_path, array $paths) :? array {

				if(end($paths) != 'index') return null;

				array_pop($paths);

				$test_path = $base_path.'/'.implode('/', $paths).'/'.end($paths).'.php';

				return file_exists($test_path) ? [ $test_path, $pathname ] : null;
			},

			'exact/lastname.php' => function(string $pathname, string $base_path, array $paths) :? array {

				$test_path = $base_path.'/'.implode('/', $paths).'/'.end($paths).'.php';

				return file_exists($test_path) ? [ $test_path, $pathname ] : null;
			},

			'exact/2ndlastname.php' => function(string $pathname, string $base_path, array $paths) :? array {

				$test_path = $base_path.'/'.implode('/', $paths).'/ '.implode('/',array_slice($paths, -2,1)).'.php';

				return file_exists($test_path) ? [ $test_path, $pathname ] : null;
			},

			'exact/trait.php' => function(string $pathname, string $base_path, array $paths) :? array {

				if(substr($paths[0],0,1) != '_') return null;

				$paths[0] = substr($paths[0],1);

				$test_path = $base_path.'/'.implode('/',$paths).'/trait.php';

				return file_exists($test_path) ? [ $test_path, $pathname ] : null;
			},

			'exact/2ndlastname.php trimmed' => function(string $pathname, string $base_path, array $paths) :? array {

				array_walk($paths, function(&$path, $k){ $path = trim($path, '_'); });

				$test_path = $base_path.'/'.implode('/', $paths).'/'.implode('/',array_slice($paths, -2,1)).'.php';

				return file_exists($test_path) ? [ $test_path, $pathname ] : null;
			},

			'exact/index.php' => function(string $pathname, string $base_path, array $paths) :? array {

				$test_path = $base_path.'/'.implode('/',$paths).'/index.php';

				$pathname = rtrim($pathname, '_index') . '_index';

				return file_exists($test_path) ? [ $test_path, $pathname, 'class' ] : null;
			},

			'exact/../index.php' => function(string $pathname, string $base_path, array $paths) :? array {

				$test_path = $base_path.'/'.implode('/',array_slice($paths,0,-1)).'/index.php';

				return file_exists($test_path) ? [ $test_path, $pathname ] : null;
			},
		];
	}

}

