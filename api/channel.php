<?php
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
  case 'POST':
    channelPost($conn);
    break;
  case 'DELETE':
    channelDelete($conn);
    break;
}

// Error fallback
http_response_code(400);
die('Invalid API Request');


// Handling POST requests
function channelPost($conn) {

  // Checks request validity
  if (isset($_POST['sub']) && isset($_POST['chan']) && strlen($_POST['chan']) >= 3 && strlen($_POST['chan']) <= 20 && preg_match('/^[a-z0-9\-_]*$/', $_POST['chan'])) {

    // Checks Subchat ID validity
    $query = $conn->prepare('SELECT admin FROM subs WHERE id = :sub');
    $query->execute([
      'sub' => $_POST['sub']
    ]);
    $res = $query->fetch();
    if ($res === false) {
      http_response_code(404);
      die("Subchat doesn't exist!");
    } else if ($res[0] !== $_SESSION['id']) {
      http_response_code(403);
      die("Channel Insertion Forbidden");
    }

    // Checks channel name validity
    $query = $conn->prepare('SELECT COUNT(*) FROM chans WHERE sub_id = :sub AND chan_name = :chan');
    $query->execute([
      'sub' => $_POST['sub'],
      'chan' => $_POST['chan']
    ]);
    $res = $query->fetch();
    if ($res[0] > 0) {
      http_response_code(409);
      die("Channel name is already used!");
    }

    // Inserts channel into the DB
    $query = $conn->prepare('INSERT INTO chans (sub_id, chan_name) VALUES (:sub, :chan)');
    $query->execute([
      'sub' => $_POST['sub'],
      'chan' => $_POST['chan']
    ]);

    die();
  }
}

// Handling DELETE requests
function channelDelete($conn) {

  // Inital setup
  $data = file_get_contents("php://input");
  $_DELETE;
  parse_str($data, $_DELETE);
  if (isset($_DELETE['sub']) && isset($_DELETE['chan'])) {

    // Checking user permissions
    $query = $conn->prepare('SELECT admin FROM subs WHERE id = :sub');
    $query->execute([
      'sub' => $_DELETE['sub']
    ]);
    $res = $query->fetch();
    if ($res[0] !== $_SESSION['id']) {
      http_response_code(403);
      die('Channel Deletion Forbidden');
    }

    // Checking Subchat channels
    $query = $conn->prepare('SELECT COUNT(*) FROM chans WHERE sub_id = :sub');
    $query->execute([
      'sub' => $_DELETE['sub']
    ]);
    $res = $query->fetch();
    if ($res[0] < 2) {
      http_response_code(403);
      die('Subchat has to contain at least one channel!');
    }

    // Removing all the messages in the channel
    $query = $conn->prepare('DELETE FROM messages WHERE sub_id = :sub AND channel = :chan');
    $query->execute([
      'sub' => $_DELETE['sub'],
      'chan' => $_DELETE['chan']
    ]);

    // Removing the channel
    $query = $conn->prepare('DELETE FROM chans WHERE sub_id = :sub AND chan_name = :chan');
    $query->execute([
      'sub' => $_DELETE['sub'],
      'chan' => $_DELETE['chan']
    ]);
    $rows = $query->rowCount();
    if ($rows === 0) {
      http_response_code(404);
      die("Channel doesn't exist!");
    }
    
    die();
  }
}