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
  // TODO
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
      http_response_code(400);
      die('Invalid Subchat ID');
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
  }
}