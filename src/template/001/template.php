<?php

trait _template_001 {

	public $incl_assets = [
      'plugins/jquery/jquery.js',
      'plugins/bootstrap/bootstrap.css',

      'template/001/css/app.css',
      'template/001/css/custom.css',
      'template/001/css/template.css',
      'template/001/css/dashboard.css',
      'template/001/css/top_menu.css',
      'template/001/css/full-screen-apps-menu.css',

      'plugins/fontawesome/font-awesome.min.css',
      'plugins/mfglabs_icons/mfglabs_icons.css',
      'plugins/opensans/opensans.css',
      'plugins/octicons/octicons.css',
      'plugins/entypo/entypo.css',

      'plugins/bootstrap/bootstrap.js',
      'plugins/bootstrap-notify/bootstrap-notify.js',

      'template/001/js/my_missing_js_functions.js',
      'template/001/js/template.js',
      
      'api/api.js',
    ];
	
	function template_url () : string {

		return $this->get_url() . '/template/' . basename(__DIR__);
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


}
