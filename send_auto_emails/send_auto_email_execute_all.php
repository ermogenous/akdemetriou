<?php
ini_set("memory_limit","128M");
ini_set('max_execution_time', 600);


include("../include/main.php");
$db = new Main(1);
include("../triggers/send_auto_emails/send_auto_emails_class.php");

$emails = new send_auto_emails();
$emails->execute_all_emails();

$db->show_header();
echo "<strong>Result:</strong>".$emails->messages["error"]."<br><strong>Error Message:</strong>".$emails->messages["error_message"]."<br>";
foreach($emails->messages as $name => $value) {
	if ($name !== 'error' && $name !== 'error_message') {
		echo $value."<br>";
	}
}
$db->show_footer();
