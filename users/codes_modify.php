<?php 
include("../include/main.php");
include("codes_array.php");
$db = new Main();
$db->admin_title = "System Codes Modify";

//if user Rights = 0
//if ($db->user_data["user_rights"] == 1 && $_GET["lid"] == "") {
//	header("Location: users_modify.php?lid=".$db->user_data["users_ID"]);
//	exit();
//}
if ($_POST["action"] == "insert") {
	$db->check_restriction_area('insert');
	
	$db->db_tool_insert_row('settings',$_POST,'fld_');
	header("Location: codes.php");
	exit();

}
else if ($_POST["action"] == "update") {
	$db->check_restriction_area('update');

	$db->db_tool_update_row('settings',$_POST,"`stg_settings_serial` = ".$_POST["lid"],$_POST["lid"], 'fld_');
	header("Location: codes.php");
	exit();
	
}





if ($_GET["lid"] != "") {

	$sql = "SELECT * FROM `settings` WHERE `stg_settings_serial` = ".$_GET["lid"];
	$data = $db->query_fetch($sql);
}



$db->show_header();
include("users_menu.php");
?>


<form name="groups" method="post" action="" onSubmit="">
<table width="450" border="0" align="center" cellpadding="0" cellspacing="0" class="row_table_border">
  <tr class="row_table_head">
    <td height="26" colspan="2" align="center"><?php if ($_GET["lid"] == "") echo "Insert"; else echo "Update";?>&nbsp;Code</td>
    </tr>
  <tr>
    <td height="26"><strong>Code</strong></td>
    <td height="26"><input name="fld_stg_section" type="hidden" id="fld_stg_section" value="<?php echo $_SESSION["system_codes_class"];?>"><?php echo $system_classes[$_SESSION["system_codes_class"]];?></td>
  </tr>
  <tr>
    <td width="94" height="26"><strong>Name</strong></td>
    <td width="350" height="26"><input name="fld_stg_value" type="text" id="fld_stg_value" value="<?php echo $data["stg_value"];?>" size="50"></td>
  </tr>
  <tr>
    <td height="26">&nbsp;</td>
    <td height="26">&nbsp;</td>
  </tr>
  <tr>
    <td height="26"><input name="action" type="hidden" id="action" value="<?php if($_GET["lid"] == "") echo "insert"; else echo "update";?>">
      <input name="lid" type="hidden" id="lid" value="<?php echo $_GET["lid"];?>"></td>
    <td height="26"><input type="submit" name="Submit" value="Submit"></td>
  </tr>
</table>
</form>
<?php 
$db->show_footer();
?>
