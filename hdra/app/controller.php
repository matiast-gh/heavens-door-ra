<?php

	require_once 'model.php';
	require_once 'view.php';

	class Controller {
		private $model;
		private $view;

		public function __construct(){
			$this->model = new Model();
			$this->view = new View();
		}

		public function handle($action) {
			$this->view->setVar('appname', TITLE_APP);
			switch ($action) {
				/* PAIR */
				case 'pairing': {
					echo $this->model->pair();
					break;
				}
				/* SAVE CONFIGURATION */
				case 'configuration': {
					if ($this->model->config['accountId'] != '') {
						$soLatch = $this->model->getStatusLatchOperations(array(LATCH_OPERATIONS_CFG));
						if ($soLatch[LATCH_OPERATIONS_CFG] == 'on') {
							$this->model->saveConfiguration();
						}
					}
					break;
				}
				/* MONITOR */
				case 'monitor': {
					if ($this->model->config['accountId'] != '') {
						$soLatch = $this->model->getStatusLatchOperations(array(LATCH_OPERATIONS_CFG));
						if ($soLatch[LATCH_OPERATIONS_CFG] == 'on') {
							$this->view->SetVarMonitor(unserialize(LATCH_OPERATIONS), $this->model->getStatusLatchOperations(array_merge(array(LATCH_OPERATIONS_UNPAIR), array(LATCH_OPERATIONS_CFG), unserialize(LATCH_OPERATIONS_SERVICE))));
							echo $this->view->monitor();
						}
					}
					break;
				}
				/* SERVICE */
				case 'service': {
					if (($this->model->config['accountId'] != '') and ($this->model->config['router'] != '') and ($this->model->config['router_ip'] != '') and ($this->model->config['user'] != '')) {
						require_once ROUTERS_PATH.'class.'.$this->model->config['router'].'.php';

						$router = new Router($this->model->config['router_ip'], $this->model->config['user'], $this->model->config['pass']);
						$operations = unserialize(LATCH_OPERATIONS);
						$soLatch = $this->model->getStatusLatchOperations(unserialize(LATCH_OPERATIONS_SERVICE));
						foreach($soLatch as $key => $value) {
							$router_response = -1;
							switch($key) {
								/* REMOTE ACCESS */
								case LATCH_OPERATIONS_RA: {
									if (($this->model->config['remote_ip'] != '') and ($this->model->config['port'] != '')) {
										switch ($value) {
											/* BLOCK */
											case 'off': {
												$router_response = $router->remote_access(false, $this->model->config['remote_ip'], $this->model->config['port']);
												break;
											}
											/* UNBLOCK */
											case 'on': {
												$router_response = $router->remote_access(true, $this->model->config['remote_ip'], $this->model->config['port']);
												if ($router_response == 1) {
													if ((WOL_COMMAND != '') and ($this->model->config['mac'] != '')) {
														$cmd = str_replace('{mac}', $this->model->config['mac'], WOL_COMMAND);
														$cmd_out = shell_exec($cmd);
														echo $operations[$key].'::&nbsp;&nbsp;&nbsp;<b>WOL Command Executed:</b> '.$cmd.'&nbsp;&nbsp;&nbsp;<b>Command Output:</b> '.$cmd_out.'<br>';
													}
												}
												break;
											}
											/* UNKNOWN */
											case 'unknown': {
												$router_response = $router->remote_access(false, $this->model->config['remote_ip'], $this->model->config['port']);
												break;
											}
										}
									}
									break;
								}
								/* REMOTE ROUTER CONFIGURATION */
								case LATCH_OPERATIONS_RC: {
									switch ($value) {
										/* BLOCK */
										case 'off': {
											$router_response = $router->router_configuration(false);
											break;
										}
										/* UNBLOCK */
										case 'on': {
											$router_response = $router->router_configuration(true);
											break;
										}
										/* UNKNOWN */
										case 'unknown': {
											$router_response = $router->router_configuration(false);
											break;
										}
									}
									break;
								}
							}
							echo $operations[$key].'::&nbsp;&nbsp;&nbsp;On Latch: '.'<b>'.$value.'</b>&nbsp;&nbsp;&nbsp;Router Response: <b>'.$router_response.'</b><br>';
						}
					}
					break;
				}
				/* TEST LOGIN */
				case 'test-login': {
					$soLatch = $this->model->getStatusLatchOperations(array(LATCH_OPERATIONS_CFG));
					if ($soLatch[LATCH_OPERATIONS_CFG] == 'on') {
						if ((trim($_POST['router']) != '') and (trim($_POST['routerip']) != '') and (trim($_POST['user']) != '')) {
							require_once ROUTERS_PATH.'class.'.trim($_POST['router']).'.php';

							$router = new Router(trim($_POST['routerip']), trim($_POST['user']), trim($_POST['pass']));
							echo $router->login();
						}
					}
					break;
				}
				/* GET MAC */
				case 'get-mac': {
					$soLatch = $this->model->getStatusLatchOperations(array(LATCH_OPERATIONS_CFG));
					if ($soLatch[LATCH_OPERATIONS_CFG] == 'on') {
						if (trim($_POST['remoteip']) != '') {
							if ((ARP_COMMAND != '') and (trim($_POST['remoteip']) != '')) {
								$cmd = str_replace('{ip}', trim($_POST['remoteip']), ARP_COMMAND);
								$cmd_out = shell_exec($cmd);
								$mac_resolved = '';
								if (preg_match(HD_EXP_ARP_MAC, $cmd_out, $mac_resolved)) {
									echo strtoupper(str_replace("-", ":", $mac_resolved[0]));
								}
							}
						}
					}
					break;
				}
				/* UNPAIR */
				case 'unpair': {
					if ($this->model->config['accountId'] != '') {
						$soLatch = $this->model->getStatusLatchOperations(array(LATCH_OPERATIONS_UNPAIR));
						if ($soLatch[LATCH_OPERATIONS_UNPAIR] == 'on') {
							$this->model->unpair();
						}
					}
					break;
				}
				default: {
					/* PAIRED */
					if ($this->model->config['accountId'] != '') {
						$soLatch = $this->model->getStatusLatchOperations(array(LATCH_OPERATIONS_CFG));
						switch ($soLatch[LATCH_OPERATIONS_CFG]) {
							/* CONFIGURATION OFF */
							case 'off': {
								echo $this->view->latched();
								break;
							}
							/* CONFIGURATION ON */
							case 'on': {
								$this->view->setVarRouters($this->model->getRouters());
								$this->view->setVar('router', $this->model->config['router']);
								$this->view->setVar('routerip', $this->model->config['router_ip']);
								$this->view->setVar('user', $this->model->config['user']);
								$this->view->setVar('pass', $this->model->config['pass']);
								$this->view->setVar('remoteip', $this->model->config['remote_ip']);
								$this->view->setVar('port', $this->model->config['port']);
								$this->view->setVar('mac', $this->model->config['mac']);
								$this->view->setVar('regexpip', HD_EXP_IP);
								$this->view->setVar('regexpport', HD_EXP_PORT);
								$this->view->setVar('regexpmac', HD_EXP_MAC);
								echo $this->view->configuration();
								break;
							}
						}
					}
					/* UNPAIRED */
					else {
						echo $this->view->pairing();
					}
				}
			}
		}

		function __destruct() {
			unset($this);
		}
	}

?>