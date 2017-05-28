<?php
if(!isset($_SESSION)){
	session_start();
}
include('session.php');
include('class/zip.php');
include('class/Vpn.class.php');
include('class/Database.class.php');
$vpn = new Vpn;
/* SEARCH IN LOG FILES */
if(isset($_GET['logs']) && $_GET['logs'] == "!searchLogs!"){
	echo $vpn->searchLogs(strip_tags($_GET['word']), strip_tags($_GET['args']));
}
/* SERVER CONF LOAD */
if(isset($_GET['server']) && $_GET['server'] == "!serverConf!"){
	echo $vpn->readConf();
}
/* SERVER STATUS CHECK */
if(isset($_GET['server']) && $_GET['server'] == "!serverStatus!"){
	echo $vpn->serverStatus();
}
/* ACTIVE CONNECTIONS */
if(isset($_GET['active']) && $_GET['active'] == "!active!"){
	echo $vpn->readActiveConn();
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
/* ACTIVE CONNECTION DROP */
if(isset($_GET['dropConn']) && $_GET['dropConn'] == "!dropConnection!"){
	echo $vpn->dropConnect(strip_tags($_GET['dropCert']));
}
/* SERVER MANAGEMENT */
if(isset($_GET['serveManage']) && $_GET['serveManage'] == "!serverManagement!"){
	echo $vpn->serverManagement(strip_tags($_GET['cmd']));
}
/* CERTIFICATE CHECK */
if(isset($_GET['checkCert']) && $_GET['checkCert'] == "!checkCert!"){
	echo $vpn->checkCert(strip_tags($_GET['cert']));
}
/* NEW CERTIFICATE */
if(isset($_POST['addNewCertificate']) && $_POST['addNewCertificate'] == "!addNewCert!"){
	$vpn->newCert(strip_tags($_POST['name']), strip_tags($_POST['desc']), strip_tags($_POST['ip']), strip_tags($_POST['email']), strip_tags($_POST['unit']), strip_tags($_POST['city']), strip_tags($_POST['province']), strip_tags($_POST['country']));
	header("Location:../index.html");
}
/* ALL CERTIFICCATES */
if(isset($_GET['allCertificates']) && $_GET['allCertificates'] == "!allCerts!"){
	echo $vpn->allCerts();
}
/* EXIT */
if(isset($_GET['exit']) && $_GET['exit'] == '1'){
	session_destroy();
	unset($_SESSION);
	header("Location:../login.html");
	die();
}
/* DELETE CERT */
if(isset($_GET['delete']) && $_GET['delete'] == "!deleteCrt!"){
	$vpn->revokeCrt(strip_tags($_GET['cert']));
}
/* FREE STATICK IP */
if(isset($_GET['freeIp']) && $_GET['freeIp'] == "!freeIp!"){
	echo $vpn->freeIp();
}
><?php
if(!isset($_SESSION)){
	session_start();
}
include('session.php');
include('class/zip.php');
include('class/Vpn.class.php');
include('class/Database.class.php');
$vpn = new Vpn;
/* SEARCH IN LOG FILES */
if(isset($_GET['logs']) && $_GET['logs'] == "!searchLogs!"){
	echo $vpn->searchLogs(strip_tags($_GET['word']), strip_tags($_GET['args']));
}
/* SERVER CONF LOAD */
if(isset($_GET['server']) && $_GET['server'] == "!serverConf!"){
	echo $vpn->readConf();
}
/* SERVER STATUS CHECK */
if(isset($_GET['server']) && $_GET['server'] == "!serverStatus!"){
	echo $vpn->serverStatus();
}
/* ACTIVE CONNECTIONS */
if(isset($_GET['active']) && $_GET['active'] == "!active!"){
	echo $vpn->readActiveConn();
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
/* ACTIVE CONNECTION DROP */
if(isset($_GET['dropConn']) && $_GET['dropConn'] == "!dropConnection!"){
	echo $vpn->dropConnect(strip_tags($_GET['dropCert']));
}
/* SERVER MANAGEMENT */
if(isset($_GET['serveManage']) && $_GET['serveManage'] == "!serverManagement!"){
	echo $vpn->serverManagement(strip_tags($_GET['cmd']));
}
/* CERTIFICATE CHECK */
if(isset($_GET['checkCert']) && $_GET['checkCert'] == "!checkCert!"){
	echo $vpn->checkCert(strip_tags($_GET['cert']));
}
/* NEW CERTIFICATE */
if(isset($_POST['addNewCertificate']) && $_POST['addNewCertificate'] == "!addNewCert!"){
	$vpn->newCert(strip_tags($_POST['name']), strip_tags($_POST['desc']), strip_tags($_POST['ip']), strip_tags($_POST['email']), strip_tags($_POST['unit']), strip_tags($_POST['city']), strip_tags($_POST['province']), strip_tags($_POST['country']));
	header("Location:../index.html");
}
/* ALL CERTIFICCATES */
if(isset($_GET['allCertificates']) && $_GET['allCertificates'] == "!allCerts!"){
	echo $vpn->allCerts();
}
/* EXIT */
if(isset($_GET['exit']) && $_GET['exit'] == '1'){
	session_destroy();
	unset($_SESSION);
	header("Location:../login.html");
	die();
}
/* DELETE CERT */
if(isset($_GET['delete']) && $_GET['delete'] == "!deleteCrt!"){
	$vpn->revokeCrt(strip_tags($_GET['cert']));
}
/* FREE STATICK IP */
if(isset($_GET['freeIp']) && $_GET['freeIp'] == "!freeIp!"){
	echo $vpn->freeIp();
}
?>