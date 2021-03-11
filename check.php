#!/usr/bin/php
<?php

$config = json_decode(file_get_contents('config.json'),true);
require_once 'vendor/autoload.php';

    


print_r($config);



echo "\n";

?>