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

function update_sheet_cell($service, $google_sheet_id, $range, $val) {
    $values = [
        [
            // Cell values ...
            "test",
        ],
        // Additional rows ...
    ];
    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);
    $params = [
//        'valueInputOption' => $valueInputOption
    ];
    $result = $service->spreadsheets_values->update($google_sheet_id, $range, $body, $params);
    print_r($result);
}

function curl_check($url) {
    $ch = curl_init($url);
    $info=curl_exec($ch);
    $info=curl_getinfo($ch);
    return ($info);
}

$client = getGClient("google_credentials.json");
$service = new Google_Service_Sheets($client);

// Sheet Headerinfo
$sheet='Sheet1';
$range="${sheet}!A1:Z1";
$response = $service->spreadsheets_values->get($config['google_sheet_id'], $range);
$header_row = $response->getValues();

print_r($header_row);

// Sheet info
$data_start_row=2;
$range="${sheet}!A${data_start_row}:AAA";
$response = $service->spreadsheets_values->get($config['google_sheet_id'], $range);
$rows = $response->getValues();

foreach($rows as $row=>$data) {
    $row+=$data_start_row;

    echo $data[0]."\n";

    // Checked
    $col="B";
    $range="${sheet}!${col}${row}:${col}${row}";

    echo "Updating `${range}`:\n";
    update_sheet_cell($service, $config['google_sheet_id'],$range,"Yes!");

    break;
}

//print_r(curl_check_200('https://spp0309.classroom.puppet.com'));

//print_r($config);



echo "\n";

?>