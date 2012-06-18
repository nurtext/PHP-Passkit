<?php
class PKLog{
	var $logLocation = 'webservice.log';

	function __construct($logLocation){
		$this->logLocation = $logLocation;
	}
	
	function fatalError($msg, $httpCode=200){
		if($this->_logToFile){ @$this->_logToFile($msg, 'fatalError'); }
		if($httpCode != 200){
			switch($httpCode){
				case 400:
					header('HTTP/1.0 400 Bad Request', true, 400);
					break;
				case 401:
					header('HTTP/1.0 401 Unauthorized', true, 401);
					break;
				case 403:
					header('HTTP/1.0 403 Forbidden', true, 403);
					break;
				case 404:
					header('HTTP/1.0 404 Not Found', true, 404);
					break;
				case 426:
					header('HTTP/1.0 426 Upgrade Required', true, 426);
					break;
				case 500:
					header('HTTP/1.0 500 Internal Server Error', true, 500);
					break;
				case 501:
					header('HTTP/1.0 501 Not Implemented', true, 501);
					break;
			}
		}
		die('<br /><b>FatalError</b>: ' . $msg);
	}
	
	function info($msg){
		if($this->_logToFile){ @$this->_logToFile($msg, 'Info'); }
	}
	
	function customError($type, $msg, $fatal=false, $httpCode=200){
		if($this->_logToFile){@$this->_logToFile($msg, ($fatal ? 'fatalCustomError' : 'customError') . ' ' . $type); }
		if($fatal){
			if($httpCode != 200){
				switch($httpCode){
					case 400:
						header('HTTP/1.0 400 Bad Request', true, 400);
						break;
					case 401:
						header('HTTP/1.0 401 Unauthorized', true, 401);
						break;
					case 403:
						header('HTTP/1.0 403 Forbidden', true, 403);
						break;
					case 404:
						header('HTTP/1.0 404 Not Found', true, 404);
						break;
					case 426:
						header('HTTP/1.0 426 Upgrade Required', true, 426);
						break;
					case 500:
						header('HTTP/1.0 500 Internal Server Error', true, 500);
						break;
					case 501:
						header('HTTP/1.0 501 Not Implemented', true, 501);
						break;
				}
			}
			die('<br /><b>' . $type . '</b>: ' . $msg);
		}
	}
	
	private function _logToFile($msg,$type){
		$error = date('m/d/Y H:i:s') . ' ' . $type . ': ' . $msg . ' (client IP: ' . $_SERVER['REMOTE_ADDR'] . ')' . "\n";
		file_put_contents($this->logLocation, $error, FILE_APPEND);
	}
}
?>