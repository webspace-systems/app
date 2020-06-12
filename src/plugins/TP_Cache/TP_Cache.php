<?php
/*
	Theis C. N. Pedersen <theischris@gmail.com>
	2020-04-14
	Skanderborg, Denmark
*/

declare(strict_types=1);



class TP_Cached {

  public $resource_name;
  public $params;
  public $cache_file_path_base;
  public $cache_file_path;

  function __construct($resource_name, $params, $cache_file_path_base){

  	$this->resource_name = $resource_name;
  	$this->params = $params;
  	$this->cache_file_path_base = $cache_file_path_base;

  	$this->cache_file_path = md5();

  }

  function is_available() : bool {


  }

  function output() : string {


  }

  function ob_start() : bool {

  	ob_start();

  	return true;
  }

  function ob_save() : bool {

  	$input = ob_get_clean();



  }


}


