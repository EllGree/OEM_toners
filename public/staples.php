<?php
header('Content-Type: application/json; charset=utf-8');
if(empty($_GET['term'])) die('{error:"Search term is required"}');
$term = urlencode($_GET['term']);
$uri = "https://www.staples.com/searchux/common/api/v1/searchProxy?SEARCHUX2=true&ajaxRequest=true&deviceType=desktop&term={$term}";
$ref = "https://www.staples.com/{$term}/directory_{$term}";
$curl = curl_init();
$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
$header[] = "Cache-Control: max-age=0";
$header[] = "Connection: keep-alive";
$header[] = "Keep-Alive: 300";
$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
$header[] = "Accept-Language: en-us,en;q=0.5";
$header[] = "Pragma: ";
curl_setopt($curl, CURLOPT_URL, $uri);
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:7.0.1) Gecko/20100101 Firefox/7.0.12011-10-16 20:23:00");
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_REFERER, $ref);
curl_setopt($curl, CURLOPT_ENCODING, "gzip,deflate");
curl_setopt($curl, CURLOPT_AUTOREFERER, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION,true);
$txt = curl_exec($curl);
$error = curl_error($curl);
curl_close($curl);
if($error) die('{error:"'+$error+'"}');
if(empty($txt)) die('{error:"Nothing received from '+$uri+'"}');
echo $txt;