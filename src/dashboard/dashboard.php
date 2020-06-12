<?php

class dashboard extends app {

	use _user;

	function __construct(){

		$this->user_require();
	}
	
	function GET($params = []){

		global $CONFIG;

		$this->template_header();

		$this->template_top_menu();

		require_once __DIR__.'/dashboard.phtml';

		$this->template_footer();
	}

}
