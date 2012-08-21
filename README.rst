--------------------------
Thunderpush client for PHP
--------------------------

A PHP library for sending messages to the `Thunderpush <https://github.com/thunderpush/thunderpush>`_ server.

Example
=======

::

	<?php
	
	require_once('Thunder.php');

	$thunder = new Thunder('key', 'secretkey', 'localhost', '8080');

	print $thunder->get_user_count();
	print $thunder->get_users_in_channel("test");
	print $thunder->send_message_to_user("test", array("msg" => "hello!"));
	print $thunder->send_message_to_channel("test", array("msg" => "hello!"));
	print $thunder->is_user_online("test");
