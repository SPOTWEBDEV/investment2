<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function checkUrlProtocol($url)
{
    $parsedUrl = parse_url($url);
    if (isset($parsedUrl['scheme'])) {
        return $parsedUrl['scheme'];
    } else {
        return 'invalid';
    }
}

$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


$request = checkUrlProtocol($currentUrl);

define("HOST", "localhost");

echo $request;

if ($request === 'https') {
    $domain = "https://spotwebtech.com.ng";
    define("USER", "spotweb1_nucor");
    define("PASSWORD", "spotweb1_nucor");
    define("DATABASE", "spotweb1_nucor");


    $connection = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }
}

if($request === 'http') {
   
    $domain = "http://localhost/investment2";
    define("USER", "root");
    define("PASSWORD", "");
    define("DATABASE", "nucor");


    $connection = mysqli_connect(HOST, USER, PASSWORD, DATABASE);

    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }
}

session_start();


$siteemail = 'support@nucor.com';
 $sitename = "Nucor";