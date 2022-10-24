<?php

define('ERROR_FILE_PATH', './pages/error.php');

require_once('./src/model/ShortURL.php');
use src\model\ShortURL;

if (isset($_GET['url'])) {
    $url = $_GET['url'];    

    if (!is_null(ShortURL::selectShortUrl($url))) {
        $shortURL = ShortURL::selectShortUrl($url);      

        $timeZone = new DateTimeZone('America/Sao_Paulo');
        $dateTimeObject = new DateTime($shortURL->getExpired_At(), $timeZone);
        $actualDateTime = new DateTime('now', $timeZone);        
        $dif = date_diff($dateTimeObject, $actualDateTime)->format('%d%');

        if (intval($dif) < 1) {
            header('location: '.$shortURL->getURL());
            exit;
        } else {
            header('location: '.ERROR_FILE_PATH.'?error=Link expirado!');
            exit;
        }
        
    } else {
        header('location: '.ERROR_FILE_PATH.'?error=Link não encontrado!');
        exit;
    }            
}


