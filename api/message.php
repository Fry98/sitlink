<?php
require_once '../lib/upload.php';
session_start();

// Checks for API access permission
if (empty($_SESSION)) {
  http_response_code(403);
  die('API Access Forbidden');
}

// Sets up the MySQL connection
$conn = new PDO('mysql:host=localhost;dbname=sitlink', getenv('MYSQL_USER'), getenv('MYSQL_PASSWD'));
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Request handling
switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    
    // Checks request validity
    if (isset($_GET['sub']) && isset($_GET['chan']) && isset($_GET['lim']) && isset($_GET['skip'])) {
      $lim = $_GET['lim'];
      $skip = $_GET['skip'];
      if (ctype_digit($lim) && ctype_digit($skip)) {
        
        // Man,... fuck SQL...
        $query = $conn->prepare("SELECT users.nick, users.img, messages.image, messages.content, messages.id FROM messages INNER JOIN users ON messages.sender = users.id WHERE messages.sub_id = :sub AND messages.channel = :chan ORDER BY messages.id DESC LIMIT :lim OFFSET :skip");
        $query->execute(array(
          "sub" => $_GET['sub'],
          "chan" => $_GET['chan'],
          "lim" => $lim,
          "skip" => $skip
        ));
        $res = $query->fetchAll();
        
        // Forming the response object
        $json = [];
        foreach ($res as $row) {
          $isImg = true;
          if ($row[2] === 0) {
            $isImg = false;
          }
          $temp = [
            "id" => $row[4],
            "nick" => $row[0],
            "upic" => $row[1],
            "img" => $isImg,
            "content"=> $row[3]
          ];
          $json[] = $temp;
        }
        die(json_encode($json));
      }
    }
    break;
  case 'POST':
    // Checks request validity
    if (isset($_POST['sid']) && isset($_POST['chan']) && isset($_POST['img']) && isset($_POST['content'])) {

      // Checks Subchat id validity
      $query = $conn->prepare("SELECT COUNT(*) FROM subs WHERE id = :sid");
      $query->execute(array(
        "sid" => $_POST['sid']
      ));
      $res = $query->fetch();
      if ($res[0] === 0) {
        http_response_code(400);
        die("Invalid Subchat ID");
      }

      // Checks channel validity
      $query = $conn->prepare("SELECT COUNT(*) FROM chans WHERE sub_id = :sid AND chan_name = :chan");
      $query->execute(array(
        "sid" => $_POST['sid'],
        "chan" => $_POST['chan']
      ));
      $res = $query->fetch();
      if ($res[0] === 0) {
        http_response_code(400);
        die('Invalid channel name');
      }

      // Uploads image via Imgur API and prepares message text
      $content = $_POST['content'];
      $imgBool = false;
      if ($_POST['img'] === "true") {
        $content = imgurUpload($content);
        $imgBool = true;
      } else {
        $content = trim($content);
        if (strlen($content) === 0) {
          http_response_code(400);
          die("Message can't be empty!");
        }
        $content = htmlspecialchars($content);
        $content = str_replace("\n", '<br>', $content);
      }

      // Adds message to the DB
      $query = $conn->prepare("INSERT INTO messages (sender, sub_id, channel, image, content) VALUES (:uid, :sid, :chan, :img, :cont)");
      $query->execute(array(
        'uid' => $_SESSION['id'],
        'sid' => $_POST['sid'],
        'chan' => $_POST['chan'],
        'img' => $imgBool,
        'cont' => $content
      ));
      $msgId = $conn->lastInsertId();

      // Fetch user data to form a response object
      $query = $conn->prepare("SELECT nick, img FROM users WHERE id = :uid");
      $query->execute(array(
        'uid' => $_SESSION['id']
      ));
      $res = $query->fetch();

      // Forming the response object
      $json = array(
        "id" => $msgId,
        "nick" => $res['nick'],
        "upic" => $res['img'],
        "img" => $imgBool,
        "content" => $content
      );
      die(json_encode($json));
    }
}

// Error fallback
http_response_code(400);
die('Invalid API Request');