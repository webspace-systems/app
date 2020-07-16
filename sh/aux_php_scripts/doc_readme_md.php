<?php

$SRC_DIR = substr(__DIR__,0,strpos(__DIR__,'/sh/')) . '/src';

$TOKEN_SUCCESS = 'OK_ALL_GOOD';

$RULES = [

	'exclude_paths' => [

		'tests',

	],

	'include_paths' => [

		'template',

		'tests/tests.php',

	],

];


if
(
	(
		$result = 
		(
			$doc_readme_md = function ( string $path,  array & $rules,  bool $exec = false  ) {


				return true;
			}
		)
		( $SRC_DIR, $RULES )
	)
)
	
	echo $TOKEN_SUCCESS;
