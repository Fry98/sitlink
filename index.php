<?php
	// Chat redirect
	session_start();
	if (!empty($_SESSION)) {
		header('Location: /c/nexus');
		die();
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="theme-color" content="#1c7ec0" />
	<link rel="stylesheet" href="/css/login.min.css">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="shortcut icon" type="image/png" href="/assets/favicon.png"/>
	<title>SITLINK</title>
</head>
<body>
	<!-- Logo for the desktop vesrion -->
	<img id='logo' src="/assets/logo.png" alt='Website Logo'>
	<!-- Login box -->
	<div id='log-scr'>
		<h2>Welcome back!</h2>
		<h3>You are just one click away...</h3>
		<form method='POST'>
			<!-- Logo for the mobile version -->
			<img src="/assets/logo.png" alt='Website Logo'>
			<!-- Input fields -->
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
	<!-- Background layer -->
  <div id="particles-js"></div>
	<script src='/js/login.js'></script>
</body>
</html>
