<?php 
//include_once("../include/common.php");
function layout_main_menu() {
global $main,$db;
?>
<script language="JavaScript" type="text/javascript">
function expand_menu(menu_ID,action) {
var div = 'div_'+menu_ID;
var image = 'image_'+menu_ID;

	if (document.getElementById(div).style.display == 'none' || action == 'show') {
		document.getElementById(div).style.display = 'block'
		document.getElementById(image).src = '<? echo $db->admin_layout_url;?>images/minus.jpg';
	}
	else if (document.getElementById(div).style.display == 'block' || action == 'hide') {
		document.getElementById(div).style.display = 'none'
		document.getElementById(image).src = '<? echo $db->admin_layout_url;?>images/plus.jpg';
	}

}
</script>

<table width="165" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="10">&nbsp;</td>
    <td width="95" valign="top"><table border="0" cellspacing="0" cellpadding="0" class="menu_left_links">
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td><a href="<? echo $main["site_url"];?>/home.php">Home</a></td>
      </tr>

      <?php 

if ($db->user_data["usr_user_rights"] != "") {

if ($db->user_data["usr_user_rights"] == 0) {
	$extra = "	 ";
}
else {
	$extra = "	AND us.usr_users_ID = ".$db->user_data["usr_users_ID"]." AND pel.prl_view = 1 ";
}

$sqla = "SELECT per.prm_permissions_ID, per.prm_name, per.prm_filename , pel.*, usg.* FROM `permissions` as per
	LEFT OUTER JOIN `permissions_lines` as pel ON per.prm_permissions_ID = pel.prl_permissions_ID
	LEFT OUTER JOIN  users_groups as usg ON usg.usg_users_groups_ID = pel.prl_users_groups_ID
	LEFT OUTER JOIN  users as us ON usg.usg_users_groups_ID = us.usr_users_groups_ID
WHERE 
	per.`prm_type` = 'menu'
	AND per.`prm_restricted` = 1
";
$sqlb = " AND per.`prm_parent` = 0
GROUP BY per.prm_filename
ORDER BY per.prm_permissions_ID";

$res = $db->query($sqla.$extra.$sqlb);
while ($men = $db->fetch_assoc($res)) {

	//submenus
	$sql = $sqla.$extra." AND per.prm_parent = ".$men["prm_permissions_ID"]." GROUP BY per.prm_filename ORDER BY per.prm_permissions_ID";
	$result = $db->query($sql);

?>
      <tr>
        <td><a href="<? if ($men["prm_filename"] != "") echo $main["site_url"]."/".$men["prm_filename"]; else echo "#"; ?>" <? if ($men["prm_filename"] == "") { ?>onclick="expand_menu(<? echo $men["prm_permissions_ID"];?>,'')" <? } ?>>
		<? if ($db->num_rows($result) >0) { ?>
		<img src="<? echo $db->admin_layout_url;?>images/plus.jpg" border="0" id="image_<? echo $men["prm_permissions_ID"];?>" />
		<? } echo $men["prm_name"];?>
		</a>
		
<?php 
//show submenus
$i=0;
echo "<div 	id=\"div_".$men["prm_permissions_ID"]."\" style=\"display:none\">";
while ($sub = $db->fetch_assoc($result)) {

	if ($i != 0) echo "<br>";
	$i++;
	echo "&nbsp;-<a href=\"".$main["site_url"]."/".$sub["prm_filename"]."\">".$sub["prm_name"]."</a>";
	
	//check if need to expand automatically
	if ($sub["prm_filename"] == substr($_SERVER['PHP_SELF'],strlen($main["remote_folder"])+1)) {
		echo '<script language="JavaScript" type="text/javascript">expand_menu('.$men["prm_permissions_ID"].",'expand');</script>";
	}

}
?>		
		</td>
      </tr>
      <?php 
}//while
echo "</div>";


$sql = "SELECT * FROM `permissions` WHERE `prm_type` = 'menu' AND prm_parent = 0 AND `prm_restricted` = 0";
$res = $db->query($sql);
while ($men = $db->fetch_assoc($res)) {
?>
      <tr>
        <td><a href="<? echo $main["site_url"]."/".$men["prm_filename"];?>"><? echo $men["prm_name"];?></a></td>
      </tr>
      <?php 
}//while
}//if is logged in
?>
    </table></td>
    <td width="10">&nbsp;</td>
  </tr>
</table>
<?php 
}
?>