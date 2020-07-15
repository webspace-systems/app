<?php

class public_index extends app {

	function GET ( $params = [] ) {

		return $this->template (

			['div', 'class'=>'module', 'id' => get_class($this), [
				'Test 1', 'test 2'
			]]
		);
	}
}