<?php

trait _template_003 {

	// Requires available function on $this->...
	abstract function requested ( string $get_certain_key );
	abstract function get_url ( string $get_certain_key );


	var $require_load_files = 
		[
			['script', 'src'=>'js-utils/api_ajax.js'],
			['script', 'src'=>'js-utils/functions.js'],
			
			['script', 'src'=>'template.js'],
			['style', 'src'=>'template.css'],
		]
	;

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
		'html_tags_valid' => ['a','abbr','address','area','article','aside','audio','b','base','bdi','bdo','blockquote','body','br','button','canvas','caption','cite','code','col','colgroup','data','datalist','dd','del','details','dfn','dialog','div','dl','dt','em','embed','fieldset','figcaption','figure','footer','form','h1','h2','h3','h4','h5','h6','head','header','hgroup','hr','html','i','iframe','img','input','ins','kbd','label','legend','li','link','main','map','mark','menu','meta','meter','nav','noscript','object','ol','optgroup','option','output','p','param','picture','pre','progress','q','rp','rt','ruby','s','samp','script','section','select','slot','small','source','span','strong','style','sub','summary','sup','table','tbody','td','template','textarea','tfoot','th','thead','time','title','tr','track','u','ul','var','video','wbr'],

		'did_config' => false
	];

	function template_url () : string {

		return $this->get_url() . '/template/' . basename(__DIR__);
	}

	function template_config ( array $settings = null ) : array {

		if ( is_array ( $settings ) )
		{
			$this->_template_config = array_merge(
				$this->_template_config,
				$settings,
				[ 'did_config' => true ]
			);
		}
		
		if ( class_exists('config', false)
		  && ! $this->_template_config['did_config']
		  && is_array ( $conf = config::get('template') ) 
		)
		{
			$this->template_config( $conf );
		}

		return $this->_template_config;
	}


	function template ( $to_be_wrapped, array $incl_require_load_files = [] ) : string {


		$config = $this->template_config();


		if( $this->requested('template_component') )
		{
			return $to_be_wrapped;
		}


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

						file_get_contents('plugins/asset_load_controller/script_source.js'),

						'var ASL = new Asset_Load_Controller(',
							json_encode($ASL_config) . ',',
							json_encode($this->require_load_files) . ',',
							'()=>(window.Template = new template()).init())',
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
