<?php

use Aws\S3\S3Client;

require 'vendor/autoload.php';

$config = require('config.php');

// get data from config and set up s3client
$s3 = S3Client::factory([
    'key' => $config['s3']['key'],
    'secret' => $config['s3']['secret'],
    'region' => 'ca-central-1',
    'signature' => 'v4',
]);


?>