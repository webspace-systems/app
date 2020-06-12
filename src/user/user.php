<?php

class user {

	function from_array( array $data ){

		foreach($data as $k=>$v) $this->$k = $v;

		return $this;
	}

	function to_array(){

		return get_object_vars($this);
	}
	
}
