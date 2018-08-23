<?php
include("../include/main.php");
$db = new Main();


if ($_GET["lid"] != "") {

	$sql = "DELETE FROM `permissions` WHERE `prm_permissions_ID` = ".$_GET["lid"]." LIMIT 1";
	$db->query($sql);
	header("Location: permissions.php?info=Permissions Deleted Succesfully");
	exit();

}
else {
	header ("Location: permissions.php");
	exit();
}

?>