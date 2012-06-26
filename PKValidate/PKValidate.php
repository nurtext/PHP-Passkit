<?php
class PKValidate {
	var $PKTypes = array('boardingPass', 'coupon', 'eventTicket', 'generic', 'storeCard');
	var $PKFields = array('headerFields', 'primaryFields', 'secondaryFields', 'auxiliaryFields', 'backFields');
	var $PKTransitTypes = array('PKTransitTypeAir', 'PKTransitTypeTrain', 'PKTransitTypeBus', 'PKTransitTypeBoat', 'PKTransitTypeGeneric');
	var $PKBarcodeFormats = array('PKBarcodeFormatQR', 'PKBarcodeFormatPDF417', 'PKBarcodeFormatAztec', 'PKBarcodeFormatText');
	var $PKTextAlignments = array('PKTextAlignmentLeft', 'PKTextAlignmentCenter', 'PKTextAlignmentRight', 'PKTextAlignmentJustified', 'PKTextAlignmentJustified');
	
	function validate($filePath){	
		$errors = array();
		
		// Load file
		if(!file_exists($filePath)){
			PKLog::fatalError('File "'.$filePath.'" does not exist.',404);
		}
		
		$content = file_get_contents($filePath);
		
		if(empty($content)){
			return $this->_error(0);
		}
		
		if(!$json = json_decode($content)){
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					$json_error = 'Unknown JSON parsing error';
					break;
				case JSON_ERROR_DEPTH:
					$json_error = 'Maximum stack depth exceeded';
					break;
				case JSON_ERROR_STATE_MISMATCH:
					$json_error = 'Underflow or the modes mismatch';
					break;
				case JSON_ERROR_CTRL_CHAR:
					$json_error = 'Unexpected control character found';
					break;
				case JSON_ERROR_SYNTAX:
					$json_error = 'Syntax error, malformed JSON';
					break;
				case JSON_ERROR_UTF8:
					$json_error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
					break;
				default:
					$json_error = 'Unknown JSON parsing error';
			}
			return $this->_error(1, $json_error);
		}
		
		if(!is_object($json)){ return $this->_error(2);	}
		
		// Check required Top-Level Keys
		if(!isset($json->passTypeIdentifier) || empty($json->passTypeIdentifier)){ $errors[] = $this->_error(3); }
		if(!isset($json->description) || empty($json->description)){ $errors[] = $this->_error(30); }
		if(!isset($json->formatVersion) || empty($json->formatVersion)){ $errors[] = $this->_error(4); }
		if(!isset($json->organizationName) || empty($json->organizationName)){ $errors[] = $this->_error(5); }
		if(!isset($json->serialNumber) || empty($json->serialNumber)){ $errors[] = $this->_error(6); }
		if(!isset($json->teamIdentifier) || empty($json->teamIdentifier)){ $errors[] = $this->_error(7); }
		
		// Check Top-Level Key values
		if($json->formatVersion != 1){ $errors[] = $this->_error(8);	}
		
		// Check for style-specific key (should be only 1)
		$nk = 0;
		$ptype = 'generic';
		foreach($this->PKTypes as $t){
			if(isset($json->{$t})){ 
				$ptype = $t;
				$nk++; 
			}
		}
		if($nk != 1){ $errors[] = $this->_error(9); }
		if(!is_object($json->{$ptype})){ $errors[] = $this->_error(10); }
		
		// Visual appearance keys
		if(isset($json->backgroundColor)){
			if(!preg_match('/rgb\(([0-9]{1,3})(,|, )([0-9]{1,3})(,|, )([0-9]{1,3})\)/', $json->backgroundColor)){
				$errors[] = $this->_error(11);
			}
		}
		if(isset($json->foregroundColor)){
			if(!preg_match('/rgb\(([0-9]{1,3})(,|, )([0-9]{1,3})(,|, )([0-9]{1,3})\)/', $json->foregroundColor)){
				$errors[] = $this->_error(12);
			}
		}
		if(isset($json->labelColor)){
			if(!preg_match('/rgb\(([0-9]{1,3})(,|, )([0-9]{1,3})(,|, )([0-9]{1,3})\)/', $json->labelColor)){
				$errors[] = $this->_error(13);
			}
		}
		
		// Relevance keys
		if(isset($json->locations)){
			if(!is_array($json->locations)){
				$errors[] = $this->_error(14);
			}
			foreach($json->locations as $l){
				if(!is_object($l)){ 
					$errors[] = $this->_error(15);
				}
				if(!isset($l->latitude) || !isset($l->longitude)){
					$errors[] = $this->_error(16);
				}
			}
		}
		if(isset($json->relevantDate)){
			if(!is_string($json->relevantDate) || !strtotime($json->relevantDate)){
				$errors[] = $this->_error(17);
			}
		}
		
		// Boarding Pass transit type key
		if($ptype == 'boardingPass'){
			if(!isset($json->boardingPass->transitType)){
				$errors[] = $this->_error(18);
			}
			if(!is_string($json->boardingPass->transitType)){
				$errors[] = $this->_error(19);
			}
			if(!in_array($json->boardingPass->transitType, $this->PKTransitTypes)){
				$errors[] = $this->_error(20);
			}
		}
		
		// Barcode key
		if(isset($json->barcode)){
			if(!isset($json->barcode->message)){
				$errors[] = $this->_error(21);
			}
			if(!isset($json->barcode->messageEncoding)){
				$errors[] = $this->_error(22);
			}
			if(!isset($json->barcode->format)){
				$errors[] = $this->_error(23);
			}
			if(!in_array($json->barcode->format, $this->PKBarcodeFormats)){
				$errors[] = $this->_error(24);
			}
		}
		
