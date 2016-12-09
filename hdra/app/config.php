<?php

	define("TITLE_APP", "Heaven's Door RA");
	define("TEMPLATE_PATH", "templates/");
	define("CONFIG_PATH", "config/config.xml");
	define("CFG_SECRET", "");
	define("ROUTERS_PATH", "app/routers/");

	define("LATCH_APP", "");
	define("LATCH_SECRET", "");
	define("LATCH_OPERATIONS_CFG", "");
	define("LATCH_OPERATIONS_UNPAIR", "");
	define("LATCH_OPERATIONS_RA", "");
	define("LATCH_OPERATIONS_RC", "");
	define("LATCH_OPERATIONS", serialize(array(LATCH_OPERATIONS_CFG => "HDRA Configuration",  LATCH_OPERATIONS_RA => "Remote Access", LATCH_OPERATIONS_RC => "Remote Router Configuration", LATCH_OPERATIONS_UNPAIR => "Unpair")));
	define("LATCH_OPERATIONS_SERVICE", serialize(array(LATCH_OPERATIONS_RA, LATCH_OPERATIONS_RC)));

	define('HD_EXP_IP', '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/');
	define('HD_EXP_PORT', '/^[0-9\d]{1,5}$/i');
	define('HD_EXP_MAC', '/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/');
	define('HD_EXP_ARP_MAC', '/([a-fA-F0-9]{2}[:-]){5}[a-fA-F0-9]{2}/');

	/* WINDOWS */
	define('WOL_COMMAND', 'C:\WakeMeOnLan\WakeMeOnLan.exe /wakeup {mac}');
	/* LINUX */
	/*
	define('WOL_COMMAND', 'wakeonlan {mac}');
	*/
	/* WINDOWS */
	define('ARP_COMMAND', 'arp -a {ip}');
	/* LINUX */
	/*
	define('ARP_COMMAND', 'arp -a {ip}');
	*/

?>