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
function serverConf(){
	$.get("script/action.php",{
		server: "!serverConf!"
	},function(data){
		$('#conf').empty();
		conf = "<br>";
		var res = JSON.parse(data);
		$.each(res, function(key, value){
			conf += value + "<br>";
		});
		
		$('#conf').append(conf);
	});
}
function serverConfModal(){
	$.get("script/action.php",{
		server: "!serverConf!"
	},function(data){
		conf = "<br>";
		var res = JSON.parse(data);
		$.each(res, function(key, value){
			conf += value + "<br>";
		});
		$("#serverEdit").modal();
		$("#confEdit").empty();
		$("#confEdit").append(conf);
	});
}
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
function addNewCrt(){
	free();
	$("#newCertificate").modal();
}
function serverOnOff(arg){
	$.get("script/action.php",{
		serverAction: "!serverAction!",
		status: arg,
	},function(data){
		conf = "<br>";
		var res = JSON.parse(data);
		$.each(res, function(key, value){
			conf += value + "<br>";
		});
		$('#serverActionStatusRead').empty();
		$('#serverActionStatusRead').append(conf);
		$('#serverActionStatus').modal();
		
		serverStatus();
	});
}
function server(){	
	serverStatus();
	serverConf();
	
	allCert();
	onlineCert();
}
function allCert(){
	$('#AllCertTable').DataTable().destroy();
	var table = $('#AllCertTable').DataTable({
		"ordering": true,
		"pageLength" : 10,
		"ajax":{
			url:"script/action.php?allCertificates=!allCerts!",
		},
		"columnDefs": [{
			"width": "10%",
            "targets": 8,
			"orderable": false,
        },{
			"width": "10%",
            "targets": 9,
			"orderable": false,
        }],
	});
	$('#AllCertTable').DataTable().ajax.reload();
}
function onlineCert(){
	$('#ActiveConnections').DataTable().destroy();
	var table = $('#ActiveConnections').DataTable({
		"ordering": true,
		"pageLength" : 10,
		"ajax":{
			url:"script/action.php?active=!active!",
		},
		"columnDefs": [{
			"width": "10%",
            "targets": 3,
            "data": null,
			"orderable": false,
            "defaultContent": "<button type='button' class='btn btn-success loading'><span class='glyphicon glyphicon-ban-circle'></span>&nbsp;Kill connection</button>"
        }]
	});
	$('#ActiveConnections').DataTable().ajax.reload();
	
	$('#ActiveConnections tbody').on('click', 'button', function(){
		var data = table.row($(this).parents('tr') ).data();
		$('.loading').button('loading');  
		$.get("script/action.php",{
			dropConn: "!dropConnection!",
			dropCert: data[0]
		},function(data){
			msg = "";
			var res = JSON.parse(data);
			$.each(res, function(key, value){
				msg += value + "<br>";
			});
			$('#serverActionStatusRead').empty();
			$('#serverActionStatusRead').append(msg);
			$('#serverActionStatus').modal();
			
			
			$('.loading').button('reset');
		});
    });
}
function sendCmd(){
	cmd = $('#cmdServer').val();
	if(cmd != ""){
	$.get("script/action.php",{
		serveManage: "!serverManagement!",
		cmd: cmd,
	},function(data){
		msg = "Command: " + cmd + "<br>---------------------------<br>";
		var res = JSON.parse(data);
		$.each(res, function(key, value){
				msg += value + "<br>";
		});
		$('#cmdServer').val("");
		$('#serverManagementRead').empty();
		$('#serverManagementRead').append(msg);
	});
	
	}else{
		console.log("data");
	}
}
function SessionCheck(){
	$.get("script/session.php", {
		checkSession: "checks"
	},function(data){
		if(data == 0){
			window.location.href = 'login.html';
		}
	});
}
function checkCrt(arg){
	if(arg.length > 2){
		$.get("script/action.php", {
			checkCert: "!checkCert!",
			cert: arg
		},function(data){
			if(data != ""){
				$("#checkStatusCert").removeClass("glyphicon-ok");
				$("#checkStatusCert").addClass("glyphicon-remove");
				
				$("#addNewCertificate").attr('disabled', true);
			}else{
				$("#checkStatusCert").removeClass("glyphicon-remove");
				$("#checkStatusCert").addClass("glyphicon-ok");
				
				$("#addNewCertificate").attr('disabled', false);
			}
		});
	}
}
function deleteCrt(arg){
	bootbox.confirm({
		message: "<center><h3>Are you sure?</h3></center>",
		buttons: {
			confirm: {
				label: 'Confirm',
				className: 'btn-success'
			},
			cancel: {
				label: 'Cancel',
				className: 'btn-danger'
			}
		},
		callback: function(result){
			if(result){
				$.get("script/action.php", {
					delete: "!deleteCrt!",
					cert: arg
				});
				allCert();
			}
		}
	});	
}
function free(){
	$.get("script/action.php", {
		freeIp: "!freeIp!"
	},function(data){
		if(data > 0){
			$("#freeIps").empty();
			$("#freeIps").append(data + " free ip");
			document.getElementById("staticIp").disabled = false;
		}else{
			$("#freeIps").empty();
			$("#freeIps").append("No free ip");
			document.getElementById("staticIp").disabled = true;
		}
	});
}