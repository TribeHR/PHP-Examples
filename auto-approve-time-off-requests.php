<?php

// EXAMPLE: AUTO-APPROVE TIME OFF REQUESTS
//
// This code example illustrates how to listen for newly created requests for time off, then
// automatically approve them (this is great for organizations that have a more flexible time
// off policy). This script listens for a WebHook event, then connects to the site via the REST
// API to get the details and modify the details of the request.
//
// More on WebHooks: http://developers.tribehr.com/api/webhook-event-notification/
// More on the REST Api: http://developers.tribehr.com/api/api-introduction/
//
// PHP API Wrapper: https://github.com/TribeHR/TribeHR-PHP-Client


// Set your TribeHR credentials
define('SUBDOMAIN', '');				// Your TribeHR Sub Domain
define('USER', '');						// The username of an Administrator
define('KEY', '');						// The API KEY of the administrator above


// Include the API Wrapper
require('./TribeHR-PHP-Client/TribeHR.php');


// Let's only process the payload if we're going to actually be getting
// a LeaveRequest. Note - this logic can be easily extende to create a script that
// can handle multiple different WebHook types.
if(isset($_POST['object_id']) && isset($_POST['object']) && $_POST['object'] == 'LeaveRequest')
{

	// Create my connection to the site using the API Wrapper
	$TribeHRConnection = new TribeHRConnector(SUBDOMAIN, USER, KEY);


	// Get the specific Leave Request, as specified by the WebHook data
	$id = intval($_POST['object_id']);
	$tc = $TribeHRConnection->sendRequest('/leave_requests/'.$id.'.xml', 'GET');
	
	
	// Create the new request data, based on the old request. Some interesting constraints
	// caused by the current API version:
	//    - date_start and date_end exped m/d/Y format, but the API returns Y-m-d
	//    - the API requires a complete data object, so all fields must be specified
	//    - the status APPROVED == 1
	$old_request = simplexml_load_string($tc->response);
	$new_request = array(
		'id' => (string) $old_request->leave_request['id'],
		'comments' => (string) $old_request->leave_request['comments'] . "\nAuto-Approved",
		'date_start' => date('m/d/Y', strtotime($old_request->leave_request['date_start'])),
		'date_end' => date('m/d/Y', strtotime($old_request->leave_request['date_end'])),
		'days' => (string) $old_request->leave_request['days'],
		'leave_type_id' => (string) $old_request->leave_request['leave_type_id'],
		'user_id' => (string) $old_request->leave_request['user_id'],
		'status' => 1,
	);

	// Submit the new data
	$tc = $TribeHRConnection->sendRequest('/leave_requests/'.$id.'.xml', 'PUT', $new_request);
	
	// Let's echo our the response for good measure
	echo htmlentities($tc->response); 
}
else
{
	// Since there wasn't a valid payload, let's echo a message out
	echo 'This script listens for TribeHR WebHooks to auto-approve vacation requests';
}

?>