<?php

trait _sql {

	private $_sql = null;

	function sql () : ? PDO {

		if(is_null($this->_sql) && ( $c = config::get('sql',1,1) ) )
		{
		     $this->_sql = new PDO(
		     	'mysql:host='.$c['host'].';dbname='.$c['db'],
		     	$c['user'],
		     	$c['pass'],
		     	[
				    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				]
			);
		}

		return $this->_sql ?? null;
	}
}
