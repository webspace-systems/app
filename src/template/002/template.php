<?php

trait _template_002 {

	public $template_require_assets = [

      'plugins/jquery/jquery.js',
      'plugins/bootstrap/bootstrap.css',

      'template/002/css/template.css',

      'template/002/js/auxil.js',

      'template/002/js/template.js',

      'template/002/css/top_menu.css',

      'template/002/css/full-screen-apps-menu.css',

      'plugins/fontawesome/font-awesome.min.css',
      // 'plugins/mfglabs_icons/mfglabs_icons.css',
      'plugins/opensans/opensans.css',
      // 'plugins/octicons/octicons.css',
      // 'plugins/entypo/entypo.css',

      'plugins/bootstrap/bootstrap.js',
      'plugins/bootstrap-notify/bootstrap-notify.js',
	];

	function template_require ( $asset ) : array {

		$this->template_require_assets[] = $asset;
	}

	function template_header () : void {

		require_once __DIR__.'/header.phtml';
	}


	function template_top_menu () : void {
		
		$User = $this->user();
		$Accounts = [];//$this->accounts()->index();

		require_once __DIR__.'/top_menu.phtml';
	}


	function template_footer () : void {
		
		require_once __DIR__.'/footer.phtml';
	}

	
	function template_url () : string {

		return $this->get_url() . '/template/' . basename(__DIR__);
	}

}
