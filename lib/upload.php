<?php
// Well, apparently Toad doesn't have the cURL module installed so this lib is now unsued
// Keeping it just for the nostalgia tho
function imgurUpload($pic, $IMGUR_TOKEN) {
	
	// Checks file data type
	if (substr($pic, 5, 5) !== 'image') {
		return null;
	}
	$pic = substr($pic, strpos($pic, ',') + 1);

	// Makes POST request to Imgur API with the image encoded in Base64
	$endpoint = 'https://api.imgur.com/3/image';
	$opts = [
		CURLOPT_URL => $endpoint,
		CURLOPT_HTTPHEADER => array("Authorization: Client-ID " . $IMGUR_TOKEN),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $pic
	];
	$curl = curl_init();
	curl_setopt_array($curl, $opts);
	$resJson = curl_exec($curl);

	// Parses JSON response and returns the image ID
	$res = json_decode($resJson);
	if (!$res->success) {
		return null;
	}
	return $res->data->id;
}
