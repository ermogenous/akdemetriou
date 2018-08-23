<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title><?php echo $db->admin_title;?></title>
<LINK REL="SHORTCUT ICON" HREF="https://www.ydrogios.net/favicon.ico">
<link href="<?php echo $db->admin_layout_url;?>style.css" rel="stylesheet" type="text/css" />
<?php echo $db->admin_more_head;?>
</head>

<body <?php echo $db->admin_body;?> onload="<?php echo $db->admin_on_load;?>">
<?php 
if ($_GET["layout_action"] != "printer") {
?> 
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%"><img src="<?php echo $db->admin_layout_url;?>images/logo_new.jpg" /></td>
    
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right" class="menu_left_links">Welcome <?php echo $db->user_data["usr_name"];?>&nbsp;&nbsp;<a href="<?php echo $main["site_url"]."/index.php?action=logout";?>">Logout</a>&nbsp;&nbsp;<a href="?layout_action=printer&<?php echo $_SERVER["QUERY_STRING"];?>">Print</a>&nbsp;&nbsp;<a href="<?php echo $main["site_url"]."/layout/".$this->get_setting("admin_default_layout")."/print_view.php";?>" target="_blank">PrintV2</a></td>
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
                <td width="98%"><div id="main_text_html" name="main_text_html"><?php  } ?>