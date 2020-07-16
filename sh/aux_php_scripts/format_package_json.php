<?php


$file_content = file_get_contents('package.json');

function apply_respect_distance_for_the_main_keys ( string $file_content ) {

	$content_per_line = array_values(array_filter(array_filter(explode("\n", $file_content),'trim')));

	$lines_count = count($content_per_line);

	$new_content_per_line = [];

	$one_tab = strpos($content_per_line[1], '"');

	if ( ! $one_tab )
	{
		throw new ErrorException('empty($one_tab). $file_content being: '.print_r($file_content,true));
		return;
	}


	foreach ( $content_per_line as $ln => $line_content )
	{
		if ( $ln > 2 && strpos($line_content,'"')/$one_tab == 1  )
		{
			$new_content_per_line[] = '';
			$new_content_per_line[] = $line_content;
		}
		else
		{
			$new_content_per_line[] = $line_content;
		}
	}

	$new_content_per_line[] = '';

	return implode("\n", $new_content_per_line);
}

$file_content = apply_respect_distance_for_the_main_keys( $file_content );

file_put_contents('package.json', $file_content) or die('Unable to write to file: package.json');

echo "OK_ALL_GOOD";
