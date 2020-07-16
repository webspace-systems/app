<?php

trait _template_003 {

	abstract function requested ( string $get_certain_key );
	
	abstract function get_url ( string $get_certain_key );

	var $template_render_components = [ 'txt', ...['a','abbr','address','area','article','aside','audio','b','base','bdi','bdo','blockquote','body','br','button','canvas','caption','cite','code','col','colgroup','data','datalist','dd','del','details','dfn','dialog','div','dl','dt','em','embed','fieldset','figcaption','figure','footer','form','h1','h2','h3','h4','h5','h6','head','header','hgroup','hr','html','i','iframe','img','input','ins','kbd','label','legend','li','link','main','map','mark','menu','meta','meter','nav','noscript','object','ol','optgroup','option','output','p','param','picture','pre','progress','q','rp','rt','ruby','s','samp','script','section','select','slot','small','source','span','strong','style','sub','summary','sup','table','tbody','td','template','textarea','tfoot','th','thead','time','title','tr','track','u','ul','var','video','wbr']];
	
	var $template_tags_no_content = [ 'meta', 'br', 'input', 'link' ];

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


	function template ( array $content, array $require_files = [] ) : void {


		$config = $this->template_config();


		if( $this->requested('template_render_component') )
		{

			echo $content;
		
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


			$Template_config = [ ];


			$doc = [

				['html', [

					['head', [

						...$meta_settings,

						['script', [ file_get_contents('plugins/asset_load_controller/script_built.js') ]],

						['script', 'window' => [

							'ASL' => 'new Asset_Load_Controller ( '.json_encode ( $ASL_config ).' )',
								
							'Template' => 'new (ASL.m("template")) ( '.json_encode( $this->template_config() ).' )',
						]]
					]],

					['body', 'class'=>"loading"]
				]]
			];

			$rendered = $this->template_render ( $doc );

			print is_array($rendered) ? implode("\n", $rendered) : $rendered;
		}
	}


	
	function template_render ( array $doc, int $depth = 0, array $path = [] ) : array {

		$html = [];

		foreach (  $doc  as  $k => $v )
		{
			
			if ( is_array ( $v ) && is_string ( $v[0] ) )
			{

				if ( in_array ( $v[0], $this->template_render_components ) )
				{
					$comp = $v[0];

					$props = array_slice ( $v, 1 );

					if ( class_exists ( $comp )  &&  method_exists ( $comp, 'render' ) )
					{
						if ( is_array( $rendered = $comp::render ( $props ) ) )
						{
							array_push ( $html, ...$rendered );
						}
						else
						{
							array_push ( $html, $rendered );
						}
					}
					else
					{
						if ( method_exists( $this, ( $tm = 'template_'.$comp.'_render' ) ) )
						{
							$res = $this->{$tm} ( $props );

							if ( is_array ( $res )  )
							{
								array_push ( $html, ...$res );
							}
							else
							{
								array_push ( $html, ...[$res] );
							}
						}
						else
						{
							array_push ( $html, ...$this->template_comp_render ( $comp, $props??[], $depth+1, [...$path, $comp] ) );
						}
					}
				}
				else
				{
					trigger_error('Unable to render: "'.print_r($v[0],true).'"', E_USER_ERROR);
				}

			}
			else
			{
				array_push ( $html, $v );
			}
		}

		return $html;
	}


	function template_comp_render ( string $comp, array $props, int $depth = 0, array $path = [] ) : array {

		$html = [];

		$padding = str_repeat ( ( $pad = '	' ), max ( $depth-1, 0 ) );

		$elem_html = '<' . $comp;
		
		$contents = [];

		foreach (  $props  as  $prop_key  =>  $prop_val  ):

			if ( is_numeric ( $prop_key ) )
			{

				if ( is_array ( $prop_val ) )
				{
					array_push ( $contents, ... $this->template_render ( $prop_val, $depth, [...$path, $prop_key] ) );
				}
				else
				{
					exit('GOT here... '.print_r(['$prop_key'=>$prop_key,'$prop_val'=>$prop_val,'$path'=>$path],true));
				}

			}
			else if ( is_array ( $prop_val ) )
			{
				array_push ( $contents, ... $this->template_render ( $prop_val, $depth, [...$path, $prop_key] ) );
			}
			else if ( is_string ( $prop_val ) )
			{
				$quote_char = '"';

				if ( is_array ( $prop_val ) )
				{
					exit(print_r(["GOT ARRAY ATTRIBUTE VALUE",$comp,$prop_key,$html],true));
				}

				if ( strpos("$prop_val", '"') !== false )
				{
					if ( strpos ( "$prop_val", "'" ) === false )
					{
						$quote_char = "'";
					}
					else if ( $config['html_attr_val_double_quotes_may_fallback_to'] )
					{
						$quote_char = '"';

						$prop_val = str_replace('"', $config['html_attr_val_double_quotes_may_fallback_to']??'“', $prop_val);
					}
					else
					{
						trigger_error("Unable to set value of attr. \"$prop_key\" as it contains both single and double quotes: $prop_val", E_USER_ERROR);
					}
				}


				$elem_html .= ' ' . $prop_key . '=' . $quote_char . $prop_val . $quote_char;
			}
			else
			{
				trigger_error (
					"Invalid type of attribute value: \"".gettype($prop_val)."\": \"".print_r($prop_val,1)."\""
				   . "given for attribute name '$prop_key' in \$props: ".json_encode($props),
				   E_USER_ERROR
				);
			}

		endforeach;


		if ( in_array ( $comp, $this->template_tags_no_content ) && ! $contents )
		{
			array_push ( $html, $elem_html.' />' );
		}
		else
		{
			switch ( sizeof ( $contents ) )
			{
				case 0:

					if ( in_array ( $comp, $this->template_tags_no_content ) )

						array_push ( $html, $elem_html.' />' );
					else
						array_push ( $html, $elem_html.'></'.$comp.'>' );
				break;

				case 1:

					array_push ( $html, $elem_html.'>'.$contents[0].'</'.$comp.'>' );
				break;
				
				default:

					array_push ( $html, $elem_html.'>' );

					foreach ( $contents as $content )
					{
						array_push ( $html, $pad . $content );
					}
		
					array_push ( $html, '</'.$comp.'>' );
				break;
			}
		}

		return array_map (  function( $v ) use ( $padding ) { return $padding . $v; },  $html  );
	}


	function _template_script_render ( array $props ) : array {

		$html = [];

		foreach ( $props as $prop  )
		{
			if ( is_array ( $prop ) )
			{
				array_push( $html, ...$this->template_script_render ( $prop ) );
			}
			else
			{
				array_push( $html, $prop );
			}
		}

		return $html;
	}
}
