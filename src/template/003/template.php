<?php

trait _template_003 {


	// Methods required available on $this

	abstract function requested ( string $get_certain_key );
	
	abstract function get_url ( string $get_certain_key );


	// Config by  $this->template_config ( array $config )  or custom logic in this function

	var $_template_config = [

		'meta' => [
			'title' => 'Untitled',
			'charset' => 'utf-8',
			'auther' => '',
			'description' => '..',
			'viewport' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'
		],

		'ASL' => [

		],

		'html_attr_val_double_quotes_may_fallback_to' => '“', // str/char for fallback || bool false to error

		'did_config' => false
	];


	// Error if unable to load required js & css

	var $_template_require = 
		[
			['script', 'src'=>'js-utils/api_ajax.js'],
			['script', 'src'=>'js-utils/functions.js'],
			
			['script', 'src'=>'template.js'],
			['style', 'src'=>'template.css'],
		]
	;


	var $html_tags_valid = ['a','abbr','address','area','article','aside','audio','b','base','bdi','bdo','blockquote','body','br','button','canvas','caption','cite','code','col','colgroup','data','datalist','dd','del','details','dfn','dialog','div','dl','dt','em','embed','fieldset','figcaption','figure','footer','form','h1','h2','h3','h4','h5','h6','head','header','hgroup','hr','html','i','iframe','img','input','ins','kbd','label','legend','li','link','main','map','mark','menu','meta','meter','nav','noscript','object','ol','optgroup','option','output','p','param','picture','pre','progress','q','rp','rt','ruby','s','samp','script','section','select','slot','small','source','span','strong','style','sub','summary','sup','table','tbody','td','template','textarea','tfoot','th','thead','time','title','tr','track','u','ul','var','video','wbr'];


	function template_config ( $set_array_or_get_str_key = null ) : array {

		if ( is_array ( $settings = $set_array_or_get_str_key ) )
		{
			$this->_template_config = array_merge(
				$this->_template_config,
				$settings,
				[ 'did_config' => true ]
			);
		}
		else if ( is_string( $key = $set_array_or_get_str_key ) )
		{
			return $this->_template_config[ $key ];
		}
		
		if ( ! $this->_template_config['did_config']
		   &&
		      class_exists( 'config', false )
		   &&
		      is_array ( $conf = config::get ( 'template' ) ) 
		)
		{
			$this->template_config( $conf );
		}

		return $this->_template_config;
	}


	function template_url () : string {

		return $this->get_url() . '/template/' . basename(__DIR__);
	}


	function template ( array $content, array $require_files = [] ) : string {

		$config = $this->template_config();

		if( $this->requested('template_component') )
		{

			return $content;
		
		}
		else
		{

			$meta_settings = [];

			foreach($config['meta'] as $k=>$v)
			{
				if( $k == 'title' )
				{
					$meta_settings[] = ['title', [ $v ]];
				}
				else
				{
					$meta_settings[] = ['meta', 'name'=>$k, 'content'=>$v];
				}
			}


			$ASL_config = array_merge([ 'base_url'=> $this->template_url() ], $config['ASL']??[]);


			$doc = [

				['html', [

					['head', [

						...$meta_settings,

						['script', [

							file_get_contents('plugins/asset_load_controller/script_built.js'),

							'var ASL = new Asset_Load_Controller (',
								json_encode($ASL_config) . ',',
								json_encode($this->_template_require) . ',',
								'`',
									'template.init ( '.json_encode($this->template_config()).', () => {',

										'console.log("LOADING COMPLETE")',

									'})',
								'`',
							')',
						]]
					]],

					['body', 'class'=>"loading"]
				]]
			];

			return $this->template_render($doc);
		}
	}


	function doc ( ...$args ) {

		return $this->template(...$args);
	}


