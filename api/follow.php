<?php
session_start();

// Checks for API access permission
if (empty($_SESSION)) {
  http_response_code(403);
  die('API Access Forbidden');
}

// Sets up the MySQL connection
$conn = new PDO('mysql:host=localhost;dbname=' . getenv('MYSQL_DB'), getenv('MYSQL_USER'), getenv('MYSQL_PASSWD'));
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Request handling
switch ($_SERVER['REQUEST_METHOD']) {
  case 'GET':
    $json = [];

    // Fetch all the owned subchats
    $query = $conn->prepare('SELECT id, title, description FROM subs WHERE admin = :uid');
    $query->execute(array(
      "uid" => $_SESSION['id']
    ));
    $res = $query->fetchAll();
    $json['owned'] = queryToArray($res);

    // Fetch all the followed subchats
    $query = $conn->prepare('SELECT subs.id, subs.title, subs.description FROM follows INNER JOIN subs ON follows.sub_id=subs.id WHERE follows.user_id = :uid');
    $query->execute(array(
      "uid" => $_SESSION['id']
    ));
    $res = $query->fetchAll();
    $json['followed'] = queryToArray($res);
    die(json_encode($json));

  case 'POST':
    // Checks request validity
    if (isset($_POST['sub'])) {

      // Checks whether the user is admin of a subchat
      $query = $conn->prepare("SELECT admin FROM subs WHERE id = :id");
      $query->execute(array(
        "id" => $_POST['sub']
      ));
      $res = $query->fetch();
      if ($res[0] === $_SESSION['id']) {
        break;
      }

      // Removes subchat from user's follows
      $query = $conn->prepare("DELETE FROM follows WHERE user_id = :uid AND sub_id = :sub");
      $query->execute(array(
        "uid" => $_SESSION['id'],
        "sub" => $_POST['sub']
      ));
      $rows = $query->rowCount();
      if ($rows > 0) {
        die();
      }

      // Adds subchat into user's follows
      $query = $conn->prepare("INSERT INTO follows (user_id, sub_id) VALUES (:user, :sub)");
      $query->execute(array(
        "user" => $_SESSION['id'],
        "sub" => $_POST['sub']
      ));
      die();
    }
}

// Error fallback
http_response_code(400);
die('Invalid API Request');

function queryToArray($res) {
  $arr = [];
  foreach ($res as $item) {
    $sub = array(
      'id' => $item['id'],
      'title' => $item['title'],
      'desc' => $item['description']
    );
    $arr[] = $sub;
  }
  return $arr;
}