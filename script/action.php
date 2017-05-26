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
/* SERVER CONF LOAD */
if(isset($_GET['server']) && $_GET['server'] == "!serverConf!"){
	echo $vpn->readConf();
}
/* SERVER CONF WRITE */
if(isset($_POST['reloadConf']) && $_POST['reloadConf'] == "!writeConf!"){
	$vpn->writeConf(strip_tags($_POST['confEdit']));
	header("Location:../index.html");
}
/* SERVER ON|OFF|RESTART */
if(isset($_GET['serverAction']) && $_GET['serverAction'] == "!serverAction!"){
	echo $vpn->serverAction(strip_tags($_GET['status']));
}
/* SERVER MANAGEMENT */
if(isset($_GET['serveManage']) && $_GET['serveManage'] == "!serverManagement!"){
	echo $vpn->serverManagement(strip_tags($_GET['cmd']));
}


