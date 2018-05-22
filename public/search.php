<?php
require_once __DIR__.'/../vendor/autoload.php';
use Google\Client;
use Google\Service\YouTube\YouTube;

/*
 * Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
 * Google Developers Console <https://console.developers.google.com/>
 * Please ensure that you have enabled the YouTube Data API for your project.
 */
$DEVELOPER_KEY = '';

$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);

// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);
$youtubeVideos = new Google_Service_YouTube($client);
try {
  // Call the search.list method to retrieve results matching the specified
  // query term.
  $searchResponse = $youtube->search->listSearch('id,snippet', array(
    'q' => 'robots',
    'maxResults' => '20',
    'type' => 'video',
    'relevanceLanguage' => 'ES'
  ));

  $videos = '';
  // Add each result to the appropriate list, and then display the lists of
  // matching videos, channels, and playlists.
  $file = fopen("search.tsv", "w");
  $fileT = fopen("searchTag.tsv", "w");
  foreach ($searchResponse['items'] as $searchResult) {
    //Realizamos una busqueda para cada uno de los videos
    $searchVideoResponse = $youtubeVideos->videos->listVideos('id,snippet,contentDetails', array(
      'id' => $searchResult['id']['videoId'],
    ));
    //Convertir los minutos en segundos
    if($searchVideoResponse){
      //Conversion a SEGUNDOS
      $duracionTemp=$searchVideoResponse['items'][0]['contentDetails']['duration'];
      $duracionTemp=substr($duracionTemp,2);
      $posMinutos = strpos($duracionTemp, "M");
      if($posMinutos){
        $duracionTempMinutos=substr($duracionTemp,0,$posMinutos);
        $duracionTemp=substr($duracionTemp,$posMinutos+1);
      }
      $posSegundos = strpos($duracionTemp, "S");
      if($posSegundos){
        $duracionTempSeg=substr($duracionTemp,0,$posSegundos);
      }
      $duracionSegundos = ($duracionTempMinutos*60)+$duracionTempSeg;
      $videos = sprintf('%s%s%s%s%s%s%s%s%s%s%s%s%s',
          $searchVideoResponse['items'][0]['id'], //titulo
          "\t",
          $searchResult['snippet']['title'], //titulo
          "\t",
          $searchVideoResponse['items'][0]['snippet']['channelTitle'], //titulo del canal
          "\t",
          $duracionSegundos, //duracion segundos
          "\t",
          $searchVideoResponse['items'][0]['snippet']['defaultAudioLanguage'], //idioma
          "\t",
          $searchVideoResponse['items'][0]['contentDetails']['licensedContent']?"true":"false", //copyright
          "\t",
          $searchVideoResponse['items'][0]['contentDetails']['definition']); //hd definicion
          fwrite($file, $videos . PHP_EOL);
          fwrite($fileT, $searchVideoResponse['items'][0]['id']);
          $tags="";
          foreach ($searchVideoResponse['items'][0]['snippet']['tags'] as $searchVideoTag) {
            $tags .= sprintf('%s%s',$searchVideoTag,"\t");
          }
          fwrite($fileT, $tags . PHP_EOL);
    }
  }
  fclose($file);
  fclose($fileT);
  } catch (Google_Service_Exception $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }
?>
