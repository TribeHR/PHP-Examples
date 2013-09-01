<?php
// key: requesting user's ID
// value: the email address to notify
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
