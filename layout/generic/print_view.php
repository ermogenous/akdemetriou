<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title><?php echo $db->admin_title;?></title>
<link href="<?php echo $db->admin_layout_url;?>style.css" rel="stylesheet" type="text/css" />
<?php echo $db->admin_more_head;?>

<script>
function load_html() {

var page_html = window.opener.document.getElementById('print_view_section_html').innerHTML;

document.getElementById('print_view_main_html').innerHTML = page_html;


}
</script>

</head>

<body onload="load_html();">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><div id="print_view_main_html" name="print_view_main_html"></div></td>
  </tr>
</table>
</body>
</html>
