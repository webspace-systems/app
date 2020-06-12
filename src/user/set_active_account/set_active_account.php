<?php

class user_set_active_account extends app  {
	

	function GET($params = []){

		return $this->POST($params);
	}


	function POST($params = []){

		if(is_numeric($params['id']) && in_array($params['id'], $this->user(true)->account_ids))
		{
			$q = $this->sql()->prepare('

				UPDATE
					users
				SET
					active_account = :active_account
				WHERE
					id = :user_id
			');

			$q->execute([
				'active_account' => $params['id'],
				'user_id' => $this->user()->id,
			]);

			$this->user(true);
		}

		header('location: '.config::get('url'));
	}

}