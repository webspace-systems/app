<?php

trait _user {

	use _error;

	private $_user = null;

	function user( array $set = null, bool $override = false ) :? user {

		if(!session_id() && !headers_sent()) session_start();

		if(!session_id()) return null;

		if(is_array($set))
		{
			if(!is_object($this->_user) || !$override)
			{
				$this->_user = new user();
			}

			$this->_user->from_array($set);

			$_SESSION['user'] = $this->_user->to_array();
		}
		else if($override)
		{
			$this->_user = $_SESSION['user'] = null;
		}
		else if(isset($_SESSION['user']) && is_array($_SESSION['user']))
		{
			// Load from session
			$this->_user = (new user($this))->from_array($_SESSION['user']);
		}

		return $this->_user;
	}


	function user_require () : void {

		if(!$this->user())
		{
			trigger_error('User required', E_USER_ERROR);
		}
	}
}
