<?php

class Request {
  private $AccessKey;
  private $SecretAccessKey;
  private $server;
  private $port;
  private $keyword;
  private $output;

  
  public function Request($keyword = "potter")
  {
    $this->AccessKey = get_option('bol_access_key');
    $this->SecretAccessKey = get_option('bol_secret_key');
    $this->server = 'openapi.bol.com';
    $this->port = '443';
    $this->keyword = $keyword;
    $this->getSearch();
  }
  
  private function calculateSignature($AccessKey, $SecretAccessKey, $today, $method, $url, $contentType, $sessionId) 
  {
	$stringToSign = $method."\n";
	$stringToSign .= "\n";
	$stringToSign .= $contentType."\n";
	$stringToSign .= $today."\n";
	$stringToSign .= "x-openapi-date:".$today."\n";
	if(!is_null($sessionId)) {
		$stringToSign .= "x-openapi-session-id:".$sessionId."\n";
	}
	$stringToSign .= $url;

	$signature = base64_encode(hash_hmac("SHA256", $stringToSign, $SecretAccessKey, true));

	return $AccessKey.":".$signature;
  }

  public function doRequest($method, $server, $port, $url, $parameters, $AccessKey, $SecretAccessKey, $content, $sessionId) 
  {
    $today = gmdate('D, d F Y H:i:s \G\M\T');

	if($method == 'GET') {
		$contentType =	'application/xml';
	} elseif ($method == 'POST') {
		$contentType =	'application/x-www-form-urlencoded';
	}

	$headers = $method." ".$url."".$parameters." HTTP/1.0\r\nContent-type: ".$contentType."\r\n";
	$headers .= "Host: ".$server."\r\n";
	$headers .= "Content-length: ".strlen($content)."\r\n";
	$headers .= "Connection: close\r\n";
	$headers .= "X-OpenAPI-Authorization: ".$this->calculateSignature($AccessKey, $SecretAccessKey, $today, $method, $url, $contentType, $sessionId)."\r\n";
	$headers .= "X-OpenAPI-Date: ".$today."\r\n";
	if(!is_null($sessionId)) {
		$headers .= "X-OpenAPI-Session-ID: ".$sessionId."\r\n";
	}
	$headers .= "\r\n";
	
	$socket = fsockopen('ssl://'.$server, $port, $errno, $errstr, 30);
	if (!$socket) {
		echo "$errstr ($errno)<br />\n";
	}
    
	fputs($socket, $headers);
	fputs($socket, $content);
	$ret = "";

	while (!feof($socket)) {
		$readLine = fgets($socket);
		$ret .= $readLine;
	}
	fclose($socket);

	return $ret;
  }
	
  public function getSearch()
  {
	$this->output.= $this->doRequest('GET', $this->server, $this->port, '/openapi/services/rest/catalog/v2/searchproducts/'.urlencode($this->keyword), '?ofset=0&nrProducts=10&output=product&', $this->AccessKey, $this->SecretAccessKey, '', null);
	
	$results = '<ul class="productlist">';
	// You can check for the right statuscode in the xml response, for now we will discard the statuscode

	$xml =  strstr($this->output, '<?xml');

	$phpobject = simplexml_load_string($xml); 
  foreach($phpobject->product as $item) {
	$id 					= $item->id;
	$section				= $item->section;
	$rating 				= $item->rating;
	$thumbnailUrl				= $item->thumbnailUrl;
	$largeImageUrl				= $item->largeImageUrl;
	$title					= $item->title;
	$description				= $item->description;
	$availabilityCode			= $item->availabilityCode;
	$availabilityDescription		= $item->availabilityDescription;
	$releaseDate				= $item->releaseDate;
	$price					= (double)$item->price;
	$listPrice				= (double)$item->listPrice;
	$eanCode				= $item->eanCode;
	$BINDING_DESCRIPTION			= $item->BINDING_DESCRIPTION;
	$BINDING_CODE				= $item->BINDING_CODE;
	$AGE_MIN				= $item->AGE_MIN;
	$externalUrl				= $item->externalUrl;
	$totalResultSize			= $item->totalResultSize;
  	
	if(@GetImageSize($thumbnailUrl)){
		//image exists!
	}else{
		$thumbnailUrl="";
	}
  // show defaultimage when image is missing
  if ($thumbnailUrl=="") { $thumbnailUrl="http://www.bol.com/nl/static/images/main/noimage_124x100default.gif"; }
  // check if there is a listprice (e.g. books don't have a listprice)
  if ($listPrice>0) { $listPricespan= '<span class="oldprice">&euro;'.number_format($listPrice, 2, '.', '').'</span>'; }
  else {	$listPricespan='';   	}
  // show rating   	
	if ($rating!="") { 	
  // split rating for image display and text                                                  
     	$nicerating = substr($rating, 0, 1); 
     	$nicerating.= '_'.substr($rating, -1); 
     	$altrating = str_replace("_", ".",$nicerating);
      $ratingspan='<span class="rating"><img alt="Score '.$altrating.' van de 5 sterren" src="http://review.bol.com/7628-nl_nl/'.$nicerating.'/5/rating.gif"></span>'; }
  else {
      $ratingspan='';
  }   
  // Build the desired HTML code for each searchResult item node and append it to $results
    	
	$results .= '<li><a class="product" href="'.$externalUrl.'"><span class="imageBox"><img alt="'.$title.'" src="'.$thumbnailUrl.'"></span><span class="productName">'.$title.'</span><span class="sectionName">'.$section.'</span> '.$listPricespan.'<span class="price">&euro;'.number_format($price, 2, '.','').'</span>'.$ratingspan.'</a>
  </li>';
  
	}
	$results.= '</ul>'; 
  
	return $results;
}

};
?>