<?php

class public_index extends app {

	function GET ( $params = [] ) {

		return $this->docf ( 'index.phtml' );
	}
}