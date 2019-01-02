<?php

// PHP implementation of String.prototype.substring() from JS
function substring($str, $start, $end) {
  return substr($str, $start, $end - $start);
}

// Runs all the markdown functions
function markdown($str) {
  $str = pairUp($str, '**', '<strong>', '</strong>');
  $str = pairUp($str, '*', '<em>', '</em>');
  $str = pairUp($str, '__', '<u>', '</u>');
  $str = pairUp($str, '~~', '<s>', '</s>');
  $str = findLinks($str);
  $str = escapeCleanup($str);
  return $str;
}

// Find pairs of markdown tags and replace them with HTML tags
function pairUp($str, $trigger, $tagStart, $tagEnd) {
  $offset = strlen($trigger);
  $startEndToggle = false;
  $startPos;

  for ($i = 0; $i <= (strlen($str) - $offset); $i++) {
    if (substr($str, $i, $offset) === $trigger) {
      if (!$startEndToggle && substr($str, $i + $offset, 1) !== substr($trigger, 0, 1)) {
        $startEndToggle = true;
        $startPos = $i;
      } else if ($startEndToggle) {
        $str = substring($str, 0, $startPos) . $tagStart . substring($str, $startPos + $offset, $i) . $tagEnd . substring($str, $i + $offset, strlen($str));
        $startEndToggle = false;
      }
    }
    if (substr($str, $i, 1) === '\\') {
      $i++;
    }
  }
  return $str;
}

// Cleans up the mess left after backslashes
function escapeCleanup($str) {
  for ($i = 0; $i < strlen($str); $i++) {
    if (substr($str, $i, 1) === '\\') {
      $str = substring($str, 0, $i) . substr($str, $i + 1);
    }
  }
  return $str;
}

// Finds links and puts them into anchor tags
function findLinks($str) {
  // Well... this regex doesn't always work properly but hey... good enough for me...
  $regex = '/(?<=\b)https?:\/\/(www\.)?[a-z0-9\-_\.]{2,256}\.[a-z]{2,6}([a-z0-9\/#?&=\-_]+)?(?=\b)/i';
  $links;
  preg_match_all($regex, $str, $links, PREG_OFFSET_CAPTURE);
  $newStr = '';
  $pos = 0;
  foreach ($links[0] as $link) {
    $newStr .= substring($str, $pos, $link[1]);
    $newStr .= ("<a href='" . $link[0] . "' rel='noopener' target='_blank'>");
    $newStr .= $link[0];
    $newStr .= '</a>';
    $pos = $link[1] + strlen($link[0]);
  }
  $newStr .= substr($str, $pos);
  return $newStr;
}