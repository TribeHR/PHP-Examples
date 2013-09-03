<?php
/**
 * A very basic example of what you can do using TribeHR webhooks without the API.
 * This uses a hard-coded array to simply send a basic email when specific TribeHR Users
 * add a new LeaveRequest.
 * To see how to get more information about the LeaveRequest or the adding User, look to 
 * more advanced examples that connect to the TribeHR API.
 */

// Create one array entry per notification rule you wish to create:
// key: adding user's ID
// value: the email address(es) to notify when the adding user creates a LeaveRequest - either a string or an array
$leaveApprovers = array(
	12 => 'supervisorperson1@mycompany.com',
	3 => 'supervisorperson1@mycompany.com',
	5 => array('hr_manager@mycompany.com', 'ceo@mycompany.com'),
);

if (!empty($_POST['user_id']) && !empty($leaveApprovers[$_POST['user_id']])) {
	mail(
		implode(', ', (array)$leaveApprovers[$_POST['user_id']]),
		sprintf('%s just requested some leave', $_POST['user_name']),
		'You should log in to TribeHR to approve it'
	);
}
?>
