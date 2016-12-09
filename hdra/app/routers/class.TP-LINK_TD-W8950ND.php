<?php

	/*

		class.TP-LINK_TD-W8950ND por mt

		version 1.0			20161019

	*/

	require_once ROUTERS_PATH.'class.abstract.router.php';

	class Router extends AbstractRouter {
		function __construct($router_ip, $user, $pass) {
			parent::__construct($router_ip, $user, $pass);
		}

		public function login() {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://$this->router_ip/");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->pass");
			$result = curl_exec($ch);
			curl_close($ch);
			if ((preg_match('/Authorization required/', $result)) or ($result == '')) {
				return 0;
			}
			else {
				return 1;
			}
		}

		public function router_configuration($active = false) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://$this->router_ip/scsrvcntr.cmd?action=view");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->pass");
			$result = curl_exec($ch);
			curl_close($ch);
			if (preg_match_all("/name='http' checked/", $result) < 2) {
				$active_status = false;
			}
			else {
				$active_status = true;
			}

			if ($active != $active_status) {
				$ch = curl_init();
				if ($active) {
					curl_setopt($ch, CURLOPT_URL, "http://$this->router_ip/scsrvcntr.cmd?action=save&http=1&http=3&icmp=1&tftp=2&tftp=0");
				}
				else {
					curl_setopt($ch, CURLOPT_URL, "http://$this->router_ip/scsrvcntr.cmd?action=save&http=2&http=0&icmp=1&tftp=2&tftp=0");
				}
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->pass");
				$result = curl_exec($ch);
				curl_close($ch);
				if (preg_match('/Authorization required/', $result) or ($result == '')) {
					return 0;
				}
				else {
					return 1;
				}
			}
			else {
				return 3;
			}
		}

		public function remote_access($active = false, $remote_ip, $port) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://$this->router_ip/scvrtsrv.cmd?action=view");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->pass");
			$result = curl_exec($ch);
			curl_close($ch);
			if (preg_match_all("/<td>$port</", $result) < 4) {
				$active_status = false;
			}
			else {
				$active_status = true;
			}

			if ($active != $active_status) {
				$ch = curl_init();
				if ($active) {
					curl_setopt($ch, CURLOPT_URL, "http://$this->router_ip/scvrtsrv.cmd?action=add&srvName=hdra&srvAddr=$remote_ip&proto=1,&eStart=$port,&eEnd=$port,&iStart=$port,&iEnd=$port,");
				}
				else {
					curl_setopt($ch, CURLOPT_URL, "http://$this->router_ip/scvrtsrv.cmd?action=remove&rmLst=".sprintf("%u", ip2long($remote_ip))."|$port|$port|1|0|0|$port|$port,");
				}
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, "$this->user:$this->pass");
				$result = curl_exec($ch);
				curl_close($ch);
				if (preg_match('/Authorization required/', $result) or ($result == '')) {
					return 0;
				}
				else {
					return 1;
				}
			}
			else {
				return 3;
			}
		}
	}

?>