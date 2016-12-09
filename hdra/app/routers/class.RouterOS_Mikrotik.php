<?php

	/*

		class.RouterOS_Mikrotik por mt

		version 1.0			20161116

	*/

	require_once ROUTERS_PATH.'class.abstract.router.php';
	require_once ROUTERS_PATH.'class.api.routeros.php';

	class Router extends AbstractRouter {
		function __construct($router_ip, $user, $pass) {
			parent::__construct($router_ip, $user, $pass);
		}

		public function login() {
			$router_os = new RouterosAPI();

			if ($router_os->connect($this->router_ip, $this->user, $this->pass)) {
				return 1;
			}
			else {
				return 0;
			}
		}

		public function router_configuration($active = false) {

		}

		public function remote_access($active = false, $remote_ip, $port) {

		}
	}

?>