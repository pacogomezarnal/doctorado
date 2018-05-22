<?php
require_once __DIR__.'/../vendor/autoload.php';
use Google\Client;
use Google\Service\YouTube\YouTube;

/*
 * Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
 * Google Developers Console <https://console.developers.google.com/>
 * Please ensure that you have enabled the YouTube Data API for your project.
 */
$DEVELOPER_KEY = 'AIzaSyCQMvumLcYaytf7BV8X25DWhsnMhDGyLYo';

$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);

// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);
try {
  // Call the search.list method to retrieve results matching the specified
  // query term.
  $searchResponse = $youtube->search->listSearch('id,snippet', array(
    'q' => 'robots',
    'maxResults' => '20',
  ));

  $videos = '';
  $channels = '';
  $playlists = '';
  // Add each result to the appropriate list, and then display the lists of
  // matching videos, channels, and playlists.
  foreach ($searchResponse['items'] as $searchResult) {
    switch ($searchResult['id']['kind']) {
      case 'youtube#video':
        $videos .= sprintf('<li>%s (%s)</li>',
            $searchResult['snippet']['title'], $searchResult['id']['videoId']);
        break;
      case 'youtube#channel':
        $channels .= sprintf('<li>%s (%s)</li>',
            $searchResult['snippet']['title'], $searchResult['id']['channelId']);
        break;
      case 'youtube#playlist':
        $playlists .= sprintf('<li>%s (%s)</li>',
            $searchResult['snippet']['title'], $searchResult['id']['playlistId']);
        break;
    }
  }

  $htmlBody .= <<<END
  <h3>Videos</h3>
  <ul>$videos</ul>
  <h3>Channels</h3>
  <ul>$channels</ul>
  <h3>Playlists</h3>
  <ul>$playlists</ul>
END;
  } catch (Google_Service_Exception $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Entrada Evento</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  </head>
  <body style="padding:10px">
    <?=$htmlBody?>
  </body>
</html>
