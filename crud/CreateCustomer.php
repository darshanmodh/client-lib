<?php
require_once('../Connection.php');
// Here we use the WebService to get the schema of "customers" resource
try
{
	$custwebService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
	$opt = array('resource' => 'customers');
	
	$xml = $custwebService->get(array('url' => PS_SHOP_PATH.'/api/customers?schema=blank'));
	
	$resources = $xml->children()->children();
	
	$Cxml =simplexml_load_file("php://input");	
}
catch (PrestaShopWebserviceException $e)
{
	// Here we are dealing with errors
	$trace = $e->getTrace();
	if ($trace[0]['args'][0] == 404) echo 'Bad ID';
	else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
	else echo 'Other error';
}
// Here we have XML before update, lets update XML

	foreach ($resources as $nodeKey => $node )
	{
		$f=0;
		foreach ($Cxml as $chNodeKey=> $chNode)
		{
			if($nodeKey === $chNodeKey)
			{
				$f=1;
				$resources->$nodeKey = $chNode;
			}			
		}
		if($f == 0) {		
			$resources->$nodeKey = null;
		}
	}

	try
	{
		$opt = array('resource' => 'customers');
		
			$opt['postXml'] = $xml->asXML();
			$xml = $custwebService->add($opt);
			$resources = $xml->children()->children();
			foreach($resources as $child=>$ch)
			{
				echo $child . ": " . $ch . "<br>";
			}
			echo "Successfully added.";

	}
	catch (PrestaShopWebserviceException $ex)
	{
		// Here we are dealing with errors
		$trace = $ex->getTrace();
		if ($trace[0]['args'][0] == 404) echo 'Bad ID';
		else if ($trace[0]['args'][0] == 401) echo 'Bad auth kdey';
		else echo 'Other error<br />'.$ex->getMessage();
	}
?>
