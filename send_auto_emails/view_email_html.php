<?php
include("../include/main.php");

$db = new Main(1,'UTF-8');

if ($_GET["lid"] != "") {
	$sql = "SELECT * FROM send_auto_emails WHERE sae_send_auto_emails_serial = '".$_GET['lid']."'";
	$result = $db->query($sql);
	$data = $db->fetch_assoc($result);
}
else {
	header("Location:send_auto_emails.php");
	exit();
}
echo $data["sae_email_body"];
?>