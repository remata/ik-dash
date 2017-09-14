<?php
	if(!isset($_SESSION)) session_start();
	$currentApp= $_POST['app'];
	$_SESSION['app']= $currentApp;
	setcookie('app', $currentApp, time()+15552000);
?>