<?php

	/*

		class.abstract.router por mt

		version 1.0			20161022

	*/

	abstract class AbstractRouter {
		protected $router_ip = '';
		protected $user = '';
		protected $pass = '';

		abstract protected function login();
		abstract protected function router_configuration($active = false);
		abstract protected function remote_access($active = false, $remote_ip, $port);

		protected function __construct($router_ip, $user, $pass) {
			$this->router_ip = $router_ip;
			$this->user = $user;
			$this->pass = $pass;
		}
	}

?>