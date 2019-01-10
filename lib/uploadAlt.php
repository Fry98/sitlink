<?php
// Uploads image via Imgur API and returns its ID 
// (Alternative version without cURL)
function imgurUpload($pic, $IMGUR_TOKEN) {
	// Checks file data type and size
	if (substr($pic, 5, 5) !== 'image' || strlen($pic) > 5242880) {
		return null;
	}
  $pic = substr($pic, strpos($pic, ',') + 1);

	// Makes POST request to Imgur API with the image encoded in Base64
  $url = 'https://api.imgur.com/3/image';
  $opts = array(
    'http' => array(
      'header'  => ("Content-type: application/x-www-form-urlencoded\r\nAuthorization: Client-ID " . $IMGUR_TOKEN),
      'ignore_errors' => true,
      'method'  => 'POST',
      'content' => $pic
    )
  );
  $context  = stream_context_create($opts);
  $res = file_get_contents($url, false, $context);

  // Parses JSON response and returns ID of the image
  $res = json_decode($res);
  if ($res->success === false) {
    return null;
  }
  return $res->data->id;
}