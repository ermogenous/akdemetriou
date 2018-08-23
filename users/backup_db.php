<?

include("../include/main.php");
include("../include/tables.php");

$db = new Main(1,'UTF-8');
$db->admin_title = "Backup DB";


if ($_POST["action"] == 'backup') {

	$filename = $db->backup_tables($main["db_host"],$main["db_username"],$main["db_password"],$main["db_database"]);
	
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	
	$db->export_file_for_download($contents,$main["admin_title"].'backup db '.date('d-m-Y G-i-s').'.sql');
	
	exit();

}


$db->show_header();
include("users_menu.php");
?>
<br>
<br>
<form action="" method="post">
<input name="action" type="hidden" id="action" value="backup">
<input name="" type="submit" value="Backup DB">
</form>
<?
$db->show_footer();
?>
