<?php
class PKTemplate {
	var $templatePath;
	var $templateData = array();
	var $templateContent;
	var $templateRawContent;
	var $templateFields = array();
	var $templatePassType = null;
	
	var $PKTypes = array('boardingPass', 'coupon', 'eventTicket', 'generic', 'storeCard');
	var $PKFields = array('headerFields', 'primaryFields', 'secondaryFields', 'auxiliaryFields', 'backFields');
	
	function __construct($templatePath){
		$this->templatePath = $templatePath;
		$templateFile = $templatePath . '/pass.json';
		if(file_exists($templateFile)){
			$this->templateFile = $templateFile;
			$this->templateRawContent = file_get_contents($templateFile);
			if(!$this->templateContent = json_decode($this->templateRawContent)){
				PKLog::fatalError('Template pass.json file is no valid JSON.',400);
			}
		}else{
			PKLog::fatalError('Template does not contain pass.json file.',404);
		}
	}
	
	function getKeys($selector=null){
		$passType = $this->getPassType();
		$keys = array();
		if($selector){
			if(!in_array($selector,$this->PKFields)){
				PKLog::fatalError('Invalid field selector specified, should be one of the following: "' . implode('", "',$this->PKFields) . '".',404);
			}
			foreach((array)$this->templateContent->{$passType}->{$selector} as $field){
				if($field->key){
					$keys[] = $field->key;
				}
			}
		}else{
			foreach($this->PKFields as $selector){
				if(isset($this->templateContent->{$passType}->{$selector})){
					foreach((array)$this->templateContent->{$passType}->{$selector} as $field){
						if($field->key){
							$keys[] = $field->key;
						}
					}
				}
			}
		}
		return $keys;
	}
	
	function setKeys($data){
		$passType = $this->getPassType();
		$data = (array)$data;
		foreach($this->PKFields as $selector){
			if(isset($this->templateContent->{$passType}->{$selector})){
				foreach((array)$this->templateContent->{$passType}->{$selector} as $id => $field){
					if($field->key){
						if($data[$field->key]){
							$tempvar = $this->templateContent->{$passType}->{$selector}[$id];
							$tempvar->value = $data[$field->key];
							$this->templateContent->{$passType}->{$selector}[$id] = $tempvar;
						}
					}
				}
			}
		}
	}
	
	function setTopLevelKey($key, $value){
		$this->templateContent->{$key} = $value;
	}
	function getTopLevelKey($key){
		if(isset($this->templateContent->{$key})){
			return $this->templateContent->{$key};
		}else{
			PKLog::fatalError('Field "' . $key . '" does not exist in pass.json', 400);
		}
	}
	function removeTopLevelKey($key){
		unset($this->templateContent->{$key});
	}
	
	function getJSON(){
		return json_encode($this->templateContent);
	}
	
	function getResources($fullPath=false){
		$resources = array();
		if ($handle = opendir($this->templatePath)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && $entry != 'pass.json') {
					if($fullPath){
						$resources[] = $this->templatePath . '/' . $entry;
					}else{
						$resources[] = $entry;
					}
				}
			}
			closedir($handle);
		}
		return $resources;
	}
	
	function getPassType(){
		foreach($this->PKTypes as $type){
			if($this->templateContent->{$type}){
				return $type;
			}
		}
	}
}