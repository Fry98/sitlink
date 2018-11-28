<?php
	if (isset($_POST['nick']) && isset($_POST['mail']) && isset($_POST['pwd']) && $_POST['pic']) {
		$nick = $_POST['nick'];
		$mail = $_POST['mail'];
		$pwd = $_POST['pwd'];
		$pic = $_POST['pic'];
		// TODO: Handle request
		http_response_code(403);
		die('User already exists!');
	}
?>