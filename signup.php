<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="theme-color" content="#1c7ec0" />
	<link rel="stylesheet" href="/css/signup.min.css">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link rel="shortcut icon" type="image/png" href="/assets/favicon.png"/>
	<title>SITLINK</title>
</head>
<body>
	<!-- Loading animation after form submission -->
	<div id='loader'>
		<svg width="200px"  height="200px"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-rolling" style="background: none;"><circle cx="50" cy="50" fill="none" stroke="rgb(28, 126, 192)" stroke-width="15" r="40" stroke-dasharray="188.49555921538757 64.83185307179586" transform="rotate(163.515 50 50)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1.1s" begin="0s" repeatCount="indefinite"></animateTransform></circle></svg>
	</div>
	<img id='logo' src="/assets/logo.png" alt='Website Logo'>
	<!-- Singup box -->
	<div id='log-scr'>
		<div id='main-wrap'>
			<h2>Sign Up</h2>
			<form>
				<!-- File selector for profile picture -->
				<input type="file" name="img-sel" id='img-sel' accept="image/*">
				<!-- Input fields -->
				<div id='flex-wrap'>
					<div id='right'>
							<div class='inp-box'>
								<label for="nick">USERNAME</label>
								<input id='nick' type="text" class='inp-fld' name='nick' required>
							</div>
							<div class='inp-box'>
								<label for="mail">E-MAIL</label>
								<input id='mail' type="email" class='inp-fld' name='mail' required>
							</div>
							<div class='inp-box'>
								<label for="pwd">PASSWORD</label>
								<input id='pwd' type="password" class='inp-fld' name='pwd' required>
							</div>
							<div class='inp-box'>
								<label for="pwd-con">CONFIRM PASSWORD</label>
								<input id='pwd-con' type="password" class='inp-fld' name='pwd-con' required>
							</div>
						</div>
						<!-- Profile picture box -->
						<div id='left'>
							<div id="pick-wrap">
								<label>PROFILE PICTURE</label>
								<div id='picker' onclick="$('#img-sel').click()">
									<div id='pick-ui'>
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z"/></svg>
									</div>
									<div id='pick-bg'></div>
								</div>
							</div>
						</div>
				</div>
				<!-- Action buttons -->
				<div id='sub-wrap'>
					<input class='cancel' type="button" value="Go Back">
					<input type="submit" value="Create Account">
				</div>
			</form>		
		</div>
	</div>
  <div id="particles-js"></div>
	<script src='/js/signup.js'></script>
</body>
</html>
