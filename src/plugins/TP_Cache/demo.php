<?php

require 'TP_Cache.php';

$cached = new TP_Cache(
	"TP_Cache_Demo",
	[
		'test_bool_false' => false,
		'test_float' => 23123.998,
		'test_multi_dim_array' => [
	  		'a' => [
	  			'a.a' => [
	  				'a.a.1' => 27
	  			]
	  		],
	  		'b' => [
	  			'b.a' => [
	  				'b.a.1' => 11
	  			]
	  		]
		]
	],
	'cache/TP_Cache_Demo',
	60*15
);

if($cached->is_available())
{
  echo $cached->output();
}
else
{
  $cached->ob_start();

?>

<html>
<body>
	This is a demo page. The current time is: <?=date('r');?>
</body>
</html>

<?php

  $cached->ob_save();
}
