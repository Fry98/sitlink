<?php
require_once '../lib/latest.php';
session_start();

// Checks for API access permission
if (empty($_SESSION)) {
  http_response_code(403);
  die('API Access Forbidden');
}

// Checks request validity
if (isset($_GET['sub']) && isset($_GET['chan']) && isset($_GET['last']) && ctype_digit($_GET['last'])) {
  
  // Sets up the MySQL connection
  $conn = new PDO('mysql:host=localhost;dbname=sitlink', getenv('MYSQL_USER'), getenv('MYSQL_PASSWD'));
  $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Checks Subchat ID validity
  $query = $conn->prepare("SELECT COUNT(*) FROM subs WHERE id = :sid");
  $query->execute(array(
    "sid" => $_GET['sub']
  ));
  $res = $query->fetch();
  if ($res[0] === 0) {
    http_response_code(400);
    die("Invalid Subchat ID");
  }

  // Checks channel validity
  $query = $conn->prepare("SELECT COUNT(*) FROM chans WHERE sub_id = :sid AND chan_name = :chan");
  $query->execute(array(
    "sid" => $_GET['sub'],
    "chan" => $_GET['chan']
  ));
  $res = $query->fetch();
  if ($res[0] === 0) {
    http_response_code(400);
    die('Invalid channel name');
  }

  // Fetches latest messages and sends them to the client
  getLatest($conn, $_GET['sub'], $_GET['chan'], $_GET['last']);
}

// Error fallback
http_response_code(400);
die('Invalid API Request');