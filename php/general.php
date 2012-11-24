<?php

function picnitRequest($url, $fields) {
	//Complete the url
	$url = "http://localhost/picnit/" . $url;
	
	//Init curl
	$ch = curl_init($url);

	foreach($fields as $key=>$value) 
		$data .= $key.'='.$value.'&';

	//Set up operations
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_VERBOSE, true); //Debug purposes

	//Execute the call
	$result = curl_exec($ch);

	//Get status code too
	$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	//Close connection
	curl_close($ch);

	//Return result and status
	return array( 'result' => $result, 'status' => $status );
}

function isLoggedIn() {
	return isset($_COOKIE['member_id']) && isset($_COOKIE['is_suspended']) && isset($_COOKIE['username']);
}

?>
