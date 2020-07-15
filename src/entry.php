<?php

require 'router.php';

spl_autoload_register(['router','route']);

router::$base_paths = config::get( 'router', 'root_paths', true );

set_error_handler(['_error','_on_error']);
register_shutdown_function(['_error','_on_shutdown']);


$path = trim( urldecode($_SERVER['REQUEST_URI']), '/');

$path = substr($path, 0, strpos($path.'?', '?')); // removes url params

$url_subdir = trim(substr(__DIR__,strlen($_SERVER['DOCUMENT_ROOT'])),'/');

if($url_subdir && substr($path,0,strlen($url_subdir)) == $url_subdir)
{
	// Cut to relative path if in sub-directory to the base-url, e.g. localhost/platform
	$path = trim(substr($path,strlen($url_subdir)),'/');
}


$route_path = $path  ?:  config::get('router','frontpage')  ?:  'public';

$route = router::route( $route_path );


if ( class_exists($route) )
{
	$ci = new $route();

	foreach([ $_SERVER['REQUEST_METHOD'], 'index' ] as $m)
	{
		if( method_exists($ci, $m) ) exit( call_user_func([$ci, $m]) );
	}

	app::_error('Not implemented', [$path, $route, $_SERVER['REQUEST_METHOD']], 501, true);
}
else
{
	app::_error('Not found: '.$route_path, [ '$route'=>$route, '$path'=>$path ], 404, true);
}
