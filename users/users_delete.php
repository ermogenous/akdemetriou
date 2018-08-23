<?php
include("../include/main.php");
$db = new Main();


if ($_GET["lid"] != "") {

	$db->db_tool_delete_row('users',$_GET["lid"],"`usr_users_ID` = ".$_GET["lid"]);
	header("Location: users.php?alert-success=User Deleted Succesfully");
	exit();

}
else {
	header ("Location: users.php");
	exit();
}

?>