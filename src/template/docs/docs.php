<?php

class template_docs extends app {

  function GET($params = []){

    global $CONFIG;

    $this->template_header();

    $this->template_top_menu();

    require_once __DIR__.'/docs.phtml';

    $this->template_footer();
  }

}
