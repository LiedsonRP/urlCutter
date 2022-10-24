<?php

define('MAIN_PAGE_PATH', '../index.html');
define('URL_PAGE_PATH', '../pages/shortUrl.php');
define('HOST_ADDRESS', 'http://localhost/urlcutter/urlcutter.php');

require("./model/ShortURL.php");
use \src\model\ShortURL;

if (isset($_POST['url'])) {

    $url = $_POST['url'];    

    $headers = @get_headers($url);
    if ($headers && strpos($headers[0], '200')) {
        $shortedURL = new ShortURL($url);        
        shortURL::insert($shortedURL);
        header('location: '.URL_PAGE_PATH.'?link='.HOST_ADDRESS.'?url='.$shortedURL->getShortURL());
        exit;
    } else {
        header('location: '.MAIN_PAGE_PATH.'?error=O link passado não existe!');
        exit;
    }    
    
} else {
    header('location: '.MAIN_PAGE_PATH.'?error=O campo está vazio!');
}



