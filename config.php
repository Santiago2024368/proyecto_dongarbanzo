<?php
require 'vendor/autoload.php';

use Google\Client as GoogleClient;

session_start();

$googleClient = new GoogleClient();
$googleClient->setClientId('26696218763-j190fjvms85cfb26s9m3um3hioern0qv.apps.googleusercontent.com'); // Reemplaza con tu CLIENT ID
$googleClient->setClientSecret('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'); // Reemplaza con tu CLIENT SECRET
$googleClient->setRedirectUri('http://localhost:3000/login.php');
$googleClient->addScope('email');
$googleClient->addScope('profile');