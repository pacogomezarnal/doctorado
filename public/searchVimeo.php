<?php
require_once __DIR__.'/../vendor/autoload.php';
use Vimeo\Vimeo;
//Configuramos un nuevo objeto Vimeo con la autenticacion
$config=[
  "client_id"=>"bd2eca5ea736499a8815e1fca675a6753b91dda0",
  "client_secret"=>"MJbZdGnZeZeOfBpgexboF8S00sy0BlVvM2FD1IT4FebVMvet2FLc8Voxa114qnQlNj1n+lUx41ibUzfKdgFIEROPhkp1WKc/9V6CguGCjjcarqbZjJQDkC8gu0qr0nx4",
  "access_token"=>"c641b54669d1ac5ab23bbc13be754b0a"
];
$lib = new Vimeo($config['client_id'], $config['client_secret'], $config['access_token']);
// Show first page of results, set the number of items to show on each page to 10, sort by relevance, show results in
// descending order, and filter only Creative Commons license videos.
$search_results = $lib->request('/videos', array(
    'page' => 1,
    'per_page' => 10,
    'query' => 'vimeo staff',
    'sort' => 'relevant',
    'direction' => 'desc',
    'filter' => 'CC'
));
var_dump($search_results);
