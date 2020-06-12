<?php

trait _template {

	function template_header(){

		global $CONFIG;
		
		require_once 'template/header.phtml';
	}

	function template_top_menu(){

		global $CONFIG;
		
		$User = $this->user();
		$Accounts = [];//$this->accounts()->index();

		require_once 'template/top_menu.phtml';
	}

	function template_footer(){

		global $CONFIG;
		
		require_once 'template/footer.phtml';
	}

}
