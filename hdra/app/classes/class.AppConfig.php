<?php

	/*

		class.AppConfig por mt

		version 1.0			20161019

	*/
	
	class AppConfig {
		private $file = '';
		private $cfg = array("accountId" => "", "router" => "", "router_ip" => "", "user" => "", "pass" => "", "port" => "", "remote_ip" => "", "mac" => "");

		public function __construct($file = "config/config.xml") {
			$this->file = $file;
		}
		public function open() {
			if (file_exists($this->file)) {
				$doc = new DOMDocument('1.0');
				$doc->load($this->file);
				foreach(array_keys($this->cfg) as $key) {
					$value = '';
					$value = $doc->getElementsByTagName($key);
					$value = $value->item(0)->nodeValue;
					$this->cfg[$key] = $value;
				}
				return true;
			}
			else {
				return false;				
			}
		}
		public function save($cfg) {
			foreach($cfg as $key => $value) {
				$this->cfg[$key] = $value;
			}
			@unlink($this->file.".bak");
			@copy($this->file, $this->file.".bak");
			if ($handle = @fopen($this->file, 'w')) {
				fwrite($handle, '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n");
				fwrite($handle, '<config>' . "\r\n");
				foreach($this->cfg as $key => $value) {
					fwrite($handle, '<' . $key . '>' . $value . '</' . $key .'>' . "\r\n");
				}
				fwrite($handle, '</config>' . "\r\n");
				fclose($handle);
				return true;
			}
			else {
				return false;
			}
		}
		public function get() {
			return $this->cfg;
		}
	}

?>