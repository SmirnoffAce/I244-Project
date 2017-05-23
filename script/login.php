<?php
include('class/Database.class.php');

if(isset($_POST['login']) && $_POST['login'] == "Login"){
	$usr = strip_tags($_POST['user']);
	$pwd = hash("sha256", strip_tags($_POST['pass']));
	$query = new db();
	$login = $query->getOne("select id, role, last_login, last_ip from users where username = '$usr' and pwd = '$pwd';");
	if(isset($login['id'])){
		session_start();
		$_SESSION['sessionId'] = time();
		$_SESSION['username'] = $usr;
		$_SESSION['role'] = $login['role'];
		$_SESSION['lastLogin'] = $login['last_login'];
		$_SESSION['lastIp'] = $login['last_ip'];
		$_SESSION['loginStatus'] = "L0G!N";
		
		$now = date("Y-m-d H:i:s");
		$ip = $_SERVER['REMOTE_ADDR'];
		$id = $login['id'];
		$query->query("update users set last_login = '$now', last_ip = '$ip' where id = '$id';");
		header("Location: ../index.html");
	}else{
		header("Location: ../login.html");
	}
}else{
	header("Location: ../login.html");
}
?>