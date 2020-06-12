<?php

class app {

	use _sql;
	use _error;
	use _template;

	
	private $_request_data = null;

	function get_request() : array {

		if(empty($this->_request_data))
		{
			$this->_request_data = [];

			if(is_array($_GET)) foreach ($_GET as $k => $v) $this->_request_data[$k] = $v;

			parse_str(file_get_contents('php://input', 'r'), $_Input);

			if(is_array($_Input))
			{
				foreach ($_Input as $k => $v) $this->_request_data[$k] = $v;
			}
		}

		return $this->_request_data;
	}
	
	
	private $_request_headers = null;

	function get_request_headers() : array {

		if(empty($this->_request_headers))
		{
			$this->_request_headers = [];

			foreach ($_SERVER as $k=>$v)
			{
				if(substr($k, 0, 5) == 'HTTP_')
				{
					$this->_request_headers[ucwords(strtolower(str_replace('_',' ',substr($k, 5))))] = $v;
				}
			}
		}

		return $this->_request_headers;
	}



	function validate_input( string $key, string $type, int $min = 1, int $max = 0 ) :? string {

		$request = $this->get_request();

		if(!isset($request[$key])) return 'key';

		$input = $request[$key];
		
		switch ($type) {

			case 'varchar':

				if(!$max) $max = 255;

				if(gettype($input) != 'string')
					return 'type';

				if(strlen($input) < $min || ($max && strlen($input) > $max))
					return strlen($input) < $min ? 'min' : 'max';

				break;

			case 'email':

				if(!$max) $max = 255;

				if(gettype($input) != 'string')
					return 'type';

				if(strlen($input) < $min || ($max && strlen($input) > $max))
					return strlen($input) < $min ? 'min' : 'max';

				if(!filter_var($input, FILTER_VALIDATE_EMAIL))
					return false;

				break;

			case 'int':

				if(!$max) $max = 11;

				if(!is_numeric($input))
					return 'type';

				if($input < $min || $input > $max)
					return $input < $min ? 'min' : 'max';

				break;
			
			default:

				if(is_numeric($input) && ($input < $min || ($max && $input > $max)))
				{
					return $input < $min ? 'min' : 'max';
				}

				if(!is_numeric($input) && (strlen($input) < $min || ($max && strlen($input) > $max)))
				{
					return strlen($input) < $min ? 'min' : 'max';
				}

				break;
		}

		return null;
	}


	function response( $success, $output_data = [], $http_code = 0 ) : void {

		$http_code = $http_code ?? ( $success ? 200 : 500 );

		$content_type = 'text/html';


		// Rule for json api

		if(strpos($_SERVER['HTTP_ACCEPT'], 'application/json')>=0)
		{
			$content_type = 'application/json';
		}


		// Other rule... ?


		header('Content-Type: '.$content_type);

		http_response_code($http_code);


		echo $output_data;
	}


	function respond($data, $code = 200){

		$content_type = "text/html";

		if(strpos($_SERVER['HTTP_ACCEPT'], 'json')>0||@strpos($_SERVER['CONTENT_TYPE'],'json')>0)
		{
			$content_type = "application/json";
		}

		if(!headers_sent())
		{
			http_response_code($code);

			header('Content-Type: '.$content_type.'; charset=UTF-8;');
		}

		$response = $content_type == 'application/json' ? json_encode($data) : $data;


		echo is_array($response) ? print_r($response,true) : $response;
	}



}

