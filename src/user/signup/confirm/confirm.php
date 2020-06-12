<?php

class user_signup_confirm extends app {

	function GET($params = []){

		global $CONFIG;


		if(!isset($params['code'])) return $this->respond(['error'=>'Bad code'],400);


		$q = $this->sql()->prepare('

			UPDATE
				users
			SET
				status=1
			WHERE
				MD5(email) = :md5_email
		');

		$q->execute([ 'md5_email' => $params['code'] ]);


		$this->template_header();

		require_once __DIR__.'/confirm.phtml';

		$this->template_footer();
	}

}
