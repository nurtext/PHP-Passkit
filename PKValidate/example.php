<?php
require '../shared/PKLog.php';
require 'PKValidate.php';

// Load validator class
$validator = new PKValidate();

// Load pass.json file for validation
$result = $validator->validate('example_pass.json');

// Check result
if($result === true){
	// Valid pass.json, $result is true
	echo 'Valid pass.json file.';
}else{
	// Some errors occurred, $result is array with errors
	echo '<b>Some errors found:</b><br /><pre>';
	print_r($result);
	echo '</pre>';
}

?>