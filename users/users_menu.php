<?php
global $db,$main;
$link = $main["site_url"]."/users/";
?>
<div>
<table width="500" border="0" cellspacing="0" cellpadding="0" align="left" class="menu_left_links">
  <tr>
    <td width="70"><a href="<?php echo $link;?>users.php">Users</a></td>
    <td width="130"><a href="<?php echo $link;?>groups.php">Users Groups </a></td>
    <td width="100"><a href="<?php echo $link;?>permissions.php">Permissions</a></td>
    <td width="100"><a href="<?php echo $link;?>codes.php">Codes</a></td>
    <td width="100"><a href="<?php echo $link;?>backup_db.php">Backup DB</a></td>
  </tr>
</table> 
</div><br />