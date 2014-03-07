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

	// Get user count
	print $thunder->get_user_count();

	// Get users in channel "test"
	print $thunder->get_users_in_channel("test");

	// Send message to user "test"
	print $thunder->send_message_to_user("test", array("msg" => "hello!"));

	// Send message to a channel
	print $thunder->send_message_to_channel("test", array("msg" => "hello!"));

	// Check if user "test" is online
	print $thunder->is_user_online("test");

	// Disconnect user "test"
	print $thunder->disconnect_user("test");
