<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  	<!-- TODO: MODIFY THIS FOR PROD -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link rel="stylesheet" href="css/chat.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <title>SITLINK</title>
</head>
<body>
  <div id='popup' class='popup-hide'>
    <img src="assets/nms.jpg" alt='User Image'>
  </div>
  <header>
    HEADER
  </header>
  <main>
    <aside id='sidebar'>
      <ul id='chans'>
        <li class='selected'>#general</li>
        <li>#videogames</li>
        <li>#coding</li>
        <li>#mockup</li>
        <li>#anime</li>
      </ul>
    </aside>
    <div id='chat'>
      <div id='content'>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/assets/ph_pi.png");'></div>
            <span>Fryer Fry</span>
          </div>
          <div class='msg-text'>
            L
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/assets/nice.jpg");'></div>
            <span>one chemistry boi</span>
          </div>
          <div class='msg-text'>
            <img src="assets/nms.jpg" alt='User Image'>
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/assets/ph_pi.png");'></div>
            <span>Fryer Fry</span>
          </div>
          <div class='msg-text'>
            Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec vitae arcu. Maecenas libero. Curabitur sagittis hendrerit ante. Integer tempor. Nunc auctor. Curabitur ligula sapien, pulvinar a vestibulum quis, facilisis vel sapien. Nunc auctor. Maecenas sollicitudin. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Maecenas fermentum, sem in pharetra pellentesque, velit turpis volutpat ante, in pharetra metus odio a lectus. Praesent vitae arcu tempor neque lacinia pretium. Nulla pulvinar eleifend sem.
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/assets/nice.jpg");'></div>
            <span>one chemistry boi</span>
          </div>
          <div class='msg-text'>
            Maecenas libero. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. Fusce nibh. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Suspendisse sagittis ultrices augue. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Duis pulvinar. Praesent vitae arcu tempor neque lacinia pretium. Nulla accumsan, elit sit amet varius semper, nulla mauris mollis quam, tempor suscipit diam nulla vel leo. Mauris elementum mauris vitae tortor.
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/assets/ph_pi.png");'></div>
            <span>Fryer Fry</span>
          </div>
          <div class='msg-text'>
            Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec vitae arcu. Maecenas libero. Curabitur sagittis hendrerit ante. Integer tempor. Nunc auctor. Curabitur ligula sapien, pulvinar a vestibulum quis, facilisis vel sapien. Nunc auctor. Maecenas sollicitudin. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Maecenas fermentum, sem in pharetra pellentesque, velit turpis volutpat ante, in pharetra metus odio a lectus. Praesent vitae arcu tempor neque lacinia pretium. Nulla pulvinar eleifend sem.
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/assets/nice.jpg");'></div>
            <span>one chemistry boi</span>
          </div>
          <div class='msg-text'>
            Maecenas libero. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. Fusce nibh. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Suspendisse sagittis ultrices augue. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Duis pulvinar. Praesent vitae arcu tempor neque lacinia pretium. Nulla accumsan, elit sit amet varius semper, nulla mauris mollis quam, tempor suscipit diam nulla vel leo. Mauris elementum mauris vitae tortor.
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/assets/nice.jpg");'></div>
            <span>one chemistry boi</span>
          </div>
          <div class='msg-text'>
            <img src="assets/nice.jpg" alt='User Image'>
          </div>
        </div>
      </div>
      <div id='msg-box-wrap'>
        <div id='msg-box'>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 7v2.99s-1.99.01-2 0V7h-3s.01-1.99 0-2h3V2h2v3h3v2h-3zm-3 4V8h-3V5H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-8h-3zM5 19l3-4 2 3 3-4 4 5H5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
            <textarea rows="1" placeholder="Enter your message..."></textarea>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>    
        </div>        
      </div>
    </div>
  </main>
  <script src="js/chat.js"></script>
</body>
</html>