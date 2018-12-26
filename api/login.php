<?php
session_start();

// Checks request validity
if (isset($_POST['nick']) && isset($_POST['pwd'])) {
	$nick = htmlspecialchars($_POST['nick']);
	$pwd = htmlspecialchars($_POST['pwd']);

	// Connects to MySQL database
	$conn = new PDO('mysql:host=localhost;dbname=sitlink', getenv('MYSQL_USER'), getenv('MYSQL_PASSWD'));
	$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Verification of the credentials
	$query = $conn->prepare("SELECT * FROM users WHERE nick = :nick");
	$query->execute(array(
		'nick' => $nick
	));
	$res = $query->fetch();
	if (empty($res) || !password_verify($pwd, $res['password'])) {
		http_response_code(401);
		die("Incorrect username or password!");
	}
	
	// Setting up the session
	$_SESSION['id'] = $res['id'];
	$_SESSION['nick'] = $res['nick'];
	$_SESSION['img'] = $res['img'];
} else {
	// Bad request handling
	http_response_code(400);
	die('Invalid request!');
}