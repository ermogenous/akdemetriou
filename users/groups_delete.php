<?php
include("../include/main.php");
$db = new Main();


if ($_GET["lid"] != "") {


	$db->db_tool_delete_row('users_groups',$_GET["lid"],"`usg_users_groups_ID` = ".$_GET["lid"]);
	
	header("Location: groups.php?info=User Deleted Succesfully");
	exit();

}
else {
	header ("Location: groups.php");
	exit();
}

?>