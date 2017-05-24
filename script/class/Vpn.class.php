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
		
		$this->config['default']['conf'] = "../default/default.ovpn";
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

	
}
