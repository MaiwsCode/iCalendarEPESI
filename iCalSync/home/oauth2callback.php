<?php

require_once __DIR__.'/vendor/autoload.php';
//require_once __DIR__.'/include/autoloader.php';
//require_once('include/database.php');
//require_once('include/session.php');


$client = new Google_Client();
$client->setAuthConfigFile($_SERVER['DOCUMENT_ROOT']."/modules/iCalSync/client_secret.json");
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');
$client->addScope(Google_Service_Calendar::CALENDAR);
//sprawdza czy sesja istnieje
if (!isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  
  $client->authenticate($_GET['code']);
  $token = $client->getAccessToken();
  $token = serialize($token);
  $temp_token = fopen($_SERVER['DOCUMENT_ROOT']."/modules/iCalSync/temp_tokens/temp_token.txt","w+");
  fwrite($temp_token, $token);
  fclose($temp_token);
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
