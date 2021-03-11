#!/usr/bin/php
<?php

$config = json_decode(file_get_contents('config.json'),true);
require_once 'vendor/autoload.php';

function getGClient($credfile='credentials.json')
{
    $client = new Google_Client();
    putenv('GOOGLE_APPLICATION_CREDENTIALS=./'.$credfile);
    $client->useApplicationDefaultCredentials();
    $client->setApplicationName('jarvis.se.puppet.net');
    $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
    return $client;
}

function curl_check_200($url) {
    $ch = curl_init($url);
    $info=curl_exec($ch);
    //$info=curl_getinfo($ch);
    return ($info);
}

$client = getGClient("google_credentials.json");
$service = new Google_Service_Sheets($client);

// Sheet Headerinfo
$range='Sheet1!A1:Z1';
$response = $service->spreadsheets_values->get($config['google_sheet_id'], $range);
$header_row = $response->getValues();

print_r($header_row);

// Sheet info
$range='Sheet1!A2:AAA';
$response = $service->spreadsheets_values->get($config['google_sheet_id'], $range);
$rows = $response->getValues();

foreach($rows as $row) {
//    echo $row[0]."\n";

//spp0309

}

print_r(curl_check_200('https://spp0309.classroom.puppet.com'));

//print_r($config);



echo "\n";

?>