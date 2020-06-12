<?php

trait _accounts {

	use _user;

	private $_accounts = null;

	function accounts( $refresh = false ){

		if(!$this->_accounts || $refresh)
		{
			$this->_accounts = new accounts($this);

			if($this->user())
				foreach($this->user()->account_ids as $account_id)
					$this->_accounts->get_by_id($account_id);

			$this->_accounts->save_to_session();
		}
		else
		{
			$this->_accounts->load_from_session();
		}

		return $this->_accounts;
	}

}
