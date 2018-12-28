<?php
function getLatest($conn, $sub, $chan, $last) {

  // Fetches all the lastest messages from the DB
  $query = $conn->prepare("SELECT messages.id, users.nick, users.img, messages.image, messages.content FROM messages INNER JOIN users ON messages.sender = users.id WHERE messages.sub_id = :sub AND messages.channel = :chan AND messages.id > :lastId");
  $query->execute([
    "sub" => $sub,
    "chan" => $chan,
    "lastId" => $last
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