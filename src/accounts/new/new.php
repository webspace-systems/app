<?php

class accounts_new extends app {

	function __construct(){

		$this->user_require();
	}

	function GET($params = []){

		$this->template_header();

		$this->template_top_menu();

		require_once __DIR__.'/new.phtml';

		$this->template_footer();
	}

	function POST($params = []){

		$error_fields = [];

		$error_fields['name'] = $this->validate_input('name','varchar',4,100);
		$error_fields['description'] = $this->validate_input('description','varchar',0,1000);
		$error_fields['company'] = $this->validate_input('company','varchar',0,100);
		$error_fields['website'] = $this->validate_input('website','varchar',0,100);

		if(!empty(array_filter(array_values($error_fields))))
		{
			$params = array_map(function($in){return strtr($in,['"'=>'“',"'"=>"‘"]);},$params);

			return $this->GET(array_merge($params,['msg'=>'Please re-evaluate required field: '.implode(', ', array_keys(array_filter($error_fields)))]));
		}

		$namespace = strtr(ucwords($params['name']),['___'=>'_','__'=>'_']);
	
		$namespace = preg_replace('/[\W]/', '', $namespace);


		$q = $this->sql()->prepare('

			INSERT INTO
				accounts
			SET
				name = :name,
				namespace = :namespace,
				description = :description,
				website = :website,
				company = :company
		');

		$q->execute([
			'name' => $params['name'],
			'namespace' => $namespace,
			'description' => $params['description'],
			'website' => $params['website'],
			'company' => $params['company']
		]);


		$account_id = $this->sql()->lastInsertId();


		$q = $this->sql()->prepare('

			INSERT INTO
				user_ref_accounts
			SET
				user_id = :user_id,
				account_id = :account_id,
				type = "owner"
		');

		$q->execute([
			'user_id' => $this->user()->id,
			'account_id' => $account_id
		]);


		header('location: '.config::get('url').'/accounts/'.$namespace);
	}

}
