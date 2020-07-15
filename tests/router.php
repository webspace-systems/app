<?php

require 'tests.php';

new test (
	[
		'type' => 'method',
		'file' => 'router.php',
		'function' => 'router::route'
	],
	[
		[ 'parameters' => [''], 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['.'], 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['index'], 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['router'], 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['router.php'], 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['config'], 'must_result' => [ 'string', 'config' ]  ],
		[ 'parameters' => ['config.php'], 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['sql'], 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['sql.php'], 'must_result' => [ 'null', null ]  ],
		[ 'parameters' => ['_sql'], 'must_result' => [ 'string', '_sql' ]  ]
	]
);

