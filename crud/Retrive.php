<?php
require_once('../Connection.php');
// Here we make the WebService Call
try
{
	$webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
	// Here we set the option array for the Webservice : we want customers resources
	$opt['resource'] = $_GET['resource'];
	// We set an id if we want to retrieve infos from a customer
	if (isset($_GET['id']))
	{
		$opt['id'] = (int)$_GET['id']; // cast string => int for security measures
	}
	else
	{
		echo "Invalid ID ";
		exit();
	}
	// Call
	$xml = $webService->get($opt);
	$c = simplexml_load_file("php://input");
	// Here we get the elements from children of customer markup which is children of prestashop root markup	
	$resources = $xml->children()->children();
}
catch (PrestaShopWebserviceException $e)
{
	// Here we are dealing with errors
	$trace = $e->getTrace();
	if ($trace[0]['args'][0] == 404) echo 'Bad ID';
	else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
	else echo 'Other error';
}
// if $resources is set we can lists element in it otherwise do nothing cause there's an error
if (isset($resources))
{
	foreach ($resources as $nodeKey => $node )
	{
		$f=0;
		foreach ($c as $chNodeKey => $chNode)
		{
			if($nodeKey === $chNodeKey)
			{
				$f=1;
				$c->$chNodeKey = $node;
			}			
		}		
		if($f == 0)
			$resources->$nodeKey = null;
	}
	$result = new SimpleXMLElement("<customer/>");
 	foreach ($c as $key => $val)
	{
		$track=$result->addChild($key,$val);
	}
	header("Content-Type: text/xml");
	echo $result->asXML();
}
?>
