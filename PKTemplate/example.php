<?php
require '../shared/PKLog.php';
require 'PKTemplate.php';

// Load template folder
$template = new PKTemplate('example_template');

// Set field values
$fields = array(
	'event' => 'iTunes Event',
	'loc' => 'Cupertino',
	'terms' => "Generico offers this pass, including all information, software, products and services available from this pass or offered as part of or in conjunction with this pass (the \"pass\"), to you, the user, conditioned upon your acceptance of all of the terms, conditions, policies and notices stated here. Generico reserves the right to make changes to these Terms and Conditions immediately by posting the changed Terms and Conditions in this location.\n\nUse the pass at your own risk. This pass is provided to you \"as is,\" without warranty of any kind either express or implied. Neither Generico nor its employees, agents, third-party information providers, merchants, licnsors or the like warrant that the pass or its operation will be accurate, reliable, uninterrupted or error-free. No agent or representative has the authority to create any warranty regarding the pass on behalf of Generico. Generico reserves the right to change or discontinue at any time any aspect or feature of the pass."
);
$template->setKeys($fields);

// Set top level keys
$template->setTopLevelKey('formatVersion', 1);
$template->setTopLevelKey('passTypeIdentifier', 'pass.com.apple.test');
$template->setTopLevelKey('teamIdentifier', 'AGKMYZT43K');
$template->setTopLevelKey('organizationName', 'Apple Inc.');
$template->setTopLevelKey('serialNumber', 'nmyuxofgnb');

$template->setTopLevelKey('locations', array(
	(object)array('longtitude'=>25.0,'latitude'=>25.0),
	(object)array('longtitude'=>-7.0,'latitude'=>7.0)
));

// Output list of resources
echo '<b>Pass resources:</b><br /><pre>';
print_r($template->getResources());
echo '</pre><br />';

// Output JSON for pass (after setting field values)
echo '<b>Pass JSON:</b><br /><pre style="word-wrap:break-word;">';
echo $template->getJSON();
echo '</pre>';
?>