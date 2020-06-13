<?php

class config extends model { use singleton; }

new config();

config::set([

	'platform_name' => 'platform',

	'url' => rtrim( '//'.$_SERVER['HTTP_HOST'].'/'.trim(substr(__DIR__,strlen($_SERVER['DOCUMENT_ROOT'])),'/'), '/'),

	'route_root_paths' => [ __DIR__ ],

	'frontpage_component' => 'user/login',

	'user_login' => [
		'redirect_to' => 'dashboard'
	],

	'error' => [

		'mail_to' => 'theischris@gmail.com',

		'save_to' => 'php_error.log', // 'db' or 'php_error.log' // impl. in comp. error.
		'save_to_table_name' => '', // auto. created in first use, customize in comp. error

		'show_page_component' => 'error', 

		'ignore' => [ 'notices about not using isset' ] // To-do / Not yet implemented
	],

	'emails' => [
		'info' => 'info@'.$_SERVER['HTTP_HOST'],
		'support' => 'support@'.$_SERVER['HTTP_HOST'],
	],

	'email_template' => [
		'header_image' => 'images/logo/new_ninja.png'
	],

	'meta' => [
		'description' => "Developer and company tools to web content development.",

		'viewport' => "width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no",

		'mobile-web-app-capable' => "yes",
		'apple-mobile-web-app-capable' => "yes",
		'apple-mobile-web-app-title' => 'Webspace.systems',

	],


	'dev_mode' => in_array(explode(':', $_SERVER['HTTP_HOST'])[0], ['localhost','127.0.0.1']),


	'sql' => [

		'host' => 'localhost',
		'user' => 'root',
		'pass' => 'root',
		'db' => 'platform'
	]
	
]);