		// Fields
		foreach($this->PKFields as $ft){
			if(isset($json->{$ptype}->{$ft})){
				if(!is_array($json->{$ptype}->{$ft})){
					$errors[] = $this->_error(25,$ft);
				}else{
					foreach($json->{$ptype}->{$ft} as $field){
						if(!is_object($field)){
							$errors[] = $this->_error(26,$ft);
						}else{
							if(!isset($field->key)){
								$errors[] = $this->_error(27,$ft);
							}else{
								if(!isset($field->value)){
									$errors[] = $this->_error(28,$field->key,$ft);
								}
								if(isset($field->textAlignment)){
									if(!in_array($field->textAlignment, $this->PKTextAlignments)){
										$errors[] = $this->_error(29,$field->key,$ft);
									}
								}
							}
						}
					}	
				}
			}
		}
		
		if(empty($errors)){
			return true;
		}else{
			return $errors;
		}
	}
	
	private function _error($id, $details=null, $details2=null){
		$errors = array(
			0 => array(
				'error'=>'File is empty', 
				'details'=>'',
				'documentation_url'=>''
			),
			1 => array(
				'error' => 'Error while parsing error', 
				'details' => $details,
				'documentation_url' => ''
			),
			2 => array(
				'error' => 'File is no JSON dictionary', 
				'details' => 'The main node of the pass.json file should be an object',
				'documentation_url' => 'https://developer.apple.com/library/prerelease/ios/documentation/UserExperience/Reference/PassKit_Bundle/Chapters/TopLevel.html'
			),
			3 => array(
				'error' => 'Top-level key "passTypeIdentifier" is required',
				'details' => '',
				'documentation_url' => ''
			),
			4 => array(
				'error' => 'Top-level key "formatVersion" is required',
				'details' => '',
				'documentation_url' => ''
			),
			5 => array(
				'error' => 'Top-level key "organisationName" is required',
				'details' => '',
				'documentation_url' => ''
			),
			6 => array(
				'error' => 'Top-level key "serialNumber" is required',
				'details' => '',
				'documentation_url' => ''
			),
			7 => array(
				'error' => 'Top-level key "teamIndentifier" is required',
				'details' => '',
				'documentation_url' => ''
			),
			8 => array(
				'error' => 'Top-level key "formatVersion" should have value "1"',
				'details' => '',
				'documentation_url' => ''
			),
			9 => array(
				'error' => 'There should be exactly 1 style-specific information key',
				'details' => '',
				'documentation_url' => ''
			),
			10 => array(
				'error' => 'The style-specific information key should be an object',
				'details' => '',
				'documentation_url' => ''
			),
			11 => array(
				'error' => 'Top-level key "backgroundColor" should be a string formatted as an CSS-style RGB triple',
				'details' => '',
				'documentation_url' => ''
			),
			12 => array(
				'error' => 'Top-level key "foregroundColor" should be a string formatted as an CSS-style RGB triple',
				'details' => '',
				'documentation_url' => ''
			),
			13 => array(
				'error' => 'Top-level key "labeldColor" should be a string formatted as an CSS-style RGB triple',
				'details' => '',
				'documentation_url' => ''
			),
			14 => array(
				'error' => 'Top-level key "locations" should be an array',
				'details' => '',
				'documentation_url' => ''
			),
			15 => array(
				'error' => 'Child nodes of key "locations" should be objects',
				'details' => '',
				'documentation_url' => ''
			),
			16 => array(
				'error' => 'Location dictionary keys should contain at least the keys "latitude" and "longitude"',
				'details' => '',
				'documentation_url' => ''
			),
			17 => array(
				'error' => 'Top-level key "relevantDate" should be an ISO 8601 date string',
				'details' => '',
				'documentation_url' => ''
			),
			18 => array(
				'error' => 'Key "transitType" should be set for Pass Type "boardingPass"',
				'details' => '',
				'documentation_url' => ''
			),
			19 => array(
				'error' => 'Key "transitType" should be a string',
				'details' => '',
				'documentation_url' => ''
			),
			20 => array(
				'error' => 'Key "transitType" has an invalid value',
				'details' => '',
				'documentation_url' => ''
			),
			21 => array(
				'error' => 'Top-level key "barcode" should contain child "message"',
				'details' => '',
				'documentation_url' => ''
			),
			22 => array(
				'error' => 'Top-level key "barcode" should contain child "messageEncoding"',
				'details' => '',
				'documentation_url' => ''
			),
			23 => array(
				'error' => 'Top-level key "barcode" should contain child "format"',
				'details' => '',
				'documentation_url' => ''
			),
			24 => array(
				'error' => 'Child "format" of top-level key "barcode" has an invalid value',
				'details' => '',
				'documentation_url' => ''
			),
			25 => array(
				'error' => 'Key "'.$details.'" should be an array',
				'details' => '',
				'documentation_url' => ''
			),
			26 => array(
				'error' => 'A field in "'.$details.'" is not an object',
				'details' => '',
				'documentation_url' => ''
			),
			27 => array(
				'error' => 'A field in "'.$details.'" is missing the key "key"',
				'details' => '',
				'documentation_url' => ''
			),
			28 => array(
				'error' => 'The field "'.$details.'" in "'.$details2.'" is missing the key "value"',
				'details' => '',
				'documentation_url' => ''
			),
			29 => array(
				'error' => 'The field "'.$details.'" in "'.$details2.'" is has an invalid value set for "textAlignment"',
				'details' => '',
				'documentation_url' => ''
			),
			30 => array(
				'error' => 'Top-level key "description" is required',
				'details' => 'Since iOS 6 beta 2 this field is required',
				'documentation_url' => ''
			)
			
		);
		
		$error = array_merge(
			array(
				'errno' => $id
			),
			$errors[$id]
		);
		return $error;
	}
}
?>