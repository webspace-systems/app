<?php


class user_logout extends app {

	use _user;

	function GET(){

		$this->user(null,true);

		header('location: '.config::get('url'));
	}
}
