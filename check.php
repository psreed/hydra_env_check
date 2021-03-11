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

$client = getGClient("google_credentials.json");
$service = new Google_Service_Sheets($client);

// Sheet info
$range='Sheet1!A2:AAA';
$response = $service->spreadsheets_values->get($config['google_sheet_id'], $range);
$rows = $response->getValues();

foreach($rows as $row) {
    echo $row[0]."\n";
}


//print_r($config);



echo "\n";

?>