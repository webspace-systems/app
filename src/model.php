<?php

class model {


	//protected function ___get ( string $key, $required = true ) {
	protected function get ( ...$args ) {

		$required = is_bool( end($args) ) ? array_pop($args) : false;

		$key = count($args) == 1 ? $args[0] : $args;


		if( ! $key )
		{
			return get_object_vars($this);
		}

		else if( is_string($key) && property_exists($this, $key) )
		{
			return $this->$key;
		}

		else if( is_array($key) )
		{
			return $this->resolve_by_key_paths($key);
		}
		
		else if( $required )
		{
			trigger_error("Unable to get of object '".get_class($this)."' by key: '".$key."'", E_USER_ERROR);
		}

		return null;
	}


	protected function set ( $key, $val = null ) : bool {

		if(is_array($key))
		{
			foreach($key as $k=>$v)
			{
				$this->$k = $v;
			}

			return true;
		}
		else if(!is_string($key) || !$key)
		{
			trigger_error("Key not found: '$key'", E_USER_ERROR);

			return false;
		}
		else if(!is_null($val))
		{
			trigger_error("Invalid value: '$val'", E_USER_ERROR);

			return false;
		}

		if(is_array($val) && property_exists($this, $key) && is_array($this->$key))
		{
			$this->$key = array_merge($this->$key, $val);
		}
		else
		{
			$this->$key = $val;
		}

		return true;
	}



	function resolve_by_key_paths ( array $key_paths ) {

		$val = null;

		if ( property_exists($this, $key_paths[0]) )
		{
			$_val = $this->{$key_paths[0]};

			array_shift($key_paths);

			$depth_required = count($key_paths)-1;

			if($depth_required < 0) $val = $_val;

			foreach($key_paths as $depth => $key_path_name)
			{
				if(!isset($_val[$key_path_name]))
				{
					break;
				}

				$_val = $_val[$key_path_name];

				if($depth >= $depth_required)
				{
					$val = $_val;
				}
			}
		}

		return $val;
	}

}
