<?php
require_once '../lib/env.php';
session_start();

// Checks for API access permission
if (empty($_SESSION)) {
  http_response_code(403);
  die('API Access Forbidden');
}

// Sets up the MySQL connection
$conn = new PDO('mysql:host=localhost;charset=utf8mb4;dbname=' . $MYSQL_DB, $MYSQL_USER, $MYSQL_PASSWD);
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Request handling
switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST':
    subchatPost($conn);
    break;
  case 'DELETE':
    subchatDelete($conn);
    break;
}

// Error fallback
http_response_code(400);
die('Invalid API Request');


// Handling POST requests
function subchatPost($conn) {

  // Checking request validity
  if (isset($_POST['url']) && isset($_POST['title']) && isset($_POST['desc'])) {
    $url = $_POST['url'];
    $title = htmlspecialchars($_POST['title']);
    $desc = htmlspecialchars($_POST['desc']);

    if (strlen($url) < 3 || strlen($url) > 30 || strlen($title) < 3 || strlen($title) > 50 || strlen($desc) < 10 || strlen($desc) > 100 || !preg_match('/^[a-z0-9\-_]*$/', $url)) {
      return;
    }

    // Checks whether the URL is taken
    $query = $conn->prepare('SELECT COUNT(*) FROM subs WHERE id = :url');
    $query->execute([
      'url' => $url
    ]);
    $res = $query->fetch();
    if ($res[0] !== 0) {
      http_response_code(409);
      die('Subchat URL is already taken!');
    }

    // Inserts new Subchat into the DB
    $query = $conn->prepare('INSERT INTO subs (id, title, description, admin) VALUES (:url, :title, :desc, :admin)');
    $query->execute([
      'url' => $url,
      'title' => $title,
      'desc' => $desc,
      'admin' => $_SESSION['id']
    ]);

    // Inserts the default channel for new Subchat
    $query = $conn->prepare('INSERT INTO chans (sub_id, chan_name) VALUES (:url, :chan)');
    $query->execute([
      'url' => $url,
      'chan' => 'default'
    ]);
    
    die();
  }
}

// Handling DELETE requests
function subchatDelete($conn) {

  // Inital setup
  $data = file_get_contents("php://input");
  $_DELETE;
  parse_str($data, $_DELETE);
  if (isset($_DELETE['sub'])) {

    // Checking user permissions and request validity
    $query = $conn->prepare('SELECT admin FROM subs WHERE id = :sub');
    $query->execute([
      'sub' => $_DELETE['sub']
    ]);
    $res = $query->fetch();
    if ($res === false) {
      http_response_code(404);
      die("Subchat doesn't exist!");
    }
    if ($res[0] !== $_SESSION['id']) {
      http_response_code(403);
      die('Subchat Deletion Forbidden');
    }

    // Deleting all Subchat follows
    $query = $conn->prepare('DELETE FROM follows WHERE sub_id = :sub');
    $query->execute([
      'sub' => $_DELETE['sub']
    ]);

    // Deleting all Subchat's channels
    $query = $conn->prepare('DELETE FROM chans WHERE sub_id = :sub');
    $query->execute([
      'sub' => $_DELETE['sub']
    ]);

    // Deleting all Subchat's messages
    $query = $conn->prepare('DELETE FROM messages WHERE sub_id = :sub');
    $query->execute([
      'sub' => $_DELETE['sub']
    ]);

    // Deleting a record of the Subchat from the DB
    $query = $conn->prepare('DELETE FROM subs WHERE id = :sub');
    $query->execute([
      'sub' => $_DELETE['sub']
    ]);

    die();
  }
}