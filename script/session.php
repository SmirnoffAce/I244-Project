<?php
if(!isset($_SESSION)){
	session_start();
}
if(isset($_GET['checkSession']) && $_GET['checkSession'] == "checks"){
	if((!isset($_SESSION['sessionId']) && !isset($_SESSION['username'])) && $_SESSION['loginStatus'] != "L0G!N"){
		$status = 0;
	}else{
		$status = 1;
	}
	echo $status;
}else{
	if((!isset($_SESSION['sessionId']) && !isset($_SESSION['username'])) && $_SESSION['loginStatus'] != "L0G!N"){
		header("Location: ../login.html");
		die();
	}
}
?>