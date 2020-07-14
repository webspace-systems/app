<?php

require 'tests.php';

new test (
	[
		'type' => 'method',
		'file' => 'router.php',
		'function' => 'router::route'
	],
	[
		[ 'parameters' => [''], 'correct_return_type' => 'NULL', 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['.'], 'correct_return_type' => 'NULL', 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['index'], 'correct_return_type' => 'NULL', 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['router'], 'correct_return_type' => 'NULL', 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['router.php'], 'correct_return_type' => 'NULL', 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['config'], 'correct_return_type' => 'NULL', 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['config.php'], 'correct_return_type' => 'NULL', 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['sql'], 'correct_return_type' => 'NULL', 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['sql.php'], 'correct_return_type' => 'NULL', 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['_sql'], 'correct_return_type' => 'string', 'must_result' => '_sql'  ]
	]
);

