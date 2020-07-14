<?php

include_once 'error/trait.php';

class router {

	static public array $base_paths;

	static public bool  $debug_mode = true;

	static public array $accept_as_response = [ 'class'=> 1, 'trait'=> 1, 'interface'=> 1 ];

	static public array $count_loops_per_request = [ ];

	// To-do:
	// static public array $indexed_paths_to_names_of_types = [ ];

	static public string $debug_path = 'singleton';
	static public int $debug_skip_occourences = 1;

	static public int $count_requests = 0;

	static function route ( string $pathname ) :? string {

		static::$count_requests = static::$count_requests+1;

		if(static::$debug_path == $pathname && static::$debug_skip_occourences)
		{
			static::$debug_skip_occourences--;
		}


		$pathname = strtr( trim($pathname,'/'),  ['//'=>'_', '/'=>'_'] );
		
		$paths = static::resolve_paths($pathname); // alt: preg_split('/[_,\/]+/', trim($pathname,'/'));


		$included_files = get_included_files();

		$exclude_files = [ __DIR__."/entry.php" ];


		$search_types = array_keys( array_filter( static::$accept_as_response ) );

		$search_names = [ $pathname ];


		$count_loops = 0;

		foreach ( static::search_methods() as $n => $sm )
		{

			foreach ( static::$base_paths ?? [ __DIR__ ] as $base_path )
			{

				if (  ( $sm_suggested = $sm ( $pathname, rtrim($base_path,'/'), $paths ) )
				   && ( $fp = realpath(  $sm_suggested[0] ?? $sm_suggested['filepath'] ?? $sm_suggested['fp']  ) )
				   && !in_array($fp, $exclude_files )
				   && !in_array($fp, $included_files )
				)
				{
					include_once $fp;
					
					$included_files[] = $fp;

					if ( ( $sm_suggested_name = $sm_suggested[1] ?? null )
					     && $sm_suggested_name != $pathname
					     &&
					     ! (
					        ( $p_n_idx = array_search($sm_suggested_name, $search_names) ) !== false
					        && !empty( ( $search_names = array_splice($search_names, 0, 0, array_splice($search_names, $p_n_idx, 1)) ) )
					     )
					)
						array_unshift($search_names, $sm_suggested_name);


					if( ( $sug_type = $sm_suggested[2] ?? null )
					   && $sug_type != $pathname
					   &&
					   ! (
					       ( $p_n_idx = array_search($sug_type, $search_types) ) !== false
					    && ( $search_types = array_splice($search_types, 0, 0, array_splice($search_types, $p_n_idx, 1)) )
					   )
					)
						array_unshift($search_types, $sug_type);


					foreach ( $search_names as $name )
					{

						foreach ( $search_types as $type ) 
						{
							
							$count_loops++;

							if( ( $type == 'function' && call_user_func_array( $type.'_exists', [ $name, false ] ) )
								||
									call_user_func_array( $type.'_exists', [ $name, false ] )
							)
							{
								static::$count_loops_per_request[] = [ $pathname, $count_loops, $name, $type ];

								return $name;
							}
						}
					}
				}
			}
		}

		return null;
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


	static function route_debug ( string $path ) {

		static::$debug_path = $path;

		$result = static::route( $path );
	}


	static function search_methods() : array {

		return
		[
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
