<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');


if (isset($_GET["placa"]) && $_GET["placa"] !== "") {
	
	$url_sinesp = "https://cidadao.sinesp.gov.br/sinesp-cidadao/mobile/consultar-placa/v4";
    $placa   = $_GET["placa"];
    $pwd = $placa . "#8.1.0#g8LzUadkEHs7mbRqbX5l";
    $token = hash_hmac('sha1', $placa, $pwd, false);
    $random_ip = (string)mt_rand(1,255).".".mt_rand(0,255).".".mt_rand(0,255).".".mt_rand(0,255);
    $latitude = (( 20000/111000.0 * sqrt(rand(1,1000)) ) * cos(2 * 3.141592654 * rand(1,1000)) + -15.7942287);
    $longitude = (( 20000/111000.0 * sqrt(rand(1,1000)) ) * sin(2 * 3.141592654 * rand(1,1000)) + -47.8821658);
    $date = date('Y-m-d h:i:s', time());
    $request = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'
      . '<v:Envelope xmlns:v="http://schemas.xmlsoap.org/soap/envelope/">'
      . '<v:Header>'
      . '<b>samsung GT-I9192</b>'
      . '<c>ANDROID</c>'
      . '<d>8.1.0</d>'
      . '<i>'. $latitude .'</i>'
      . '<e>4.1.5</e>'
      . '<f>'. $random_ip .'</f>'
      . '<g>'. $token .'</g>'
      . '<k></k>'
      . '<h>'. $longitude .'</h>'
      . '<l>'. $date .'</l>'      
      . '<m>8797e74f0d6eb7b1ff3dc114d4aa12d3</m>' 
      . '</v:Header>'
      . '<v:Body>'
      . '<n0:getStatus xmlns:n0="http://soap.ws.placa.service.sinesp.serpro.gov.br/">'
      . '<a>'. $placa .'</a>'
      . '</n0:getStatus>'
      . '</v:Body>'
      . '</v:Envelope>';
      
    $headers = [
            'Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Content-length: ' . strlen($request),
        ];
  //  $proxy = '200.150.85.100:3128';
    $soap_do = curl_init();
    curl_setopt($soap_do, CURLOPT_URL, $url_sinesp);
 //   curl_setopt($soap_do, CURLOPT_PROXY, $proxy);
    curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
    curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($soap_do, CURLOPT_POST, true );
    curl_setopt($soap_do, CURLOPT_POSTFIELDS, $request);
    curl_setopt($soap_do, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($soap_do);
    $res =  utf8_encode($res);
	
//	print_r($res);
	
    $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $res);  
    $xml = new SimpleXMLElement($response);
    $body = $xml->xpath('//return')[0];
    $array = json_decode(json_encode((array)$body), true);
    $array = json_encode($array);
    echo "$array";
}
?>