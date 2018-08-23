<?php
//ini_set('error_reporting','E_ALL & ~E_NOTICE');
ini_set('display_errors', '1');
ini_set('html_errors', '1');
ini_set('display_startup_errors', 1);

error_reporting(E_ALL);


ini_set("memory_limit","128M");
ini_set('max_execution_time', 600);


//phpinfo();
//exit();

include("../include/main.php");
$db = new Main(1);

include("send_auto_emails_class.php");
$emails = new send_auto_emails($_GET["lid"]);

$emails->send_email();

$db->show_header();
echo "<strong>Result:</strong>".$emails->messages["error"]."<br><strong>Error Message:</strong>".$emails->messages["error_message"]."<br>";
foreach($emails->messages as $name => $value) {
	if ($name != 'error' && $name != 'error_message') {
		echo $value."<br>";
	}
}
$db->show_footer();
