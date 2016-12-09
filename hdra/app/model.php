<?php

	require_once 'classes/class.AppConfig.php';

	/* LATCH */
	use ElevenPaths\Latch\LatchAuth as LatchAuth;
	use ElevenPaths\Latch\LatchApp as LatchApp;
	use ElevenPaths\Latch\Latch as Latch;
	use ElevenPaths\Latch\LatchUser as LatchUser;
	require_once 'latch/LatchAuth.php';
	require_once 'latch/LatchApp.php';
	require_once 'latch/Latch.php';
	require_once 'latch/LatchUser.php';
	require_once 'latch/Error.php';
	require_once 'latch/LatchResponse.php';

	class Model {
		public $config = array();
		private $routers = array();
		private $apiLatch = '';

		public function __construct(){
			$AppConfig = new AppConfig(CONFIG_PATH);
			$AppConfig->open();
			$this->config = $AppConfig->get();
			if ($this->config['pass'] != '') {
				$this->config['pass'] = $this->decrypt(CFG_SECRET, $this->config['pass']);
			}
			
			$this->readRouters();

			$this->apiLatch = new Latch(LATCH_APP, LATCH_SECRET);
		}

		private function readRouters() {
			$scanned_directory = array_diff(scandir(ROUTERS_PATH), array('..', '.'));
			$this->routers = array();
			foreach($scanned_directory as $value) {
				$router = explode(".", $value);
				if ($router[2] == 'php') {
					$this->routers[] = $router[1];
				}
			}
		}

		private function encrypt($password, $data) {
			$salt = substr(md5(mt_rand(), true), 8);
			$key = md5($password . $salt, true);
			$iv = md5($key . $password . $salt, true);
			$ct = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
			return base64_encode('Salted__' . $salt . $ct);
		}

		private function decrypt($password, $data) {
			$data = base64_decode($data);
			$salt = substr($data, 8, 8);
			$ct = substr($data, 16);
			$key = md5($password . $salt, true);
			$iv = md5($key . $password . $salt, true);
			$pt = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ct, MCRYPT_MODE_CBC, $iv);
			return trim($pt);
		}

		public function getRouters() {
			return $this->routers;
		}

		public function getStatusLatchOperations($operations) {
			$StatusLatchOperations = array();
			foreach($operations as $value) {
				$operation_status = $this->apiLatch->operationStatus($this->decrypt(CFG_SECRET, $this->config['accountId']), $value);
				if ($operation_status->data->operations->$value->status != '') {
					$StatusLatchOperations[$value] = $operation_status->data->operations->$value->status;
				}
				else {
					$StatusLatchOperations[$value] = 'unknown';
				}
			}
			return $StatusLatchOperations;
		}

		public function saveConfiguration() {
			if ((isset($_POST['router'])) and (isset($_POST['router-ip'])) and (isset($_POST['user']))) {
				$process = true;
				$router = trim($_POST['router']);
				if (file_exists(ROUTERS_PATH.'class.'.$router.'.php') == false) {
					$process = false;
				}
				$router_ip = trim($_POST['router-ip']);
				if (preg_match(HD_EXP_IP, $router_ip) == false) {
					$process = false;
				}
				$user = trim($_POST['user']);
				if ($user == '') {
					$process = false;
				}
				$pass = trim($_POST['pass']);
				$remote_ip = trim($_POST['remote-ip']);
				if ($remote_ip != '') {
					if (preg_match(HD_EXP_IP, $remote_ip) == false) {
						$process = false;
					}
				}
				$port = trim($_POST['port']);
				if ($port != '') {
					if (($port < 1) or ($port > 65535)) {
						$process = false;
					}
				}
				$mac = trim($_POST['mac']);
				if ($mac != '') {
					if (preg_match(HD_EXP_MAC, $mac) == false) {
						$process = false;
					}
				}
				if ($process) {
					$this->config['router'] = $router;
					$this->config['router_ip'] = $router_ip;
					$this->config['user'] = $user;
					if ($pass != '') {
						$this->config['pass'] = $this->encrypt(CFG_SECRET, $pass);
					}
					else {
						$this->config['pass'] = $pass;						
					}
					$this->config['remote_ip'] = $remote_ip;
					$this->config['port'] = $port;
					$this->config['mac'] = strtoupper($mac);
					$AppConfig = new AppConfig(CONFIG_PATH);
					if ($AppConfig->save($this->config)) {
						$_SESSION['msg'] = array('success', 'Configuration Saved!');
					}
					else {
						$_SESSION['msg'] = array('error', 'Save Error!');
					}
				}
				else {
					$_SESSION['msg'] = array('error', 'Process Error!');
				}
			}
			header("Location: index.php");
			exit();
		}

		public function pair() {
			if (isset($_POST['pairing-code'])) {
				$pairing_code = trim($_POST['pairing-code']);
				if (($pairing_code != '') and (strlen($pairing_code) >= 6)) {
					$pairing_response = $this->apiLatch->pair($pairing_code);
					if ((isset($pairing_response->data->accountId) and ($pairing_response->data->accountId != ''))) {
						$this->config['accountId'] = $this->encrypt(CFG_SECRET, $pairing_response->data->accountId);
						$AppConfig = new AppConfig(CONFIG_PATH);
						$AppConfig->save($this->config);
						header("Location: index.php");
						exit();
					}
					else {
						$_SESSION['msg'] = array('error', 'Pairing token not found or expired');
					}
				}
				else {
					$_SESSION['msg'] = array('error', 'The code is invalid!');
				}
			}
			header("Location: index.php");
			exit();
		}

		public function unpair() {
			$unpair_response = $this->apiLatch->unpair($this->decrypt(CFG_SECRET, $this->config['accountId']));
			if ($unpair_response->data == '') {
				$this->config['accountId'] = '';
				$AppConfig = new AppConfig(CONFIG_PATH);
				$AppConfig->save($this->config);
				header("Location: index.php");
				exit();
			}
		}

		function __destruct() {
			unset($this);
		}
	}

?>