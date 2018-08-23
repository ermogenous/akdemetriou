<?php
include("../include/main.php");
include("../include/tables.php");

$db = new Main();
$db->admin_title = "My Cover Notes";

$db->show_header();
include("menu.php");

$table = new draw_table('users_groups');




$db->show_header();
//include("menu.php");
?>



<?php
$db->show_footer();
?>