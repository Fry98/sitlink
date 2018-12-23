<?php
  // Session check
  session_start();
  if (empty($_SESSION)) {
		header('Location: /~tomanfi2');
		die();
  }
  
  // Sets up the MySQL connection
  $conn = new PDO('mysql:host=localhost;dbname=sitlink', 'root', '');
  $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Subchat validity check
  $query = $conn->prepare("SELECT * FROM subs WHERE id = :id");
  $query->execute(array(
    'id' => strtolower($_GET['sub'])
  ));
  $res = $query->fetch();
  if (empty($res)) {
		header('Location: /~tomanfi2/c/nexus');
    die();
  }
  $name = $res[1];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <!-- TODO: MODIFY THIS FOR PROD -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link rel="stylesheet" href="/~tomanfi2/css/chat.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <link rel="shortcut icon" type="image/png" href="/~tomanfi2/assets/favicon.png"/>
  <title>SITLINK</title>
</head>
<body>
  <input type="file" name="img-sel" id='img-sel' accept="image/*">
  <div id='popup' class='popup-hide'>
    <img src="/~tomanfi2/assets/nms.jpg" alt='User Image'>
  </div>
  <div id='flw-overlay'>
    <div id='flw-list'>
      <div id='flw-list-head'>
        <div class='flw-list-option flw-option-active'>Followed</div>
        <div class='flw-list-option'>My Subchats</div>
        <div id='flw-list-filler'></div>
        <div id='flw-list-close'>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
        </div>
      </div>
      <div id='flw-list-content'></div>
    </div>
  </div>
  <header>
    <div id='burger'>
      <svg height="32px" id="Layer_1" style="enable-background:new 0 0 32 32;" version="1.1" viewBox="0 0 32 32" width="32px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M4,10h24c1.104,0,2-0.896,2-2s-0.896-2-2-2H4C2.896,6,2,6.896,2,8S2.896,10,4,10z M28,14H4c-1.104,0-2,0.896-2,2  s0.896,2,2,2h24c1.104,0,2-0.896,2-2S29.104,14,28,14z M28,22H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,2-2  S29.104,22,28,22z"/></svg>
    </div>
    <div id='sub-name'><?= $name ?></div>
    <a id='lo-wrap' href="/~tomanfi2/api/logout.php">
      <div id='logout'>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
      </div>
    </a>
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
      <div id='ctrls'>
        <div id='btn-wrap'>
          <div id='subs'>Subchats</div>
          <div id='flw'>
            <svg class='flw-idle' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M14.07 5.32C16.26 6 18 7.74 18.68 9.93c.19.63.76 1.07 1.41 1.07h.04c1 0 1.72-.96 1.43-1.91-.97-3.18-3.48-5.69-6.66-6.66-.94-.29-1.9.43-1.9 1.43v.04c0 .66.44 1.23 1.07 1.42zm4.61 8.75c-.68 2.2-2.42 3.93-4.61 4.61-.63.19-1.07.76-1.07 1.41v.04c0 1 .96 1.72 1.91 1.43 3.18-.97 5.69-3.48 6.66-6.66.29-.95-.43-1.91-1.42-1.91h-.05c-.66.01-1.23.45-1.42 1.08zM11 20.11c0-.67-.45-1.24-1.09-1.44C7.07 17.78 5 15.13 5 12s2.07-5.78 4.91-6.67c.64-.2 1.09-.77 1.09-1.44v-.01c0-1-.97-1.74-1.93-1.44C4.98 3.69 2 7.5 2 12c0 4.5 2.98 8.31 7.07 9.56.96.3 1.93-.44 1.93-1.45z"/></svg>
            <svg class='flw-invis' xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
            <svg class='flw-hover' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 24 24" version="1.1"><path style=" " d="M 19.28125 5.28125 L 9 15.5625 L 4.71875 11.28125 L 3.28125 12.71875 L 8.28125 17.71875 L 9 18.40625 L 9.71875 17.71875 L 20.71875 6.71875 Z "/></svg>
          </div>
        </div>
      </div>
    </aside>
    <div id='chat'>
      <div id='content'>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/~tomanfi2/assets/ph_pi.png");'></div>
            <span>Fryer Fry</span>
          </div>
          <div class='msg-text'>
            Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec vitae arcu. Maecenas libero. Curabitur sagittis hendrerit ante.
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/~tomanfi2/assets/nice.jpg");'></div>
            <span>one chemistry boi</span>
          </div>
          <div class='msg-text'>
            <img src="/~tomanfi2/assets/nms.jpg" alt='User Image'>
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/~tomanfi2/assets/ph_pi.png");'></div>
            <span>Fryer Fry</span>
          </div>
          <div class='msg-text'>
            Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec vitae arcu. Maecenas libero. Curabitur sagittis hendrerit ante. Integer tempor. Nunc auctor. Curabitur ligula sapien, pulvinar a vestibulum quis, facilisis vel sapien. Nunc auctor. Maecenas sollicitudin. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Maecenas fermentum, sem in pharetra pellentesque, velit turpis volutpat ante, in pharetra metus odio a lectus. Praesent vitae arcu tempor neque lacinia pretium. Nulla pulvinar eleifend sem.
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/~tomanfi2/assets/nice.jpg");'></div>
            <span>one chemistry boi</span>
          </div>
          <div class='msg-text'>
            Maecenas libero. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. Fusce nibh. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Suspendisse sagittis ultrices augue. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Duis pulvinar. Praesent vitae arcu tempor neque lacinia pretium. Nulla accumsan, elit sit amet varius semper, nulla mauris mollis quam, tempor suscipit diam nulla vel leo. Mauris elementum mauris vitae tortor.
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/~tomanfi2/assets/ph_pi.png");'></div>
            <span>Fryer Fry</span>
          </div>
          <div class='msg-text'>
            Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec vitae arcu. Maecenas libero. Curabitur sagittis hendrerit ante. Integer tempor. Nunc auctor. Curabitur ligula sapien, pulvinar a vestibulum quis, facilisis vel sapien. Nunc auctor. Maecenas sollicitudin. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Maecenas fermentum, sem in pharetra pellentesque, velit turpis volutpat ante, in pharetra metus odio a lectus. Praesent vitae arcu tempor neque lacinia pretium. Nulla pulvinar eleifend sem.
          </div>
        </div>
        <div class='msg'>
          <div class='nametag'>
            <div class='pro-img' style='background-image: url("/~tomanfi2/assets/nice.jpg");'></div>
            <span>one chemistry boi</span>
          </div>
          <div class='msg-text'>
            Maecenas libero. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. Fusce nibh. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Suspendisse sagittis ultrices augue. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Duis pulvinar. Praesent vitae arcu tempor neque lacinia pretium. Nulla accumsan, elit sit amet varius semper, nulla mauris mollis quam, tempor suscipit diam nulla vel leo. Mauris elementum mauris vitae tortor.
          </div>
        </div>
      </div>
      <div id='msg-box-wrap'>
        <div id='msg-box'>
            <svg id='img' xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 7v2.99s-1.99.01-2 0V7h-3s.01-1.99 0-2h3V2h2v3h3v2h-3zm-3 4V8h-3V5H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-8h-3zM5 19l3-4 2 3 3-4 4 5H5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
            <textarea id='msg' rows="1" placeholder="Enter your message..."></textarea>
            <svg id='submit' xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>    
        </div>        
      </div>
    </div>
  </main>
  <script>
    const sub = "<?= strtolower($_GET['sub']) ?>";
  </script>
  <script src="/~tomanfi2/js/chat.js"></script>
</body>
</html>