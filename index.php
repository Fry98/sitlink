<?php
	// Chat redirect
	session_start();
	if (!empty($_SESSION)) {
		header('Location: /~tomanfi2/c/nexus');
		die();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="/~tomanfi2/css/login.min.css">
	<!-- TODO: MODIFY THIS FOR PROD -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"> 
	<script src="/~tomanfi2/js/lib/particles.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="shortcut icon" type="image/png" href="/~tomanfi2/assets/favicon.png"/>
	<title>SITLINK</title>
</head>
<body>
	<img id='logo' src="/~tomanfi2/assets/logo.png" alt='Website Logo'>
	<div id='log-scr'>
		<h2>Welcome back!</h2>
		<h3>You are just one click away...</h3>
		<form method='POST'>
			<img src="/~tomanfi2/assets/logo.png" alt='Website Logo'>
			<div class='inp-box'>
				<label for="nick">USERNAME</label>
				<input id='nick' type="text" class='inp-fld' name='nick' required>
			</div>
			<div class='inp-box'>
				<label for="pwd">PASSWORD</label>
				<input id='pwd' type="password" class='inp-fld' name='pwd' required>
			</div>
			<div id='sub-wrap'>
				<input type="submit" value="Login">
			</div>
			<div id='signup'>
				<a href='signup.php'>Don't have an account yet? <span>Sign up!</span></a>
			</div>
		</form>
	</div>
  <div id="particles-js"></div>
	<script src='/~tomanfi2/js/login.js'></script>
</body>
</html>