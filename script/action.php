<?php
if(!isset($_SESSION)){
	session_start();
}
include('session.php');
include('class/zip.php');
include('class/Vpn.class.php');
include('class/Database.class.php');
$vpn = new Vpn;

/* SERVER STATUS CHECK */
if(isset($_GET['server']) && $_GET['server'] == "!serverStatus!"){
	echo $vpn->serverStatus();
}
/* SEARCH IN LOG FILES */
if(isset($_GET['logs']) && $_GET['logs'] == "!searchLogs!"){
	echo $vpn->searchLogs($_GET['word'], $_GET['args']);
}