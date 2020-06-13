<?php

class user_forgotten_reset extends app {

	function GET($params = []){

		$this->template_header();

		require_once __DIR__.'/reset.phtml';

		$this->template_footer();
	}


	function POST($params = []){


		if(!isset($params['code'])) return $this->respond(['error'=>'Bad code'],400);


		$error_fields = [];

		$error_fields['password'] = $this->validate_input('password','varchar',8);

		if(!empty(array_filter(array_values($error_fields))))
		{
			return $this->GET(array_merge($params,['msg'=>'Please re-evaluate required field: '.implode(', ', array_keys(array_filter($error_fields)))]));
		}

		$q = $this->sql()->prepare('

			UPDATE
				users
			SET
				password = :password
			WHERE
				MD5(email) = :md5_email
		');

		$q->execute([
			'password' => md5($params['password']),
			'md5_email' => $params['code'],
		]);

		header('location: '.config::get('url').'/user/login?msg=Your password has been reset.');
	}

}
