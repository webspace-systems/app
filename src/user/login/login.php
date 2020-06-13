<?php

class user_login extends app {

	use _user;

	function GET(){

		$params = $this->get_request();

		$this->template_header();

		require_once __DIR__.'/login.phtml';

		$this->template_footer();
	}


	function POST(){

		$params = $this->get_request();


		$q = $this->sql()->prepare('

			SELECT
				*
			FROM
				users
			WHERE
				(
					username = :username
					OR email = :username
				)
				AND password = :password
		');

		$q->execute([
			'username' => $params['username'],
			'password' => md5($params['password'])
		]);

		if($q->rowCount()==0)
			return $this->GET(['msg'=>'Bad credentials. <a href="/user/forgotten" style="font-weight:normal;">Forgot your password?</a>']);

		$user = $q->fetch();

		if($user['status'] < 1)
			return $this->GET(['msg'=>'Please check your e-mail inbox or spam folder for a link to confirm and activate your account.']);

		if($user['status'] > 1)
			return $this->GET(['msg'=>'Unable to sign in. Please contact '.config::get('emails')['support']]);


		if( $this->user($user) )
		{
			header('location: '.config::get('user_login')['redirect_to'] );		
		}

		return null;
	}
}
