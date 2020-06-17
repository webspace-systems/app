<?php

include_once __DIR__.'/../src/router.php';

spl_autoload_register(['router','route']);

$in_out_expectations = [

	'' => null,
	'.' => null,
	'index' => null,
	'router' => null,
	'sql' => null,
	'_sql' => '_sql'
];

foreach($in_out_expectations as $in=>$out_expected)
{
	if( router::route($in) != $out_expected )
	{
		exit('Failed at "'.$in.'"');
	}
}

echo "OK";
