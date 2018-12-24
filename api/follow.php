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

  switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
      if (isset($_GET['owned'])) {
        $query = $conn->prepare('SELECT id, title, description FROM subs WHERE admin = :uid');
        $query->execute(array(
          "uid" => $_SESSION['id']
        ));
        $res = $query->fetchAll();
        queryToJSON($res);
        die();
      } else {
        $query = $conn->prepare('SELECT subs.id, subs.title, subs.description FROM follows INNER JOIN subs ON follows.sub_id=subs.id WHERE follows.user_id = :uid');
        $query->execute(array(
          "uid" => $_SESSION['id']
        ));
        $res = $query->fetchAll();
        queryToJSON($res);
        die();
      }
      break;
    case 'POST':
      $query = $conn->prepare("INSERT INTO follows (user_id, sub_id) VALUES (:user, :sub)");
      $query->execute(array(
        "user" => $_SESSION['id'],
        "sub" => $_POST['sub']
      ));
      break;
  }

  function queryToJSON($res) {
    $json = [];
    foreach ($res as $item) {
      $sub = array(
        'id' => $item['id'],
        'title' => $item['title'],
        'desc' => $item['description']
      );
      $json[] = $sub;
    }
    die(json_encode($json));
  }