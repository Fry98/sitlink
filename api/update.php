<?php
require_once '../lib/env.php';
session_start();

// Checks for API access permission
if (empty($_SESSION)) {
  http_response_code(403);
  die('API Access Forbidden');
}

// Checks request validity
if (isset($_GET['sub']) && isset($_GET['chan']) && isset($_GET['last']) && ctype_digit($_GET['last'])) {
  
  // Sets up the MySQL connection
  $conn = new PDO('mysql:host=localhost;charset=utf8mb4;dbname=' . $MYSQL_DB, $MYSQL_USER, $MYSQL_PASSWD);
  $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Checks Subchat ID validity
  $query = $conn->prepare("SELECT COUNT(*) FROM subs WHERE id = :sub");
  $query->execute(array(
    "sub" => $_GET['sub']
  ));
  $res = $query->fetch();
  if ($res[0] === 0) {
    http_response_code(404);
    die("Subchat doesn't exist!");
  }

  // Checks channel validity
  $query = $conn->prepare("SELECT COUNT(*) FROM chans WHERE sub_id = :sub AND chan_name = :chan");
  $query->execute(array(
    "sub" => $_GET['sub'],
    "chan" => $_GET['chan']
  ));
  $res = $query->fetch();
  if ($res[0] === 0) {
    http_response_code(404);
    die("Channel doesn't exist!");
  }

  // Fetches all the lastest messages from the DB
  $query = $conn->prepare("SELECT messages.id, users.nick, users.img, messages.image, messages.content FROM messages INNER JOIN users ON messages.sender = users.id WHERE messages.sub_id = :sub AND messages.channel = :chan AND messages.id > :lastId");
  $query->execute([
    "sub" => $_GET['sub'],
    "chan" => $_GET['chan'],
    "lastId" => $_GET['last']
  ]);
  $res = $query->fetchAll();

  // Forms the response object
  $json = [];
  foreach ($res as $row) {
    $isImg = true;
    if ($row[3] === 0) {
      $isImg = false;
    }
    $temp = [
      "id" => $row[0],
      "nick" => $row[1],
      "upic" => $row[2],
      "img" => $isImg,
      "content"=> $row[4]
    ];
    $json[] = $temp;
  }
  die(json_encode($json));
}

// Error fallback
http_response_code(400);
die('Invalid API Request');