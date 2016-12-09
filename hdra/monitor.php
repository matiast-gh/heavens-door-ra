<?php

	require_once 'app/config.php';
	require_once 'app/controller.php';

	if (in_array(trim($_POST['action']), array('monitor', 'test-login', 'get-mac'))) {
		$controller = new Controller();
		$controller->handle(trim($_POST['action']));
	}

?>