<?php

$SRC_DIR = substr(__DIR__,0,strpos(__DIR__,'/sh/')) . '/src';

$TOKEN_SUCCESS = 'OK_ALL_GOOD';

$EXEC_FORMAT_FIX = ( in_array( 'e', $argv ) || $_REQUEST['e'] ) ? true : false;

$RULES =
[

	'scope_start_line_break' =>	// Ensure  {  is only char in line
	[
		'in'	=>	[	'php',	'js'	],

		'test'	=>	function ( string $line, array $ctx ) : bool {		return strpos( $line, '{' );		},
	
		//'test' => ' strpos( $line, "{" ) ',
	
		'format'	=>	function (	string $line,	array $ctx	)	:	string	{

			if ( $ctx['line_len'] > 50 )
			{
				return strtr( $line, [  "{"  =>  "\n" . $ctx['line_indent'] . "{" ]);
			}
		}
	],


	'scope_end_line_break' =>	// Ensure  }  is only char in line
	[
		'in'	=>	[	'php',	'js'	],

		'test'	=>	function ( string $line, array $ctx ) : bool {		return strpos( $line, '}' );		},
	
		'format'	=>	function (	string $line,	array $ctx	)	:	string	{

			if ( $ctx['line_len'] > 50 )
			{
				return strtr( $line, [  "{"  =>  "\n" . $ctx['line_indent'] . "}" ]);
			}
		}
	],


	'notice_use_of_php_const_boolean' =>  // Note if word 'boolean' is seen in a php script
	[
		'in' => [ 'php' ],
		'test' => function ( string $line, array $ctx ) : bool {		return strpos ( $line, 'boolean' );		},
		'notice' => "Use of PHP constant 'boolean' is often mistaken for 'bool'"
	],

];


$result = 

 (

	$funct_format_src = function ( string $path, array & $rules, bool $exec = false ) {

		$rules_in = array_unique(array_reduce($RULES,function($c,$i){return array_merge($c, $i['in']);},[]));

		$all_good = [];
		
		$formatting_proposition = [];
		
		$notes = [];

		
		foreach (  scandir( $path )  as  $file_name  ):

			if (( $_last_dot_pos = strrpos( $file_name, "."))  >  0
			    &&
			    ( $type = substr($file_name, $_last_dot_pos+1) )
				 &&
			      in_array( $type, $rules_in )
			):

				$file_all_good = true;

				$to_format = [];

				$did_format = [];


				$file_path = rtrim($path,'/') . '/' . $file_name;

				$file_content = file_get_contents( $file_path );

				$file_lines = explode("\n", $file_content);


				$tokens_in_lines = array_fill_keys ( array_keys($file_lines), array() );

				foreach( token_get_all($file_content) as list( $token, $text, $line_num ) ) if ( $token )
				{
					array_push($tokens_in_lines[$line_num], $token);
				}


				$new_file_content_lines = [];

				$line_type = $type;


				foreach ( $file_lines  as  $line_num  =>  $line  ):


					$line_len = strlen( $line );


					foreach (  $RULES  as  $rule_name  =>  $rule ):


						$ctx = 
						[
							'line_num' => $line_num,
							'line_len' => $line_len,
							'line_php_tokens' => $php_tokens_in_line[$line_num] ?? [],
							'line_prev' => $line[$line_num-1] ?? '',
							'line_next' => $line[$line_num+1] ?? '',
							'line_type' => $line_type,
							'line_indent' => substr($line, 0, $line_len-strlen(ltrim($line)))
						];



						if(($_php_open_tag_at = array_search( T_OPEN_TAG, $ctx['line_php_tokens'] ) ) )
						{
							$line_type = 'php';

							if ( $_php_open_tag_at !== 0 )
							{
								$notes[] = ['T_OPEN_TAG not 1st token in line',$ctx];
							}
						}



						$test = is_string($rule['test'])  ?  eval ( $rule['test'] )  :  $rule['test'] ( $line, $ctx );


						if ( $test === true ):


							if ( isset ( $rule['notice'] ) && ( $_notice_how = gettype($rule['notice']) )
								  &&
									! (
										 in_array( $_notice_how, [ 'function', 'object'] )
									  &&
										  array_push( $notes, [  $rule['notice']( $line, $ctx ),  $ctx ])
									)
								  &&
									! (
										  in_array( $_notice_how, [ 'string', 'array' ] )
										&&
										   array_push( $notes, [  $rule['notice'],  $ctx ])
									)
							)
								array_push( $notes, [ 'Unexpected type of $rule["notice"]: "'.$_notice_how.'"', $ctx ]);



							if ( in_array( gettype($rule['format']), ['function','object'] )
								&&
								 ( $_line_formatted = $rule['format']( $line, $ctx ) )
								&&
									! empty ( $_line_formatted )
								&&
									$_line_formatted !== $line
							):

								$file_all_good = false;

								$format = 
								[
									$file_path . ':' . $line_num,
									$rule_name,
									strtr( $line, "\n", '\\'.' n'),
									...explode( "\n", $_line_formatted )
								];

								if ( $exec ):

									$line = (array) $_line_formatted;

									$did_format[] = $format;

								else:

									$formatting_proposition[] = $format;

								endif;

							
							endif; // $_line_formatted
						

						endif; // $test === true


						$new_file_content_lines[] = $line;


						if ( $exec ):


							// Save proposed..?
						
						endif; // $exec


					endforeach; // $rule_name => $rule

				endforeach; // $line_num => $line


				if ( $file_all_good ):


					$all_good[] = $file_path;

				
				endif;

			endif; // valid file ext or found type

		endforeach; // scandir( $path )  as  $file_name 


		return compact ( 'all_good', 'formatting_proposition', 'notes' );
	}
 )
 ( $SRC_DIR, $RULES, $EXEC_FORMAT_FIX )
;


if ( empty ( $result['formatting_proposition'] ) )

	echo $TOKEN_SUCCESS;

else

	die ( print_r ( [ 'formatting_proposed' => current($result['formatting_proposition']) ], true ) );
