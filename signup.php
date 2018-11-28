<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="css/signup.min.css">
	<!-- TODO: MODIFY THIS FOR PROD -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet"> 
	<script src="js/lib/particles.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<title>SITLINK</title>
</head>
<body>
	<img id='logo' src="assets/logo.png" alt='Website Logo'>
	<div id='log-scr'>
		<div id='main-wrap'>
			<h2>Sign Up</h2>
			<form>
				<input type="file" name="img-sel" id='img-sel' accept="image/*">
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
						<div id='left'>
							<div id="pick-wrap">
								<label>PROFILE PICTURE</label>
								<div id='picker' onclick="document.getElementById('img-sel').click()">
									<div id='pick-ui'>
										<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z"/></svg>
									</div>
									<div id='pick-bg'></div>
								</div>
							</div>
						</div>
				</div>
				<div id='sub-wrap'>
					<input class='cancel' type="button" value="Go Back">
					<input type="submit" value="Create Account">
				</div>
			</form>		
		</div>
	</div>
  <div id="particles-js"></div>
	<script src='js/signup.js'></script>
</body>
</html>