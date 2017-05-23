function SessionCheck(){
	$.get("script/session.php", {
		checkSession: "checks"
	},function(data){
		if(data == 0){
			window.location.href = 'login.html';
		}
	});
}