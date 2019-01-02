<?php
require_once '../lib/upload.php';

// Checks whether the request contains all the required fields
if (isset($_POST['nick']) && isset($_POST['mail']) && isset($_POST['pwd']) && $_POST['pic']) {
	$nick = htmlspecialchars($_POST['nick']);
	$mail = htmlspecialchars($_POST['mail']);
	$pwd = htmlspecialchars($_POST['pwd']);
	$pic = htmlspecialchars($_POST['pic']);

	// Checks request validity
	if (strlen($nick) < 3 || strlen($nick) > 30) {
		http_response_code(400);
		die('Invalid nickname!');
	}
	if (strlen($pwd) < 6 || strlen($pwd) > 30) {
		http_response_code(400);
		die("Invalid password!");
	}
	if (!preg_match("/^(.+)@(.+)\.(.+)$/", $mail) || strlen($mail) > 30) {
		http_response_code(400);
		die("Invalid e-mail address!");
	}
	if (strlen($pic) > 2097152) {
		http_response_code(400);
		die('Selected image file is too big!');
	}

	// Sets up the MySQL connection
	$conn = new PDO('mysql:host=localhost;dbname=' . getenv('MYSQL_DB'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWD'));
	$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Checks for dupliate username
	$query = $conn->prepare("SELECT COUNT(*) FROM users WHERE nick = :nick");
	$query->execute(array(
		'nick' => $nick
	));
	$res = $query->fetch();
	if ($res[0] > 0) {
		http_response_code(409);
		die('Nickname is already taken!');
	}

	// Uploads the submitted profile picture to Imgur
	$imgId = imgurUpload($pic);
	if ($imgId === null) {
		http_response_code(400);
		die('Invalid image file!');
	}

	// Hash password
	$pwdHash = password_hash($pwd, PASSWORD_DEFAULT);
	
	// Adds new user to the database
	$query = $conn->prepare("INSERT INTO users (id, nick, mail, img, password) VALUES (UUID_SHORT(), :nick, :mail, :img, :pwd)");
	$query->execute(array(
		'nick' => $nick,
		'mail' => $mail,
		'img' => $imgId,
		'pwd' => $pwdHash
	));
	die();
} else {
	// Bad request handling
	http_response_code(400);
	die('Invalid request!');
}