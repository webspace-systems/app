<?php

trait _template_003 {

	// Requires available function on $this->...
	abstract function requested ( string $get_certain_key );
	abstract function get_url ( string $get_certain_key );

	var $template_config = [

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
		'html_tags_valid' => ['a','abbr','address','area','article','aside','audio','b','base','bdi','bdo','blockquote','body','br','button','canvas','caption','cite','code','col','colgroup','data','datalist','dd','del','details','dfn','dialog','div','dl','dt','em','embed','fieldset','figcaption','figure','footer','form','h1','h2','h3','h4','h5','h6','head','header','hgroup','hr','html','i','iframe','img','input','ins','kbd','label','legend','li','link','main','map','mark','menu','meta','meter','nav','noscript','object','ol','optgroup','option','output','p','param','picture','pre','progress','q','rp','rt','ruby','s','samp','script','section','select','slot','small','source','span','strong','style','sub','summary','sup','table','tbody','td','template','textarea','tfoot','th','thead','time','title','tr','track','u','ul','var','video','wbr'],

		'did_config' => false
	];

	var $template_require = 
		[
			['script', 'src'=>'js-utils/api_ajax.js'],
			['script', 'src'=>'js-utils/functions.js'],
			
			['script', 'src'=>'template.js'],
			['style', 'src'=>'template.css'],
		]
	;

	function template_config ( array $settings = null ) : array {

		if ( is_array ( $settings ) )
		{
			$this->template_config = array_merge(
				$this->template_config,
				$settings,
				[ 'did_config' => true ]
			);
		}
		
		if ( class_exists('config', false)
		  && ! $this->template_config['did_config']
		  && is_array ( $conf = config::get('template') ) 
		)
		{
			$this->template_config( $conf );
		}

		return $this->template_config;
	}


	function template_url () : string {

		return $this->get_url() . '/template/' . basename(__DIR__);
	}


	function template ( $to_be_wrapped, array $incl_template_require = [] ) : string {


		$config = $this->template_config();


		if( $this->requested('template_component') )
		{
			return $to_be_wrapped;
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

							'var ASL = new Asset_Load_Controller(',
							 [
								json_encode($ASL_config) . ',',
								json_encode($this->template_require) . ',',
								'"template.init"',
							 ],
							')'
						]]
					
					]],

					['body', 'class' => "loading", [

					]]
				]]
			];

			return $this->template_doc_render($doc);
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


			$e_str_contents = array_filter($e_contents, 'is_string');

			if( count($e_str_contents) === count($elem) && ! in_array($elem[0], $config['html_tags_valid']) )
			{
				foreach( $e_str_contents as $sc )
				{
					$html[] = $padding . $sc;
				}

				continue;
			}


			$e_name = $e_contents[0];

			$e_contents = array_slice($e_contents, 1);

			$e_path = [ ...$path, $e_name ];

			$e_attrs = array_filter($elem, 'is_string', ARRAY_FILTER_USE_KEY);



			if( $e_name == 'style' || $e_name == 'link' )
			{
				if( isset( $e_attrs['src'] ) && ( $e_attrs['href'] = $e_attrs['src'] ) )
				{
					unset( $e_attrs['src'] );
				}

				$e_name = isset ( $e_attrs['href'] ) ? 'link' : 'style';
			}



			$elem_html = $padding . '<' . $e_name;


			if( ! empty( $e_attrs ) )
			{
				foreach($e_attrs as $attr_name => $attr_value)
				{
					if( ! in_array( gettype( $attr_value ), $valid_types_of_attribute_value ) )
					{
						$error_level = E_USER_ERROR;
						trigger_error("Invalid type of attr. value: ".gettype($attr_value).": '".print_r($attr_value,1)."' given for attr. name '$attr_name' in \$elem: ".json_encode($elem), $error_level);
					}

					$quote_char = ( strpos($attr_value, '"') !== false ) ? "'" : '"';

					if( $quote_char == "'" && strpos($attr_value,"'") !== false )
					{
						$error_level = E_USER_ERROR;

						if($config['html_attr_val_double_quotes_may_fallback_to'])
						{
							$error_level = E_USER_NOTICE;

							$attr_value = str_replace('"', $config['html_attr_val_double_quotes_may_fallback_to']??'“', $attr_value);
						}

						trigger_error("Unable to set value value of attr. name '$attr_name', as it contains both single and double quotes: ".gettype($attr_value).": '$attr_value' given for attr. name '$attr_name' of \$elem: ".json_encode($elem), $error_level);
					}

					$elem_html .= ' ' . $attr_name . '=' . $quote_char . $attr_value . $quote_char;
				}
			}
			
			
			if ( in_array ( $e_name, $tags_no_content ) )
			{
				$elem_html .= ' />';

				$html[] = $elem_html;
			}
			else
			{
				if(!empty($e_contents))
				{

					$content = $this->template_doc_render($e_contents, $depth+1, $e_path, true);

					if ( sizeof($content) > 1)
					{
						$html[] = $elem_html . '>';

						foreach( $content as $cl )
						{
							$html[] = $cl;
						}

						$html[] = $padding . "</$e_name>";
					}
					else if ( isset ( $content[0] )  &&  is_string ( $content[0] ) )
					{
						$html[] = $elem_html . '>' . trim ( $content[0] ) . '</'.$e_name.'>';
					}
				}
				else
				{
					$html[] = $elem_html . '>' . "</$e_name>";
				}
			}
		}

		return $ret_array ? $html : implode( "\n", $html );
	}

}
