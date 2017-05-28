<?php
class Vpn{
	private $config = array();
	private $now;
	public function __construct(){
		$this->now = date("Y-m-d H:i:s");
		
		$this->config['vpn']['confDir'] = "/etc/openvpn/";
		$this->config['vpn']['certDir'] = "/etc/openvpn/easy-rsa/2.0/keys/";
		$this->config['vpn']['staticDir'] = "/etc/openvpn/static/";
		$this->config['vpn']['logDir'] = "/var/log/openvpn/log/";
		$this->config['vpn']['statusFile'] = "/var/log/openvpn/status.log";
		
		$this->config['default']['def'] = "../default/";
		$this->config['default']['conf'] = "../default/default.ovpn";
		$this->config['default']['ca'] = "../default/ca.crt";
		$this->config['default']['static'] = "../default/static";
		$this->config['default']['download'] = "../default/download/";
				
		$this->config['vpn']['bash'] = "/var/www/boris.gnw.ee/htdocs/script/class/bash/";
	}	
	
	public function serverStatus(){
		$answer = "0";
		exec('ps aux | grep openvpn', $output);
		if(count($output) > 2)
			$answer = "1";
		
		return $answer;
	}
	public function serverAction($status){
		switch($status){
			case "restart":
				$cmd = "restart";
			break;
			case "start":
				$cmd = "start";
			break;
			case "stop":
				$cmd = "stop";
			break;
			default:
				$cmd = "no";
		}
		if($cmd != "no"){
			$command =  "sudo systemctl ". $cmd . " openvpn@server.service";
			exec($command);
			exec("sudo systemctl status openvpn@server.service", $output);
			
			return json_encode($output);
		}	
	}
	
	public function readConf(){
		$content = file($this->config['vpn']['confDir'] . "server.conf");
		return json_encode($content);
	}
	public function writeConf($conf){
		$serverFile = fopen($this->config['vpn']['confDir'] . "server.conf", 'w');
		fwrite($serverFile, $conf);
		fclose($serverFile);
	}
	public function serverManagement($cmd){
		exec("sudo sh " . $this->config['vpn']['bash'] . "management.sh \"" . $cmd . "\"", $output);
		return json_encode($output);
	}
	
	public function searchLogs($word, $when){
		$searchResult = array();
		if($when == "all"){
			if($word != null){
				$files = scandir($this->config['vpn']['logDir']);
				unset($files[0]); unset($files[1]);
				foreach($files as $file){
					$content = file($this->config['vpn']['logDir'] . $file);
					foreach($content as $string){
						$pattern = "/^.*$word.*\$/m";
						if(preg_match_all($pattern, $string, $result)){
							foreach($result[0] as $log){
								$searchResult[] = array(substr($log, 0, 24), substr($log, 25));
							}
						}
					}
				}
			}
		}elseif($when == "today"){
			if($word != null){
				$content = file($this->config['vpn']['logDir'] . "openvpn.log");
				foreach($content as $string){
					$pattern = "/^.*$word.*\$/m";
					if(preg_match_all($pattern, $string, $result)){
						foreach($result[0] as $log){
							$searchResult[] = array(substr($log, 0, 24), substr($log, 25));
						}
					}
				}	
			}else{
				$content = file($this->config['vpn']['logDir'] . "openvpn.log");
				foreach($content as $string){
					$searchResult[] = array(substr($string, 0, 24), substr($string, 25));					
				}
			}
		}
		
		if(count($searchResult) == 0){
			$searchResult[] = array("", "Nothing to search");
		}
		rsort($searchResult);

		$json = array(
			"draw"	=>	1,
			"recordsTotal"	=>	count($searchResult),
            "recordsFiltered"	=>	count($searchResult),
			"data"	=>	$searchResult
        );
		return json_encode($json);
	}
	
	public function readActiveConn(){
		$result = array();
		$content = file($this->config['vpn']['statusFile']);
		foreach($content as $string){
			$arg = explode(",", $string);
			
			if($arg[0] == "ROUTING_TABLE"){
				$ip = explode(":", $arg[3]);
				
				$result[] = array(
					$arg[2], $arg[1], $ip[0]
				);
			}
		}
		if(count($result) == 0){
			$result[] = array("", "No active connnections", "");
		}
		$json = array(
			"draw"	=>	1,
			"recordsTotal"	=>	count($result),
            "recordsFiltered"	=>	count($result),
			"data"	=>	$result
        );
		
		return json_encode($json); 
	}

	public function dropConnect($cert){
		exec("sudo sh " . $this->config['vpn']['bash'] . "dropConn.sh " . $cert, $output);
		return json_encode($output);
	}
	public function revokeCrt($crt){
		$query = new db();
		
		$check = $query->getOne("select static_ip.id from certs, static_ip where certs.id = static_ip.cert and certs.name = '$crt';");
		if($check){
			$id = $check['id'];
			$query->query("update static_ip set cert = NULL, status = '0' where id = '$id'");
			unlink($this->config['vpn']['staticDir'] . $crt);		
		}
		
		$query->query("delete from certs where name = '$crt';");
		
		//exec("sudo sh " . $this->config['vpn']['bash'] . "dropConn.sh " . $crt);
		exec("sudo sh " . $this->config['vpn']['bash'] . "dropCrt.sh " . $crt);
		exec("sudo sh " . $this->config['vpn']['bash'] . "deleteCrt.sh " . $crt);
		unlink($this->config['default']['download'] . $crt . ".zip");
	}
	
