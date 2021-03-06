<?php
require_once('../Connection.php');
// Here we use the WebService to get the schema of "customers" resource
try
{
    $custwebService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
        if(isset($_GET['resource']))
        {         
            $opt = array('resource' => $_GET['resource']);            
            $xml = $custwebService->get(array('url' => PS_SHOP_PATH.'/api/'.$_GET['resource'].'?schema=blank'));           
            $resources = $xml->children()->children();
	
            $Cxml =simplexml_load_file("php://input");	
        }
        else {
            echo 'Resource is not set.';
            exit();
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
		$opt = array('resource' => $_GET['resource']);
		
			$opt['postXml'] = $xml->asXML();
			$xml = $custwebService->add($opt);			
                        $resources = $xml->children()->children();
			foreach($resources as $child=>$ch)
			{
				echo $child . ": " . $ch . "<br>";
			}                        			
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
