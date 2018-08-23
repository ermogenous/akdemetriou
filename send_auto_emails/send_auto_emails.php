<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

/*
$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
    //Server settings
    //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
    //$mail->isSMTP();                                      // Set mailer to use SMTP
    //$mail->CharSet="UTF-8";
    //$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    //$mail->SMTPAuth = true;                               // Enable SMTP authentication
    //$mail->Username = 'micacca@gmail.com';                 // SMTP username
    //$mail->Password = 'Kmariou24??';                           // SMTP password
    //$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    //$mail->Port = 465;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('micacca@poutsos.com', 'Mailer');
    $mail->addAddress('ermogenousm@gmail.com', 'Michael Ermogenous');     // Add a recipient
    $mail->addReplyTo('micacca@poutsos.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}

$to = 'ermogenousm@gmail.com';
$subject = 'subject';
$body = 'some body';
$headers = 'From: micacca@gmail.com';

if (mail($to, $subject, $body, $headers)){
    echo "Mail sent";
}
else {
    echo 'Error sending mail';
}
*/
include("../include/main.php");
include("../include/tables.php");



$db = new Main(1,'UTF-8');
$db->admin_title = "Send Auto Emails";

$search_sql = '';
if ($_GET["search_action"] == 'search') {
	$_SESSION['send_auto_emails']['search'] = 'search';
	$_SESSION['send_auto_emails']['text'] = $_GET["search_text"];
	$_SESSION['send_auto_emails']['type'] = $_GET["search_type"];
}

if ($_GET["search_action"] == 'clear') {
	$_SESSION['send_auto_emails']['search'] = '';
	$_SESSION['send_auto_emails']['text'] = '';
	$_SESSION['send_auto_emails']['type'] = '';
}

if ($_SESSION['send_auto_emails']['search'] == 'search') {
	$search_sql = $_SESSION['send_auto_emails']['type']." LIKE '%".addslashes($_SESSION['send_auto_emails']['text'])."%'";
	//echo $search_sql;
}


$table = new draw_table('send_auto_emails','sae_send_auto_emails_serial','DESC');
$table->extras = $search_sql;
$table->generate_data();

$db->show_header();
$table->show_pages_links();
?>
<script>
function clear_search() {
	document.getElementById('search_action').value = 'clear';
	document.getElementById('search_form').submit();
}
</script>

<form action="" method="get" id="search_form" name="search_form">
<table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><input name="search_text" type="text" id="search_text" size="50" value="<?php echo $_GET["search_text"];?>" />
      <select name="search_type" id="search_type">
        <option value="sae_label2" <?php if ($_SESSION["send_auto_emails"]["type"] == 'sae_label2') echo 'selected="selected"';?>>Policy Numbers</option>
        <option value="sae_email_to" <?php if ($_SESSION["send_auto_emails"]["type"] == 'sae_email_to') echo 'selected="selected"';?>>Email to</option>
        <option value="sae_email_to_name" <?php if ($_SESSION["send_auto_emails"]["type"] == 'sae_email_to_name') echo 'selected="selected"';?>>Email to Name</option>
        <option value="sae_email_subject" <?php if ($_SESSION["send_auto_emails"]["type"] == 'sae_email_subject') echo 'selected="selected"';?>>Email Subject</option>
        <option value="sae_agent_code" <?php if ($_SESSION["send_auto_emails"]["type"] == 'sae_agent_code') echo 'selected="selected"';?>>Agent Code</option>
        <option value="sae_email_body" <?php if ($_SESSION["send_auto_emails"]["type"] == 'sae_email_body') echo 'selected="selected"';?>>Email Body</option>
      </select>
      <input type="submit" name="button" id="button" value="Search" />
      <input name="search_action" type="hidden" id="search_action" value="search" />
      <input type="button" name="button2" id="button2" value="Clear" onclick="clear_search();" /></td>
    </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
</table>

</form>
<br />
<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" class="row_table_border">
  <tr class="row_table_head">
    <td width="38" align="center"><?php $table->display_order_links('ID','sae_send_auto_emails_serial');?></td>
    <td width="72" align="left"><?php $table->display_order_links('Type','sae_type');?></td>
    <td width="54" align="center"><?php $table->display_order_links('Active','sae_active');?></td>
    <td width="140" align="center"><?php $table->display_order_links('Send Date/Time','sae_send_datetime');?></td>
    <td width="81" align="center"><?php $table->display_order_links('Result','sae_send_result');?></td>
    <td width="82" align="center"><?php $table->display_order_links('Agent Code','sae_agent_code');?></td>
    <td width="440" align="center"><?php $table->display_order_links('Email','sae_email_to');?></td>
    <td colspan="2" align="center"><a href="send_auto_emails_modify.php">New</a></td>
  </tr>
<?php
$i=0;
while ($row = $table->fetch_data()) {
$i++;
?>
  <tr class="row_table_<?php if ($i%2 == 0) echo "odd"; else echo "even";?>">
    <td height="30" align="center" class="main_text"<?php if ($row["sae_active"] == 'I') echo " style=\"text-decoration:line-through\"";?>><?php echo $row["sae_send_auto_emails_serial"];?></td>
    <td align="left" class="main_text"<?php if ($row["sae_active"] == 'I') echo " style=\"text-decoration:line-through\"";?>><?php echo $row["sae_type"];?></td>
    <td align="center" class="main_text"<?php if ($row["sae_active"] == 'I') echo " style=\"text-decoration:line-through\"";?>><?php echo $row["sae_active"];?></td>
    <td align="center" class="main_text"<?php if ($row["sae_active"] == 'I') echo " style=\"text-decoration:line-through\"";?>><?php echo $db->convert_date_format($row["sae_send_datetime"],'yyyy-mm-dd','dd/mm/yyyy',1);?></td>
    <td align="center" class="main_text"<?php if ($row["sae_active"] == 'I') echo " style=\"text-decoration:line-through\"";?>><?php echo $row["sae_send_result"];?></td>
    <td align="center" class="main_text"<?php if ($row["sae_active"] == 'I') echo " style=\"text-decoration:line-through\"";?>><?php echo $row["sae_agent_code"];?></td>
    <td align="center" class="main_text"<?php if ($row["sae_active"] == 'I') echo " style=\"text-decoration:line-through\"";?>><?php echo substr($row["sae_email_to"],0,55); if (strlen($row["sae_email_to"]) > 55) {echo "...";}?></td>
    <td width="50" align="center" class="main_text"><a href="send_auto_emails_modify.php?lid=<?php echo $row["sae_send_auto_emails_serial"];?>">Modify</a></td>
    <td width="43" align="center" class="main_text"><a href="send_auto_emails_send.php?lid=<?php echo $row['sae_send_auto_emails_serial']; ?>" target="_blank">Send</a></td>
  </tr>
<?php
}
?>
</table><br />
<div align="center"><a href="send_auto_email_execute_all.php" target="_blank">Execute all pending emails</a></div>
<?php
$db->show_footer();
?>
