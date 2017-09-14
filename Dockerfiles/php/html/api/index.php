<?php
require 'Slim/Slim.php';
require_once '../dash.php';

\Slim\Slim::registerAutoloader();
$slimApp = new \Slim\Slim();
//$app->config('debug', false);
 
function echoResponse($status_code, $response) {
    $slimApp = \Slim\Slim::getInstance();
    $slimApp->status($status_code);
    echo $response;
}

$slimApp->get('/put/:app/:env/:envId', function ($app, $env, $envId) {
	$ver= '';
	$date= '';
	$log= '';
    $slimApp = \Slim\Slim::getInstance();
	$params= $slimApp->request()->params();
	foreach ($params as $p_name=>$p_value) {
		switch ($p_name) {
			case 'v': $ver= $p_value; break;
			case 'd': $date= $p_value; break;
			case 'l': $log= $p_value; break;
		}
	}
	if (($ver=='') || ($date=='') || ($log=='')) echoResponse(400, "Bad Request");
	else {
		$dash= new Dash();
		$dash->updateVersion($app, $env, $envId, $ver, $date, $log);
		echoResponse(200, "Update ".$app." on ".$envId." => Version: ".$ver." Date: ".$date." Log: ".$log);
	}
});

$slimApp->get('/del/:app', function ($app) {
	echoResponse(200, "Delete: ".$app);
});

$slimApp->run();
?>