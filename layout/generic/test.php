<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title><? echo $db->admin_title;?></title>
<link href="<? echo $db->admin_layout_url;?>style.css" rel="stylesheet" type="text/css" />
<? echo $db->admin_more_head;?>
</head>

<body <? echo $db->admin_body;?>>
<? 
if ($_GET["layout_action"] != "printer") {
?> 
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="250"><img src="<? echo $db->admin_layout_url;?>images/logo.jpg" /></td>
    <td align="right" ><img src="<? echo $db->admin_layout_url;?>images/header2.jpg"/></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td align="right" class="menu_left_links"><a href="<? echo $main["site_url"]."/index.php?action=logout";?>">Logout</a>&nbsp;&nbsp;<a href="?layout_action=printer&<? echo $_SERVER["QUERY_STRING"];?>">Print</a></td>
  </tr>
  
  <tr>
    <td height="300" colspan="2" align="left" valign="top">
      <table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%">
        <tr>
          <td width="175" height="100%" valign="top" background="<? echo $db->admin_layout_url;?>images/left_menu_top_back.jpg"><table width="100%" border="0" cellpadding="0" cellspacing="0" >
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td width="11%">&nbsp;</td>
              <td width="89%"><? layout_main_menu();?></td>
            </tr>

          </table>          </td>
          <td width="14">&nbsp;</td>
          <td width="14" background="<? echo $db->admin_layout_url;?>images/center_vertical_line.jpg"><img src="<? echo $db->admin_layout_url;?>images/spacer.gif" width="1" height="1" /></td>
          <td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="14" colspan="2" background="<? echo $db->admin_layout_url;?>images/center_back_horizontal_line.jpg">&nbsp;</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td width="2%">&nbsp;</td>
                <td width="98%"><?  } ?>
				
				
				
				<? 
if ($_GET["layout_action"] != "printer") {
?></td>
              </tr>
            </table></td>
          <td width="14" align="left" valign="top" background="<? echo $db->admin_layout_url;?>images/center_vertical_line.jpg"><br />
          <br /><br /></td>
        </tr>
        <tr>
          <td height="14" colspan="5" background="<? echo $db->admin_layout_url;?>images/center_back_horizontal_line.jpg"><img src="<? echo $db->admin_layout_url;?>images/spacer.gif" width="1" height="1" /><img src="<? echo $db->admin_layout_url;?>images/spacer.gif" width="1" height="1" /></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td height="28" colspan="2" align="center">
      &copy;&nbsp;Copyright Ydrogios Insurance 2008</td>
  </tr>
</table>
<? 
}
?>
</body>
</html>
<?
$db->main_exit();
?>