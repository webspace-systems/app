<?php

class router {

	static function route(string $pathname, array $base_paths = [ __DIR__ ] ) :? string {

		$pathname = strtr(trim($pathname,'/'), ['//'=>'_','/'=>'_']);

		if(trait_exists($pathname,0))
		{
			return $pathname;
		}

		
		$paths = static::resolve_paths($pathname); // alt: preg_split('/[_,\/]+/', trim($pathname,'/'));


		$exclude_files = [
			__DIR__.'/index.php'
		];

		$included_files = get_included_files();

		foreach(static::methods() as $mn=>$method)
		{
			foreach($base_paths as $base_path)
			{
				if(($path = $method(rtrim($base_path,'/'), $paths)) && $path != __DIR__.'/index.php')
				{
					$path = realpath($path);

					if(!in_array($path, $exclude_files) && !in_array($path, $included_files))
					{
						try
						{
							include_once($path);

							$included_files[] = $path;
							
							if(class_exists($pathname,0) || trait_exists($pathname,0))
							{
								return $pathname;
							}
						}
						catch(Exception $e)
						{
							trigger_error('Failing attempt to include $path: '.$path, E_USER_NOTICE);
						}
					}
				}
			}
		}

		return null;
	}

	static function resolve_paths ( string $path, array $paths = [] ) : array {

		$depth = count($paths);

		preg_match('/[a-z]/i', $path, $first_letter, PREG_OFFSET_CAPTURE);

		$first_letter_i = isset($first_letter[0]) ? $first_letter[0][1] : 0;

		$last_letter_i = strpos($path, '_', $first_letter_i);

		if($last_letter_i <= 0)
		{
			$paths[] = $path;
		}
		else
		{
			$paths[] = substr($path, 0, $last_letter_i);

			$path = substr($path, $last_letter_i+1);

			return static::resolve_paths($path, $paths);
		}

		return $paths;
	}

	static function methods() : array {

		return [

			'exact.php' => function(string $base_path, array $paths) :? string {

				$test_path = $base_path.'/'.implode('/',$paths) . '.php';

				return file_exists($test_path) ? $test_path : null;
			},

			'index_popped' => function(string $base_path, array $paths) :? string {

				if(end($paths) != 'index') return null;

				array_pop($paths);

				$test_path = $base_path.'/'.implode('/', $paths).'/'.end($paths).'.php';

				return file_exists($test_path) ? $test_path : null;
			},

			'exact/lastname.php' => function(string $base_path, array $paths) :? string {

				$test_path = $base_path.'/'.implode('/', $paths).'/'.end($paths).'.php';

				return file_exists($test_path) ? $test_path : null;
			},

			'exact/2ndlastname.php' => function(string $base_path, array $paths) :? string {

				$test_path = $base_path.'/'.implode('/', $paths).'/ '.implode('/',array_slice($paths, -2,1)).'.php';

				return file_exists($test_path) ? $test_path : null;
			},

			'exact/trait.php' => function(string $base_path, array $paths)  :? string {

				if(substr($paths[0],0,1) != '_') return null;

				$paths[0] = substr($paths[0],1);

				$test_path = $base_path.'/'.implode('/',$paths).'/trait.php';

				return file_exists($test_path) ? $test_path : null;
			},

			'exact/2ndlastname.php trimmed' => function(string $base_path, array $paths) :? string {

				array_walk($paths, function(&$path, $k){ $path = trim($path, '_'); });

				$test_path = $base_path.'/'.implode('/', $paths).'/'.implode('/',array_slice($paths, -2,1)).'.php';

				return file_exists($test_path) ? $test_path : null;
			},

			/*
			'exact/model.php' => function(string $base_path, array $paths)  :? string {

				$test_path = $base_path.'/'.implode('/',$paths).'/model.php';

				return file_exists($test_path) ? $test_path : null;
			},
			*/
			

			'exact/index.php' => function(string $base_path, array $paths) :? string {

				$test_path = $base_path.'/'.implode('/',$paths).'/index.php';

				return file_exists($test_path) ? $test_path : null;
			},

			'exact/../index.php' => function(string $base_path, array $paths)  :? string {

				$test_path = $base_path.'/'.implode('/',array_slice($paths,0,-1)).'/index.php';

				return file_exists($test_path) ? $test_path : null;
			},
		];
	}
}

