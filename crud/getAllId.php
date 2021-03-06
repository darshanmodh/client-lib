<?php
require_once('../Connection.php');
try
{	
	// Here we set the option array for the Webservice : we want customers resources
	$opt['resource'] = $_GET['resource'];
	// We set all ids from customer resource
	
	$xml = $webService->get($opt);	
	// Here we get the elements from children of customer markup which is children of prestashop root markup	
	$resources = $xml->children()->children();

	foreach ($resources as $resource)
	{
		echo $resource->attributes() . "\n";
	}
}
catch (PrestaShopWebserviceException $e)
{
	// Here we are dealing with errors
	$trace = $e->getTrace();
	if ($trace[0]['args'][0] == 404) echo 'Bad ID';
	else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
	else echo 'Other error';
}
?>
