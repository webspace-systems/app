<?php

class accounts extends app {

	use _user;

	public $_accounts = [];

	function __construct( $account_id = null ){

		$this->user_require();

		if(is_numeric($account_id))
		{
			return $this->get_by_id($account_id);
		}
	}

	function GET( $method = null, $params = [], $path = [] ){
		
		$this->template_header();

		$this->template_top_menu();

		require_once __DIR__.'/accounts.phtml';

		$this->template_footer();
	}

	function index(){

		return $this->_accounts;
	}

	function get_active($specific_attribute = ''){

		if($specific_attribute)
			return $this->_accounts[$this->user()->active_account][$specific_attribute];
		else
			return $this->_accounts[$this->user()->active_account];
	}

	function get_current($s=''){ return $this->get_active($s); }

	function get_by_id($id){

		if(!is_numeric($id)) return false;

		if(isset($this->_accounts[$id])) return $this->_accounts[$id];

		$q = $this->sql()->prepare("

			SELECT
				*
			FROM
				accounts
			WHERE
				id=:account_id
		");

		$q->execute([ 'account_id' => $id ]);

		if($q->rowCount()==0) return false;

		$account = $q->fetch();

		$this->_accounts[$account['id']] = $account;

		return $account;
	}

	function load_from_session(){

		if(!session_id() && !headers_sent()) session_start();

		if(is_array($_SESSION) && is_array($_SESSION['accounts']))
			$this->_accounts = $_SESSION['accounts'];
	}

	function save_to_session(){

		if(!session_id() && !headers_sent()) session_start();

		if(is_array($_SESSION))
			$_SESSION['accounts'] = $this->_accounts;
	}
}