	function docf ( string $filepath ) : string {

		if(substr($filepath,0,4) != substr(__DIR__,0,4) && substr($filepath,0,1)!='/')
		{
			$filepath = dirname(debug_backtrace()[0]['file']) . '/' . $filepath;
		}

		ob_start();

		include_once $filepath;

		return $this->doc( ob_get_clean() );
	}

	/*
	function template_doc_render ( array $doc, int $depth = 0, array $path = [], bool $ret_array = false ) {

		$config = $this->template_config();

		if ( is_string ( $doc[0] ) )
		{
			$doc = [$doc];
		}
		else if ( isset( $doc[0] ) && sizeof($doc) == 1 && is_array($doc[0]) && isset($doc[0][0]) && is_array($doc[0][0]) )
		{
			$doc = $doc[0];
		}

		$html = [];


		$padding = str_repeat("	", $depth);

		$tags_no_content = [ 'meta', 'br', 'input', 'link' ];

		$valid_types_of_attribute_value = [ 'string', 'integer', 'double', 'boolean' ];


		foreach( $doc as $dk => $elem )
		{

			if( is_string ( $elem ) )
			{
				$html[] = $padding . $elem;

				continue;
			}

			if( ! is_array( $elem ) )
			{
				trigger_error("Invalid \$doc \$elem; must be string or array; Got type: '".gettype($elem)."'; ".print_r($elem,1), E_USER_WARNING);
			}


			if ( sizeof ( $elem ) == 0
			    || ( ! is_string( $elem[0] ) && trigger_error("Invalid \$doc \$elem; Missing tag name at index/key 0 in given \$elem: ".print_r($doc,1), E_USER_ERROR) )
			)
				continue;


			$e_contents = array_filter($elem, 'is_numeric', ARRAY_FILTER_USE_KEY);

			if( empty( $e_contents ) )
			{
				continue;
			}


			$module = [
				'name' => get_class($this),
				'contents' => $content,
				'require_files' => $require_files
			];

			$e_str_contents = array_filter($e_contents, 'is_string');

			if( count($e_str_contents) === count($elem) && ! in_array($elem[0], $this->html_tags_valid) )
			{
				foreach( $e_str_contents as $sc )
				{
					$html[] = $padding . $sc;
				}
						file_get_contents('plugins/asset_load_controller/script_built.js'),

						'var ASL = new Asset_Load_Controller (',
							json_encode($ASL_config) . ',',
							json_encode($this->_template_require) . ',',
							'`template.init ( ',
								json_encode($this->template_config()).', ',
								json_encode($module)
							.')`',
						')'
					]]
				]],

				['body', 'class'=>"loading"]
			]]
		];

		return $this->template_render($doc);
	}
	*/
	
