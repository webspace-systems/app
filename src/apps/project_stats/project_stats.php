<?php

class project_stats extends app {


  function __construct(){


    $this->settings = [
        'project_path' => '',
        'exclude_paths' => []
    ];

    $this->possible_exclude_paths = ['test'];

	//$this->user_require();

    $this->results = [];

    //$this->load_session();
/*
    $mn = '_'.strtoupper($_SERVER['REQUEST_METHOD']);

    if(method_exists($this, $mn))
      $this->$mn();
    else
      exit("Unexpected request method '".$_SERVER['REQUEST_METHOD']."'");
      */
  }

	function index($params = []){

		$this->template_header();

		$this->template_top_menu();

		require_once __DIR__.'/project_stats.phtml';

		$this->template_footer();
	}


  function POST(){
    
    if(isset($_REQUEST['settings']))
    {
      $this->set_settings($_REQUEST['settings']);
    }

    $this->draw_stats();

    $this->GET();
  }


  function load_session(){

    if(!session_id()) session_start();

    if(@is_array($_SESSION['Project_Stats_Session_Settings']))
    {
      $this->set_settings($_SESSION['Project_Stats_Session_Settings']);
    }
  }

  function set_settings($settings = []){

    if(!session_id()) session_start();

    if(is_array($settings))
    {
      $_SESSION['Project_Stats_Session_Settings'] = 
      $this->settings = array_merge($this->settings, $settings);
    }
  }


  function render_exclude_paths_options_html($html = ''){

    return implode('', array_map( function($path) {

          $selected = in_array($path,$this->settings['exclude_paths']);

          return '
            <li>
              <input
                type="checkbox"
                name="settings[exclude_paths][]"
                value="'.$path.'"
                '.($selected?'selected':'').'
              />
              '.$path.'
            </li>
          ';
        },
        $this->possible_exclude_paths
      )
    );
  }


  function render_results_html($html = ''){

    if(empty($this->results))
    {
      return $html;
    }

    

    return $html;
  }

  function draw_stats(){

    $results = [];


  }

}
