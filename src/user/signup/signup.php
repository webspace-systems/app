<?php

class user_signup extends app {
	

	function GET($params = []){

		$this->template_header();

		require_once __DIR__.'/signup.phtml';

		$this->template_footer();
	}


	function POST($params = []){

		$error_fields = [];

		$error_fields['name'] = $this->validate_input('name','varchar',6);
		$error_fields['company'] = $this->validate_input('company','varchar',0);
		$error_fields['username'] = $this->validate_input('username','varchar',5,true);
		$error_fields['email'] = $this->validate_input('email','email',6,true);
		$error_fields['password'] = $this->validate_input('password','varchar',8);

		if(!$error_fields['username']&&!$error_fields['email'])
		{
			$q = $this->sql()->prepare('SELECT * FROM users WHERE username=? OR email=?');

			$q->execute([$params['username'],$params['email']]);

			if($q->rowCount()>0)
			{
				$r = $q->fetch();

				if($r['email'] == $params['email'])
					$error_fields['email'] = 'unique';

				if($r['username'] == $params['username'])
					$error_fields['username'] = 'unique';
			}
		}

		if(!empty(array_filter(array_values($error_fields))))
		{
			return $this->GET(array_merge($params,['msg'=>'Please re-evaluate required field: '.implode(', ', array_keys(array_filter($error_fields)))]));
		}

		$q = $this->sql()->prepare('

			INSERT INTO
				users
			SET
				username = :username,
				email = :email,
				password = :password,
				name = :name,
				company = :company
		');

		$q->execute([
			'username' => $params['username'],
			'email' => $params['email'],
			'password' => md5($params['password']),
			'name' => $params['name'],
			'company' => $params['company'],
		]);


		$Recipient_Name = $params['name'];

		$Confirm_Url = config::get('url').'/'.strtr(get_class(),'_','/').'/confirm?code='.md5($params['email']);

		ob_start();
		require_once __DIR__.'/welcome_mail.phtml';

		$mail_contents = ob_get_clean();

        mail(
        	$params['email'],
        	'Welcome',
        	$mail_contents,
        	'MIME-Version: 1.0' . "\r\n"
        	.'Content-type: text/html; charset=iso-UTF-8'."\r\n"
        	.'To: '.$params['name'].' <'.$params['email'].'>' . "\r\n"
        	.'From: Customer Service - '.config::get('template', 'title').' <noreply@'.$_SERVER['HTTP_HOST'].'>'."\r\n"
        )
        or die('Unable to send mail');


		header('location: '.config::get('url').'/user/login?msg=Welcome to '.config::get('template', 'title').'! Please click the link in the mail you\'be been sent, to confirm and activate your account.');
	}

}

