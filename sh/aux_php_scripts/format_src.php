<?php


// Ajour this var if file is moved or it's parent dir. "sh" is renamed:

$src_dir = substr(__DIR__,0,strpos(__DIR__,'/sh/')) . '/src';

$SUCCESS_TOKEN = 'OK_ALL_GOOD';



// Idea: Automated fixing of formatting details in source files



$rules =
[

	'scope_start_line_break' =>	// Ensure  {  is only char in line
	[
		'in'	=>	[	'php',	'js'	],

		'test'	=>	function ( string $line ) : bool {		return strpos( $line, '{' );		},
	
		//'test' => ' strpos( $line, "{" ) ',
	
		'format'	=>	function (	string $line,	array $ctx,	array & $notes = []	)	:	bool	{

			if ( $ctx['line_length'] > 50 )
			{
				$line = strtr($line, [ "{" => "\n" . $ctx['indentation'] . "{" ]);
			}

			return $line;
		}
	],


	'scope_end_line_break' =>	// Ensure  }  is only char in line
	[
		'in'	=>	[	'php',	'js'	],

		'test'	=>	function ( string $line ) : bool {		return strpos( $line, '}' );		},
	
		'format'	=>	function (	string $line,	array $ctx,	array & $notes = []	)	:	bool	{

			if ( $ctx['line_length'] > 50 )
			{
				$line = strtr($line, [ "{" => "\n" . $ctx['indentation'] . "{" ]);
			}

			return $line;
		}
	],


	'notice_use_of_php_const_boolean' =>  // Note if word 'boolean' is seen in a php script
	[
		'in' => [ 'php' ],
		'test' => function ( string $line ) : bool {		return strpos ( $line, 'boolean' );		},
		'notice' => "Use of PHP constant 'boolean' is often mistaken for 'bool'"
	],

];


$search_types_in_filename_extensions =
[
	'.php' => [ 'php', 'html', 'js' ],
	'.phtml' =>  [ 'php', 'html', 'js' ],
	'.js' =>  [ 'php', 'html', 'js' ]
];



$result = 
(
	$funct_format_src = function ( string $path ) use ( $rules, $search_types_in_filename_extensions ) {

		$rules_in = array_unique(array_reduce($rules,function($c,$i){return array_merge($c, $i['in']);},[]));

		$result = 
		[
			'files_succeeded' => [],
			'files_failed' => [],
			'notes' => []
		];

		foreach ( ( $files = scandir( $path ) )  as  $filename )
		{
			if (( $_last_dot_pos = strrpos( $filename, "."))  >  0
			    &&
			    ( $type = substr($filename, $_last_dot_pos+1) )
				 &&
			      in_array( $type, $rules_in )
			)
			{
				$file_content = file_get_contents( rtrim($path,'/') . '/' . $filename );

				$lines_of_file = explode("\n", $file_content);

				$tokens_in_lines = array_fill_keys ( array_keys($lines_of_file), array() );

				foreach( token_get_all($file_content) as list( $token, $text, $line_num ) ) if ( $token )
				{
					array_push($tokens_in_lines[$line_num], $token);
				}
				// $line_tokens = array_reduce($file_tokens, function($c,$i){ exit(print_r([$c,$i])); return [ '' ]; }, [])

				$new_file_content_lines = [];

				$line_type = $type;

				foreach ( $lines_of_file as $line_num => $line )
				{
					foreach ( $rules as $rule_name => $rule )
					{
						$ctx = 
						[
							'line_num' => $line_num,
							'line_length' => strlen($line),
							'line_php_tokens' => $php_tokens_in_line[$line_num] ?? [],
							'line_prev' => $line[$line_num-1] ?? '',
							'line_next' => $line[$line_num+1] ?? '',
							'line_type' => $line_type,
						];


						if(($_php_open_tag_at = array_search( T_OPEN_TAG, $ctx['line_php_tokens'] ) ) )
						{
							$line_type = 'php';

							if ( $_php_open_tag_at !== 0 )
							{
								$result['notes'][] = ['T_OPEN_TAG not 1st token in line',$ctx];
							}
						}


						$cond_funct = is_string($rule['test']) ? eval($rule['test']) : $rule['test'];

						if (  $cond_funct ( $line )  ===  true  )
						{


							$indent_length = $ctx['line_length'] - strlen(ltrim($line));

							$indentation = str_split(substr($line, 0, $indent_length));

							print_r(['$indentation'=>$indentation,'$line'=>$line,'$ctx'=>$ctx]);
							exit;

							$indent_tabs = strlen($_lts=ltrim($line,' ')) - strlen( ltrim ( $_lts, '	' ) );
							$indent_spaces = strlen($_ltt=ltrim($line,'	')) - strlen( ltrim ( $_ltt, ' ' ) );

							$indent = substr($line, 0, $indent_length);


							$ctx['indent'] = substr($line, 0, $indent_length);
							$ctx['indent_length'] = $indent_length;
							$ctx['indent_length'] = $indent_length;

							print_r($ctx);
							exit("\n\n\n\nSICK\n\n\n\n\n\n\n\n");


							if ( isset ( $rule['notice'] ) )
							{
								$notes = is_function($rule['notice']) ? $rule['notice']( $line, $ctx, $result['notes'] ) : $rule['notice'];

								foreach( is_array($notes) ? $notes : []  as  $k => $v  )
								{

									if ( stripos($line, 'notice:') === false )
									{
										$line = 'Notice: ' . $line;
									}	
								}
							}

							if ( is_function ( $rule['format'] ) )
							{
								$line = $rule['format']( $line, $ctx );									
							}


							print("\n\n\n\n\n\n['$line']  $indent_spaces vs tabs: $indent_tabs \n".print_r($indentation,true));
							
							exit("\n\n\n\n\n\n - $rule_name - --;");

						}

						$new_file_content_lines[] = $line;
					}
				}
			}
		}

		return $result;
	}
)
( $src_dir );


if ( ! empty ( $result['failed'] ) )

	die ( print_r ( $result, true ) );

else

	echo $SUCCESS_TOKEN;


