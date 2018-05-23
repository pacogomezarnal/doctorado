<?php
require_once __DIR__.'/../vendor/autoload.php';
use Google\Client;
use Google\Service\YouTube\YouTube;
use Vimeo\Vimeo;

/*AUTENTICACION YOUTUBE*/
$DEVELOPER_KEY = '';
$client = new Google_Client();
$client->setDeveloperKey($DEVELOPER_KEY);

// Define an object that will be used to make all API requests.
$youtube = new Google_Service_YouTube($client);
$youtubeVideos = new Google_Service_YouTube($client);

/*AUTENTICACION VIMEO*/
$config=[
  "client_id"=>"",
  "client_secret"=>"+lUx41ibUzfKdgFIEROPhkp1WKc/9V6CguGCjjcarqbZjJQDkC8gu0qr0nx4",
  "access_token"=>""
];
$lib = new Vimeo($config['client_id'], $config['client_secret'], $config['access_token']);


try {
  // Call the search.list method to retrieve results matching the specified
  // query term.
  $busquedas=["programacion","php","laravel"];
  $file = fopen("search.tsv", "w");
  $fileV = fopen("searchVimeo.tsv", "w");
  $fileT = fopen("searchTag.tsv", "w");
  foreach ($busquedas as $busqueda) {
    //Busqueda con YOUTUBE


    //-->YOUYUBE
    $searchResponse = $youtube->search->listSearch('id,snippet', array(
      'q' => $busqueda,
      'maxResults' => '40',
      'type' => 'video'
      //'relevanceLanguage' => 'ES'
    ));
    $videos = '';
    // Add each result to the appropriate list, and then display the lists of
    // matching videos, channels, and playlists.
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
        $videos = sprintf('%s%s%s%s%s%s%s%s%s%s%s%s%s%s%s',
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
            $searchVideoResponse['items'][0]['contentDetails']['definition'], //hd definicion
            "\t",$busqueda);
            fwrite($file, $videos . PHP_EOL);
            fwrite($fileT, $searchVideoResponse['items'][0]['id']);
            fwrite($fileT, "\t");
            $tags="";
            foreach ($searchVideoResponse['items'][0]['snippet']['tags'] as $searchVideoTag) {
              $tags .= sprintf('%s%s',$searchVideoTag,",");
            }
            fwrite($fileT, $tags . PHP_EOL);
      }
    }
    //-->VIMEO
    // Show first page of results, set the number of items to show on each page to 10, sort by relevance, show results in
    // descending order, and filter only Creative Commons license videos.
    $search_results_vimeo = $lib->request('/videos', array(
        'page' => 1,
        'per_page' => 40,
        'query' => $busqueda,
        'sort' => 'relevant',
        'direction' => 'desc',
        'filter' => 'CC'
    ));
    foreach ($search_results_vimeo["body"]["data"] as $searchResultVimeo) {
      $videos = sprintf('%s%s%s%s%s%s%s%s%s%s%s%s%s%s%s',
          $searchResultVimeo['uri'], //titulo
          "\t",
          $searchResultVimeo['name'], //titulo
          "\t",
          $searchResultVimeo['user']['name'], //titulo del canal
          "\t",
          $searchResultVimeo['duration'], //duracion segundos
          "\t",
          $searchResultVimeo['language'], //idioma
          "\t",
          $searchResultVimeo['license'], //copyright
          "\t",
          $searchResultVimeo['width'], //hd definicion
          "\t",$busqueda);
          fwrite($fileV, $videos . PHP_EOL);
          fwrite($fileT, $searchResultVimeo['uri']);
          fwrite($fileT, "\t");
          $tags="";
          foreach ($searchResultVimeo['tags'] as $searchVideoTag) {
            $tags .= sprintf('%s%s',$searchVideoTag["tag"],",");
          }
          fwrite($fileT, $tags . PHP_EOL);
    }

  }
  fclose($file);
  fclose($fileT);
  fclose($fileV);
  } catch (Google_Service_Exception $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }
?>
