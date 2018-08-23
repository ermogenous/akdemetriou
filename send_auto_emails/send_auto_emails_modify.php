<?php
include("../include/main.php");
include("../include/tables.php");



$db = new Main(1,'UTF-8');
$db->admin_title = "Send Auto Email Modify";
if ($_POST["action"] == "insert") {
	$db->check_restriction_area('insert');
	
	$_POST["fld_sae_type"] = 'Manual';
	$_POST["fld_sae_send_result"] = 0;
	$_POST["fld_sae_send_datetime"] = $db->convert_date_format($_POST["fld_sae_send_datetime"],"dd/mm/yyyy","yyyy-mm-dd",1);
	$_POST["fld_sae_created_datetime"] = date("Y-m-d G:i:s");
	
	$db->db_tool_insert_row('send_auto_emails',$_POST,'fld_');
	header("Location: send_auto_emails.php");
	exit();

}
else if ($_POST["action"] == "update") {
	$db->check_restriction_area('update');
	
	$_POST["fld_sae_send_datetime"] = $db->convert_date_format($_POST["fld_sae_send_datetime"],"dd/mm/yyyy","yyyy-mm-dd",1);
	$_POST["fld_sae_created_datetime"] = $db->convert_date_format($_POST["fld_sae_created_datetime"],"dd/mm/yyyy","yyyy-mm-dd",1);

	$db->db_tool_update_row('send_auto_emails',$_POST,"`sae_send_auto_emails_serial` = ".$_POST["lid"],$_POST["lid"], 'fld_');
	header("Location: send_auto_emails.php");
	exit();
	
}


if ($_GET["lid"] != "") {
	$sql = "SELECT * FROM send_auto_emails WHERE sae_send_auto_emails_serial = '".$_GET['lid']."'";
	$result = $db->query($sql);
	$data = $db->fetch_assoc($result);


    if ($data["sae_send_auto_emails_serial"] < 1) {
		header("Location: send_auto_emails.php");
		$db->main_exit();
		exit();	
	}
}
$db->show_header();
?>
<form name="form1" method="post" action="">
  <table width="750" border="0" align="center" cellpadding="0" cellspacing="0" class="row_table_border">
    <tr class="row_table_head">
      <td colspan="3" align="center"><strong><?php if($_GET["lid"] == "") echo "Insert"; else echo "Update";?> Auto Email</strong></td>
    </tr>
    <tr>
      <td width="5">&nbsp;</td>
      <td width="165" height="28">Serial</td>
      <td width="580"><?php echo $data["sae_send_auto_emails_serial"];?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Active</td>
      <td><select name="fld_sae_active" id="fld_sae_active">
        <option value="A" <?php if ($data["sae_active"] == 'A') echo "selected";?>>Active</option>
        <option value="I" <?php if ($data["sae_active"] == 'I') echo "selected";?>>In-Active</option>
      </select></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Type</td>
      <td><?php echo $data["sae_type"];?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Send Result</td>
      <td><?php if ($_GET["lid"] != "") { ?> <input type="text" name="fld_sae_send_result" id="fld_sae_send_result" value="<?php echo $data["sae_send_result"];?>"><?php } ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Created Date/Time</td>
      <td><?php if ($_GET["lid"] != "") { ?><input type="text" name="fld_sae_created_datetime" id="fld_sae_created_datetime" value="<?php echo $db->convert_date_format($data["sae_created_datetime"],'yyyy-mm-dd','dd/mm/yyyy',1);?>"><?php } ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Send Date/Time</td>
      <td><?php if ($_GET["lid"] != "") { ?><input type="text" name="fld_sae_send_datetime" id="fld_sae_send_datetime" value="<?php echo $db->convert_date_format($data["sae_send_datetime"],'yyyy-mm-dd','dd/mm/yyyy',1);?>"><?php } ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Email To</td>
      <td><input name="fld_sae_email_to" type="text" id="fld_sae_email_to" value="<?php echo $data["sae_email_to"];?>" size="80"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Email To Name/s</td>
      <td><input name="fld_sae_email_to_name" type="text" id="fld_sae_email_to_name" value="<?php echo $data["sae_email_to_name"];?>" size="80"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Email From</td>
      <td><input name="fld_sae_email_from" type="text" id="fld_sae_email_from" value="<?php echo $data["sae_email_from"];?>" size="80"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Email From Name/s</td>
      <td><input name="fld_sae_email_from_name" type="text" id="fld_sae_email_from_name" value="<?php echo $data["sae_email_from_name"];?>" size="80"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Reply To</td>
      <td><input name="fld_sae_email_reply_to" type="text" id="fld_sae_email_reply_to" value="<?php echo $data["sae_email_reply_to"];?>" size="80"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Reply To Name/s</td>
      <td><input name="fld_sae_email_reply_to_name" type="text" id="fld_sae_email_reply_to_name" value="<?php echo $data["sae_email_reply_to_name"];?>" size="80"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">CC</td>
      <td><input name="fld_sae_email_cc" type="text" id="fld_sae_email_cc" value="<?php echo $data["sae_email_cc"];?>" size="80"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">BCC</td>
      <td><input name="fld_sae_email_bcc" type="text" id="fld_sae_email_bcc" value="<?php echo $data["sae_email_bcc"];?>" size="80"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Subject</td>
      <td><input name="fld_sae_email_subject" type="text" id="fld_sae_email_subject" value="<?php echo $data["sae_email_subject"];?>" size="80"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Message Body<br />
        <a href="view_email_html.php?lid=<? echo $data["sae_send_auto_emails_serial"];?>" target="_blank">View HTML</a></td>
      <td><textarea name="fld_sae_email_body" id="fld_sae_email_body" cols="60" rows="8"><?php echo $data["sae_email_body"];?></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Agent Code</td>
      <td><input type="text" name="fld_sae_agent_code" id="fld_sae_agent_code" value="<?php echo $data["sae_agent_code"];?>"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">Send Result Description</td>
      <td><textarea name="result_description" id="result_description" cols="60" disabled rows="6"><?php echo $data["sae_send_result_description"];?></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td height="28"><input name="action" type="hidden" id="action" value="<?php if($_GET["lid"] == "") echo "insert"; else echo "update";?>">
        <input name="lid" type="hidden" id="lid" value="<?php echo $_GET["lid"];?>"></td>
      <td><input type="submit" name="button" id="button" value="<?php if($_GET["lid"] == "") echo "Insert"; else echo "Update"?>"></td>
    </tr>
  </table>
</form>
<script>

var created_datetime = $('#fld_sae_created_datetime');
var send_datetime = $('#fld_sae_send_datetime');

send_datetime.datetimepicker({
	dateFormat: "dd/mm/yy",
	timeFormat: "HH:mm:ss"
});
created_datetime.datetimepicker({
	dateFormat: "dd/mm/yy",
	timeFormat: "HH:mm:ss"
});

</script>

<?php
$db->show_footer();
?>
