<?php

function authCheck() {
  if (!empty($_SESSION)) {
    if (!isset($_SESSION['app']) || $_SESSION['app'] !== 'sitlink' || !isset($_SESSION['id']) || !isset($_SESSION['nick']) || !isset($_SESSION['img'])) {
      session_destroy();
    } else {
      return;
    }
  }
  http_response_code(403);
  die('API Access Forbidden');
}