	function template_render ( array $doc, int $depth = 0, array $path = [] ) {


		$config = $this->template_config();


		if (
			  is_string ( $doc[0] )
		)
		{
			$doc = [$doc];
		}
		else
		if (
			  isset ( $doc[0] )
		   &&
		     sizeof ( $doc ) == 1
		   &&
		     is_array ( $doc[0] )
		   &&
		     isset ( $doc[0][0] )
		   &&
		     is_array ( $doc[0][0] )
		)
		{
			$doc = $doc[0];
		}



		$html = [];



		$padding = str_repeat("	", $depth);


		$tags_no_content = [ 'meta', 'br', 'input', 'link' ];


		$valid_types_of_attribute_value = [ 'string', 'integer', 'double', 'boolean' ];




		foreach( $doc as $dk => $elem ):



			if(  is_string ( $elem )  )
			{

				$html[] = $padding . $elem;


				continue;
			}



			if(  ! is_array( $elem )  )
			{
				trigger_error("Invalid \$doc \$elem; must be string or array; Got type: '".gettype($elem)."'; ".print_r($elem,1), E_USER_WARNING );
			}



			if (  sizeof ( $elem ) == 0  )
			{

				if (  ! is_string( $elem[0] )  )
				{
					trigger_error("Invalid \$doc \$elem; Missing tag name at index/key 0 in given \$elem: ".print_r($doc,1), E_USER_ERROR );
				}


				continue;

			}




			$e_contents = array_filter($elem, 'is_numeric', ARRAY_FILTER_USE_KEY);



			if ( empty ( $e_contents ) )
			{
				continue;
			}




			$e_str_contents = array_filter($e_contents, 'is_string');




			if (  count ( $e_str_contents )  ===  count ( $elem )
				&&
				   ! in_array ( $elem[0],  $this->html_tags_valid )
			)
			{
				foreach (  $e_str_contents  as  $sc  )
				{

					$html[] = $padding . $sc;
				
				}


				continue;
			}




			$e_name = $e_contents[0];

			$e_contents = array_slice($e_contents, 1);

			$e_path = [ ...$path, $e_name ];

			$e_attrs = array_filter($elem, 'is_string', ARRAY_FILTER_USE_KEY);



			
			// Correct tag attribute 'link' attr. 'src' > 'href'

			// Correct tag name 'style' > 'link' if has attr 'href'

			if ( in_array (  $e_name,  [  'style',  'link'  ] ) ):

				if ( isset ( $e_attrs['src'] ) ):

					$e_attrs['href'] = $e_attrs['src'];

					unset ( $e_attrs['src'] );
				
				endif;

				if ( isset ( $e_attrs['href'] ) ):

					$e_name = 'link';

				endif;

			endif;




			$elem_html = $padding . '<' . $e_name;



			foreach (  $e_attrs  as  $attr_name  =>  $attr_value  ):


				if(  ! in_array ( gettype( $attr_value ), $valid_types_of_attribute_value ) ):

					trigger_error("Invalid type of attr. value: ".gettype($attr_value).": '".print_r($attr_value,1)."' given for attr. name '$attr_name' in \$elem: ".json_encode($elem), E_USER_ERROR);

				endif;


				$quote_char = ( strpos($attr_value, '"') !== false ) ? "'" : '"';

				if (
					  $quote_char == "'"
					&& 
					  strpos ( $attr_value, "'" ) !== false
				)
				{
					if($config['html_attr_val_double_quotes_may_fallback_to'])
					{
						$attr_value = str_replace('"', $config['html_attr_val_double_quotes_may_fallback_to']??'“', $attr_value);
					}

					trigger_error("Unable to set value value of attr. name '$attr_name', as it contains both single and double quotes: ".gettype($attr_value).": '$attr_value' given for attr. name '$attr_name' of \$elem: ".json_encode($elem), E_USER_ERROR);
				}


				$elem_html .= ' ' . $attr_name . '=' . $quote_char . $attr_value . $quote_char;


			endforeach; //  $e_attrs  as  $attr_name  =>  $attr_value

			
			


			if ( in_array ( $e_name, $tags_no_content ) ):



				$elem_html .= ' />';


				$html[] = $elem_html;



			else:



				if ( ! empty ( $e_contents ) ):



					$content = $this->template_render (  $e_contents,  $depth+1,  $e_path,  true  );



					if (  sizeof ( $content ) > 1  ):


						$html[] = $elem_html . '>';


						foreach (  $content  as  $cl  ):


							$html[] = $cl;

						endforeach;


						$html[] = $padding . "</$e_name>";


					else
					if (
						  isset ( $content[0] )
						&&
						  is_string ( $content[0] )
					):


						$html[] = $elem_html . '>' . trim ( $content[0] ) . '</'.$e_name.'>';


					else:

						trigger_error ( 'What else ?', E_USER_WARNING );

					endif;


				
				else:

				
					$html[] = $elem_html . '>' . "</$e_name>";
				

				endif; // ! empty ( $e_contents )




			endif; // in_array ( $e_name, $tags_no_content )
		


		endforeach; //  $doc  as  $dk  =>  $elem



		return $html;
	}


}
