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

// Checks request validity
if (isset($_GET['sub']) && isset($_GET['chan']) && isset($_GET['last']) && ctype_digit($_GET['last'])) {

  // Fetches all the lastest messages from the DB
  $query = $conn->prepare("SELECT messages.id, users.nick, users.img, messages.image, messages.content FROM messages INNER JOIN users ON messages.sender = users.id WHERE messages.sub_id = :sub AND messages.channel = :chan AND messages.id > :lastId");
  $query->execute([
    "sub" => $_GET['sub'],
    "chan" => $_GET['chan'],
    "lastId" => $_GET['last']
  ]);
  $res = $query->fetchAll();

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