	public function allCerts(){
		$query = new db();
		$certs = $query->getAll("select name, description, ip, country, province, city, unit, email, created from certs;");
		$all = array();
		foreach($certs as $cert){
			$name = $cert['name'];
			if($cert['ip'] != "dynamic"){
				$ip = $cert['ip'];
			}else{
				$ip = "Dynamic ip";				
			}
			$link = "default/download/" . $name . ".zip";
			$delBtn = "<button type='button' class='btn btn-success' onclick=deleteCrt('" . $name . "');><span class='glyphicon glyphicon-trash'></span>&nbsp;Delete</button>";
			//$downloadBtn = "<button type='button' class='btn btn-success' onclick=downloadCrt('" . $name . "');><span class='glyphicon glyphicon-download'></span>&nbsp;Download</button>";
			$downloadBtn = "<a href=" . $link . " download=" . $name . ".zip" . "><button type='button' class='btn btn-success'><span class='glyphicon glyphicon-download'></span>&nbsp;Download</button></a>";
			
			$all[] = array($name, $cert['description'], $ip, $cert['created'], $cert['country'] . " (" . $cert['province'] . ")", $cert['city'], $cert['unit'], $cert['email'], $downloadBtn, $delBtn);
		}
		if(count($all) == 0){
			$all[] = array("", "No certificates", "", "", "", "", "", "", "", "");
		}
		$json = array(
			"draw"	=>	1,
			"recordsTotal"	=>	count($all),
            "recordsFiltered"	=>	count($all),
			"data"	=>	$all
        );
		return json_encode($json);
	}
	public function newCert($name, $desc, $ip, $email, $unit, $city, $province, $country){
		$query = new db();
		$query->query("insert into certs(name, description, created, ip, country, province, city, unit, email) values('$name', '$desc', '$this->now', '$ip', '$country', '$province', '$city', '$unit', '$email');");
		exec("sudo sh " . $this->config['vpn']['bash'] . "newCrt.sh " . $name);		
		
		if($ip == "static"){			
			$ip = $query->getOne("select id, ip, gw from static_ip where status = '0';");
			$cert = $query->getOne("select id from certs where name = '$name';");
			
			$file_contents = file_get_contents($this->config['default']['static']);
			$fh = fopen($this->config['vpn']['staticDir'] . $name, 'w');
			$file_contents = str_replace('ip', $ip['ip'], $file_contents);
			$file_contents = str_replace('gw', $ip['gw'], $file_contents);
			fwrite($fh, $file_contents);
			fclose($fh);
			
			$certId = $cert['id'];
			$ipId = $ip['id'];
			$ips = $ip['ip'];
			$query->query("update certs set ip = '$ips' where id = '$certId'");
			$query->query("update static_ip set cert = '$certId', status = '1' where id = '$ipId';");
		}
			
		$file_contents = file_get_contents($this->config['default']['conf']);
		$fh = fopen($this->config['default']['download'] . "client.ovpn", 'w');
		$file_contents = str_replace('XXX', $name , $file_contents);
		fwrite($fh, $file_contents);
		fclose($fh);
		
		exec("sudo chmod -R 0777 " . $this->config['vpn']['certDir']);
		$zip = $this->config['default']['download'] . $name . ".zip";
		$archive = new PclZip($zip);
		$archive->add($this->config['vpn']['certDir'] . $name . ".crt", PCLZIP_OPT_REMOVE_PATH, $this->config['vpn']['certDir'], PCLZIP_OPT_ADD_PATH, 'certs');
		$archive->add($this->config['vpn']['certDir'] . $name . ".key", PCLZIP_OPT_REMOVE_PATH, $this->config['vpn']['certDir'], PCLZIP_OPT_ADD_PATH, 'certs');
		$archive->add($this->config['default']['ca'], PCLZIP_OPT_REMOVE_PATH, $this->config['default']['def'], PCLZIP_OPT_ADD_PATH, 'certs');
		$archive->add($this->config['default']['download'] . "client.ovpn", PCLZIP_OPT_REMOVE_PATH, $this->config['default']['download'], PCLZIP_OPT_ADD_PATH, 'certs');
		exec("sudo chmod -R 0644 " . $this->config['vpn']['certDir']);
			
		unlink($this->config['default']['download'] . "client.ovpn");
	}
	public function checkCert($name){
		$arg = strip_tags($name);
		$query = new db();
		$cert = $query->getOne("select id from certs where name = '$arg';");
		return $cert['id'];
	}
	public function freeIp(){
		$query = new db();
		$free = $query->getAll("select count(id) as total from static_ip where status = '0';");
		return $free[0]['total'];
	}
}
?>