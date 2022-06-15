<?php

ini_set("memory_limit", '-1');
date_default_timezone_set("Asia/Jakarta");

require_once "RollingCurl/RollingCurl.php";
require_once "RollingCurl/Request.php";

echo banner().PHP_EOL;

enterlist:
$listname = readline(" Enter list: ");
if(empty($listname) || !file_exists($listname)) {
    echo" [?] list not found".PHP_EOL;
    goto enterlist;
}

$lists = array_unique(explode("\n", str_replace("\r", "", file_get_contents($listname))));

$savedir = readline(" Save to dir (default: Result): ");
$dir = empty($savedir) ? "Result" : $savedir;
if(!is_dir($dir)) mkdir($dir);
chdir($dir);

$no = 0;
$total = count($lists);
$ape = 0;
$dead = 0;
$unknown  = 0;

echo PHP_EOL;
$rollingCurl = new \RollingCurl\RollingCurl();

foreach($lists as $domain) {
    $apikey = "Go README.MD";
    $c++;
    if(empty($domain)) continue;
    $domain = str_replace(" ", "", $domain);
    $rollingCurl->get("https://domain-availability.whoisxmlapi.com/api/v1?apiKey=$apikey&domainName=$domain&credits=DA");
}

$rollingCurl->setCallback(function(\RollingCurl\Request $request, \RollingCurl\RollingCurl $rollingCurl) use (&$results) {
  global $listname, $dir, $no, $total, $live, $dead, $unknown;
    $no++;
    $deletelist = 1;
  
$json_decode = file_get_contents($request->getUrl(), PHP_URL_QUERY);
$api_result = json_decode($json_decode , true);
$domain = $api_result['DomainInfo']['domainName'];
$miss = $api_result['DomainInfo']['domainAvailability'];

   if ($miss == "AVAILABLE") {
        $live++;
        file_put_contents("AVAILABLE.txt", $domain.PHP_EOL, FILE_APPEND);
        echo color()["LG"].$miss." => ".$domain;
    }                  
    else if($miss == "UNAVAILABLE") {
        $dead++;
        file_put_contents("UNAVAILABLE.txt", $domain.PHP_EOL, FILE_APPEND);
        echo color()["LR"].$miss." => ".$domain;
    }
    else{
        $unknown++;
        $deletelist = 0;
        file_put_contents("unknown.txt", $domain.PHP_EOL, FILE_APPEND);
        echo color()["LW"]."UNKNOWN".color()["WH"]." => ".$domain;
    }
    
echo color()["LW"]." | ".color()["YL"]."BULK ".color()["CY"]." domaDomain Chek ".color()["LR"]."Natama Chek Domain".color()["WH"];
echo PHP_EOL;
})->setSimultaneousLimit((int) 3)->execute();

echo PHP_EOL." -- Total: ".$total." - AVAILABLE: ".$live." - UNAVAILABLE: ".$dead." - UNKNOWN: ".$unknown.PHP_EOL." Saved to dir \"".$dir."\"".PHP_EOL;

function banner() {
  $out = PHP_EOL.PHP_EOL   .color()["LW"]."✅ Domain Availability Bulk Chek ✅".color()["WH"]."

".color()["WH"].color()["LW"]." 💘 Made BY : Natama Entertainment 💘".color()["WH"]."
".color()["WH"].PHP_EOL.PHP_EOL;
  return $out;
}

function color() {
  return array(
    "LW" => "\e[1;37m",
    "WH" => "\e[0m",
    "YL" => "\e[1;33m",
    "LR" => "\e[1;31m",
    "MG" => "\e[0;35m",
    "LM" => "\e[1;35m",
    "CY" => "\e[1;36m",
    "LG" => "\e[1;32m"
  );
}