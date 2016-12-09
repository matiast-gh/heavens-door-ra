<?php

	session_start();

	require_once 'app/config.php';
	require_once 'app/controller.php';

	$controller = new Controller();
	$controller->handle(trim($_POST['action']));

?>