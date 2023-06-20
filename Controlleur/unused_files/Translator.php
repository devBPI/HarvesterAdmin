<?php
	if(empty($_POST['input']) or empty($_POST['patern']))
	{	
		die(header("HTTP/1.0 500 Argument manquant"));
	}
	$input=urlencode($_POST['input']);
	$patern=urlencode($_POST['patern']);
	$url = 'http://127.0.0.1:8080/CatalogueWebService/api/xslt/transform?xml='.$input.'&xslt='.$patern;
	$detail = @file_get_contents($url);
	if($detail==false)
	{
		die(header("HTTP/1.0 500 Arguments invalides"));
	}
	var_dump($detail);
	$simpleXml = new SimpleXMLElement($detail);
	$xmlTxt =  $simpleXml->asXML();
	$xml = new DOMDocument('1.0', 'utf-8');
	$xml->loadXML($xmlTxt);

	$xsl = new DOMDocument;
	$xsl->load("../xsl/translate.xsl");
	$proc = new XSLTProcessor;
	$proc->importStyleSheet($xsl);
	echo $proc->transformToXML($xml);

	//echo htmlspecialchars($detail);
?>


