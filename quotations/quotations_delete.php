<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 12/4/2018
 * Time: 11:26 ΜΜ
 */
include("../include/main.php");
$db = new Main();


if ($_GET["lid"] != "") {

    $db->db_tool_delete_row('quotations',$_GET["lid"],"`quotations_id` = ".$_GET["lid"]);
    header("Location: quotations.php?alert-success=Quotation Deleted Successfully");
    exit();

}
else {
    header ("Location: quotations.php");
    exit();
}

?>