<?php
function template_header() {
	global $main,$db;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="SHORTCUT ICON" HREF="<?php echo $main["site_url"]; ?>/favicon.png">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="<?php echo $main["site_url"]; ?>/scripts/bootstrap-4/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js" integrity="sha384-slN8GvtUJGnv6ca26v8EzVaR9DC58QEwsIk9q1QXdCU8Yu8ck/tL/5szYlBbqmS+" crossorigin="anonymous"></script>
<title><?php echo $db->admin_title;?></title>
<link href="<?php echo $db->admin_layout_url;?>style.css" rel="stylesheet" type="text/css" />
<?php echo $db->admin_more_head;?>
</head>

<body <?php echo $db->admin_body;?> onload="<?php echo $db->admin_on_load;?>">
<?php 
if ($_GET["layout_action"] != "printer") {
?>
    <i class="fas fa-user"></i>
    <div class="alert alert-primary" role="alert">
        This is a primary alert—check it out!
    </div>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr >
        <td align="left" colspan="2"><img src="<?php echo $db->admin_layout_url;?>images/logo.png" /></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td width="250">&nbsp;</td>
    <td align="right" class="menu_left_links"><?php if ($db->user_data["usr_users_ID"] > 0) { ?>Welcome <?php echo $db->user_data["usr_name"];?>&nbsp;&nbsp;<a href="<?php echo $main["site_url"]."/login.php?action=logout";?>">Logout</a>&nbsp;&nbsp;<a href="<?php echo $main["site_url"]."/layout/".$db->get_setting("admin_default_layout")."/print_view.php";?>" target="_blank">PrintV2</a><?php } ?>&nbsp;</td>
  </tr>
  
  <tr>
    <td height="300" colspan="2" align="left" valign="top">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
        <tr>
          <td width="175" height="100%" valign="top" class="left_main_menu_backround"><table width="100%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td width="11%">&nbsp;</td>
              <td width="89%"><?php layout_main_menu();?></td>
            </tr>

          </table>          </td>
          <td width="14">&nbsp;</td>
          <td width="14" background="<?php echo $db->admin_layout_url;?>images/center_vertical_line.jpg"><img src="<?php echo $db->admin_layout_url;?>images/spacer.gif" width="1" height="1" /></td>
          <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="14" colspan="2" background="<?php echo $db->admin_layout_url;?>images/center_back_horizontal_line.jpg">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td width="2%">&nbsp;</td>
                <td width="98%"><div id="main_text_html" name="main_text_html"><?php  } 
//header
}
function template_footer() {
	global $main,$db;	
?></div><?php 
if ($_GET["layout_action"] != "printer") {
?></td>
              </tr>
            </table></td>
          <td width="14" align="left" valign="top" background="<?php echo $db->admin_layout_url;?>images/center_vertical_line.jpg"><br />
          <br /><br /></td>
        </tr>
        <tr>
          <td height="14" colspan="5" background="<?php echo $db->admin_layout_url;?>images/center_back_horizontal_line.jpg"><img src="<?php echo $db->admin_layout_url;?>images/spacer.gif" width="1" height="1" /><img src="<?php echo $db->admin_layout_url;?>images/spacer.gif" width="1" height="1" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td height="28" colspan="2" align="center">
      &copy;&nbsp;Copyright Ydrogios Insurance Company (Cyprus) Ltd 2013</td>
  </tr>
</table>
<?php 
}
?>
    <script src="<?php echo $main["site_url"]; ?>/scripts/bootstrap-4/js/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="<?php echo $main["site_url"]; ?>/scripts/bootstrap-4/js/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="<?php echo $main["site_url"]; ?>/scripts/bootstrap-4/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
<?php
$db->main_exit();
}//template_footer
?>