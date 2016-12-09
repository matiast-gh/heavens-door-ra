<?php

	class View {
		private $vars;
		private $template;

		public function __construct(){
			$this->vars = array();
			$this->template = '';
		}

		private function getTemplate($template) {
			$this->template .= @file_get_contents(TEMPLATE_PATH.$template);
		}

		private function htmlTrim(){
			$this->template = preg_replace('/^\h+|\h+$/m', '', $this->template);
		}
		private function htmlOneLine(){
			$this->template = str_replace(array("\r\n", "\n", "\r"), '', $this->template);
		}

		private function render($trim = false, $oneline = false) {
			$this->setMessages();
			foreach($this->vars as $key => $value) {
				$this->template = str_replace('{'.$key.'}', $value, $this->template);
			}
			if ($trim) {
				$this->htmlTrim();
			}
			if ($oneline) {
				$this->htmlOneLine();
			}
		}

		private function setMessages() {
			if (isset($_SESSION['msg'])) {
				switch ($_SESSION['msg'][0]) {
					case 'error' : {
						$this->setVar('msg', '<div id="msg" class="form-group"><div class="alert alert-warning" role="alert">'.$_SESSION['msg'][1].'</div></div>');
						break;
					}
					case 'success' : {
						$this->setVar('msg', '<div id="msg" class="form-group"><div class="alert alert-success" role="alert">'.$_SESSION['msg'][1].'</div></div>');
						break;
					}
					default: {
						$this->setVar('msg', '');
					}
				}
				unset($_SESSION['msg']);
			}
			else {
				$this->setVar('msg', '');
			}
		}

		private function header() {
			$this->getTemplate('header.tpl');
		}

		private function footer() {
			$this->getTemplate('footer.tpl');
		}

		public function setVar($name, $value) {
			if (is_array($value)) {
				foreach($value as $keyv => $valuev) {
					$this->vars[$name] .= $valuev;
				}
			}
			else {
				$this->vars[$name] = $value;
			}
		}

		public function setVarRouters($routers) {
			$oRouters = array();
			foreach($routers as $router) {
				$oRouters[] = '<option>' . $router . '</option>';
			}
			$this->setVar('routers', $oRouters);
		}

		public function setVarMonitor($operations, $statusLatchOperations) {
			$oStatusLatchOperations = '';
			foreach($statusLatchOperations as $key => $value) {
				switch ($value) {
					case 'off': {
						$oStatusLatchOperations .= '<div class="alert alert-warning" role="alert">'.$operations[$key].' is <strong>Latched</strong></div>';
						if ($key == LATCH_OPERATIONS_CFG) {
							$oStatusLatchOperations .= '<script>location.reload();</script>';
						}
						break;
					}
					case 'on': {
						if ($key == LATCH_OPERATIONS_UNPAIR) {
							$oStatusLatchOperations .= '<div style="position:relative;" class="alert alert-success" role="alert">'.$operations[$key].' is <strong>OPEN</strong><form method="post" style="position:absolute; top:8px; right:8px;"><input type="hidden" name="action" value="unpair"><button id="btn-unpair" name="btn-unpair" type="submit" class="btn btn-primary">Unpair</button></form></div>';
							$oStatusLatchOperations .= '<script>
								$("#btn-unpair").click(function() {
									if (confirm("Unpair! Are you sure ?")) {
										$("#btn-unpair").submit();
									}
								});
							</script>';
						}
						else {
							$oStatusLatchOperations .= '<div class="alert alert-success" role="alert">'.$operations[$key].' is <strong>OPEN</strong></div>';
						}
						break;
					}
					case 'unknown': {
						$oStatusLatchOperations .= '<div class="alert alert-warning" role="alert">'.$operations[$key].': <strong>Unknown Status</strong></div>';
						break;
					}
				}
			}
			$this->setVar('monitor', $oStatusLatchOperations);
		}

		public function pairing() {
			$this->header();
			$this->getTemplate('pairing.tpl');
			$this->footer();
			$this->render(true);
			return $this->template;
		}

		public function latched() {
			$this->header();
			$this->getTemplate('latched.tpl');
			$this->footer();
			$this->render(true);
			return $this->template;
		}

		public function configuration() {
			$this->header();
			$this->getTemplate('configuration.tpl');
			$this->footer();
			$this->render(true);
			return $this->template;
		}

		public function monitor() {
			$this->getTemplate('monitor.tpl');
			$this->render(true);
			return $this->template;
		}

		function __destruct() {
			unset($this);
		}
	}

?>