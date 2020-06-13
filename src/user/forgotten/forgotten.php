<?php

class user_forgotten extends app {

	function GET($params = []){

		$this->template_header();

		require_once __DIR__.'/forgotten.phtml';

		$this->template_footer();
	}


	function POST(){

		$request = $this->get_request();

		$q = $this->sql()->prepare('

			SELECT
				*
			FROM
				users
			WHERE
				username = :username
				OR email = :username
		');

		$q->execute([
			'username' => $request['username'],
		]);

		if($q->rowCount()>0)
		{
			$users = $q->fetchAll();

			foreach($users as $user)
			{
				$Recipient_Name = $user['name'];

				$Reset_Url = config::get('url').'/'.strtr(get_class(),'_','/').'/reset?code='.md5($user['email']);

				ob_start();

				require_once __DIR__.'/reset_password_mail.phtml';

				$mail_contents = ob_get_clean();

		        mail(
		        	$user['email'],
		        	'Password reset request - '.config::get('platform_name'),
		        	$mail_contents,
		        	'MIME-Version: 1.0' . "\r\n"
		        	.'Content-type: text/html; charset=iso-UTF-8'."\r\n"
		        	.'To: '.$user['name'].' <'.$user['email'].'>' . "\r\n"
		        	.'From: Customer Service - '.config::get('platform_name').' <noreply@'.$_SERVER['HTTP_HOST'].'>'."\r\n"
		        )
		        or die('Unable to send mail');
			}

			header('location: /user/login?msg=Check your e-mail inbox for further instructions');

		}
		else
			$this->GET(['msg'=>'Unable to find matching account. Maybe try another username or e-mail?']);
	}

}
