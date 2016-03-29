<?php

require_once('CRMDefaults.php');
require_once('LanguageHandler.php');
//require_once('DbHandler.php');

$lh = \creamy\LanguageHandler::getInstance();

// check required fields
$validated = 1;
if (!isset($_POST["extenid"])) {
	$validated = 0;
}

if ($validated == 1) {
	$extenid = $_POST["extenid"];
	

//uncomment to work	
    $url = "https://encrypted.goautodial.com/goAPI/goPhones/goAPI.php"; #URL to GoAutoDial API. (required)
    $postfields["goUser"] = "admin"; #Username goes here. (required)
    $postfields["goPass"] = "goautodial"; #Password goes here. (required)
    $postfields["goAction"] = "goDeletePhone"; #action performed by the [[API:Functions]]. (required)
    $postfields["responsetype"] = "json"; #json. (required)
    $postfields["exten_id"] = "$extenid"; #Desired User ID. (required)
    $postfields["hostname"] = $_SERVER['REMOTE_ADDR']; #Default value
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    $data = curl_exec($ch);
    curl_close($ch);
    $output = json_decode($data);
     
    if ($output->result=="success") {
    # Result was OK!
		ob_clean();
		print CRM_DEFAULT_SUCCESS_RESPONSE;
    } else {
		ob_clean(); 
		$lh->translateText("unable_delete_phone");
    }

} else {
	ob_clean(); $lh->translateText("some_fields_missing");
}
?>