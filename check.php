#!/usr/bin/php
<?php
set_time_limit(0);

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

function update_sheet_cell($google_sheet_id, $range, $val) {
    global $service;
    $valueInputOption = 'USER_ENTERED';
    $values = [
        [
            // Cell values ...
            $val
        ],
        // Additional rows ...
    ];
    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);
    $params = [
        'valueInputOption' => $valueInputOption
    ];
    $result = $service->spreadsheets_values->update($google_sheet_id, $range, $body, $params);
    return $result;
}

function curl_check($url) {
    echo "Checking URL: ".$url."\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
    $info=curl_exec($ch);
    $info=curl_getinfo($ch);
    return ($info);
}

$client = getGClient("google_credentials.json");
$client->addScope(Google_Service_Sheets::SPREADSHEETS);
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

//$test="spp0309";

foreach($rows as $row=>$data) {
//    if ($data[0]!="spp0309") continue;
    $row+=$data_start_row;

    if (isset($data[1]) && preg_match("/true/i",$data[1])) continue;

    $url='https://'.$data[0].".classroom.puppet.com";
    $curl_result=curl_check($url);

    if (is_array($curl_result)) {

        // Sleeps due to Google Sheet usage limits ..
        // This version of the Google Sheets API has a limit of 500 requests per 100 seconds per project, and 100 requests per 100 seconds per user. Limits for reads and writes are tracked separately. There is no daily usage limit.
        sleep(2);

        // Checked
        $col="B";
        $result=update_sheet_cell($config['google_sheet_id'],"${sheet}!${col}${row}:${col}${row}","True");
        sleep(1);

        $col="C";
        $result=gethostbyname($data[0].".classroom.puppet.com");
        $result=update_sheet_cell($config['google_sheet_id'],"${sheet}!${col}${row}:${col}${row}",$result);
        sleep(1);

        // Welcome Page http_code
        $col="D";
        $result=update_sheet_cell($config['google_sheet_id'],"${sheet}!${col}${row}:${col}${row}",$curl_result['http_code']);
        sleep(1);

        //PE Admin console
        $col="E";
        $url='https://'.$data[0]."-master.classroom.puppet.com";
        $curl_result=curl_check($url);
        $result=update_sheet_cell($config['google_sheet_id'],"${sheet}!${col}${row}:${col}${row}",$curl_result['http_code']);
        sleep(1);

        //CD4PE
        $col="F";
        $url='https://'.$data[0]."-cd4pe.classroom.puppet.com";
        $curl_result=curl_check($url);
        $result=update_sheet_cell($config['google_sheet_id'],"${sheet}!${col}${row}:${col}${row}",$curl_result['http_code']);
        sleep(1);
        
        //Remediate
        $col="G";
        $url='https://'.$data[0]."-remediate.classroom.puppet.com";
        $curl_result=curl_check($url);
        $result=update_sheet_cell($config['google_sheet_id'],"${sheet}!${col}${row}:${col}${row}",$curl_result['http_code']);
        sleep(1);
        
        //Gitlab
        $col="H";
        $url='https://'.$data[0]."-gitlab.classroom.puppet.com";
        $curl_result=curl_check($url);
        $result=update_sheet_cell($config['google_sheet_id'],"${sheet}!${col}${row}:${col}${row}",$curl_result['http_code']);
        sleep(1);
        
        //Comply
        $col="I";
        $url='https://'.$data[0]."comply0.classroom.puppet.com";
        $curl_result=curl_check($url);
        $result=update_sheet_cell($config['google_sheet_id'],"${sheet}!${col}${row}:${col}${row}",$curl_result['http_code']);
        sleep(1);
        
    }


//    break;
}

//print_r(curl_check_200('https://spp0309.classroom.puppet.com'));

//print_r($config);



echo "\n";

?>