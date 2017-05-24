//on load
function server(){	
	serverStatus();
}
//session check
function SessionCheck(){
	$.get("script/session.php", {
		checkSession: "checks"
	},function(data){
		if(data == 0){
			window.location.href = 'login.html';
		}
	});
}
//search function sending data from logs tab search form
function searchLog(){
	searchWord = $('#searchValue').val();
	searchArg = $('#day').val();

	$('#logPrint').DataTable().destroy();
	$('#logPrint').DataTable({
		"columns": [{ "width": "20%" }, null ],
		"ordering": true,
		"pageLength" : 10,
		"ajax":{
			url:"script/action.php?logs=!searchLogs!&word=" + searchWord + "&args=" + searchArg,
		}
	});
	$('#logPrint').DataTable().ajax.reload();
}
//check status of vpn server
function serverStatus(){
	$.get("script/action.php",{
		server: "!serverStatus!"
	},function(data){
		if(data == "0"){
			$('#serverStatus').removeClass('alert-success');
			$('#serverStatus').addClass('alert-danger');
			$('#serverStatus').empty();
			$('#serverStatus').append("<center><strong>OpenVpn server is not running </strong><br>For more details check logs.</center>");
		}else{			
			$('#serverStatus').removeClass('alert-danger');
			$('#serverStatus').addClass('alert-success');
			$('#serverStatus').empty();
			$('#serverStatus').append("<center><strong>OpenVpn server is running.</center>");
		}
	});
}