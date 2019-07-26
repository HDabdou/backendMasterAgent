<?php 
       header('Access-Control-Allow-Origin','*');
       header('Access-Control-Allow-Headers','content-type');

define('DIR',__DIR__);


require DIR . '/vendor/autoload.php';

session_start();

// Instantiate the app
require 'config/default.php';
$app = new \Slim\App($config);


// Set up dependencies
require DIR . '/app/dependencies.php';


// Register routes
require DIR . '/app/routes.php';


$app->run();
