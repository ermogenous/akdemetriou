<?php
include("../include/main.php");
include("../include/tables.php");
include("codes_array.php");

$db = new Main(1,'UTF-8');
$db->admin_title = "System Codes";


$db->show_header();

if ($_POST["action"] == "show_class") {
	
	$_SESSION["system_codes_class"] = $_POST["class_filter"];

}

if (!empty($_SESSION["system_codes_class"])){

	$table = new draw_table('settings','stg_value','ASC');
	$table->extras = "stg_section = '".$_SESSION["system_codes_class"]."'";
	$table->generate_data();
	$table->show_pages_links();
}


?>
<br />
<form id="form1" name="form1" method="post" action="">
  <table width="450" border="0" align="center" cellpadding="0" cellspacing="0" class="row_table_border">
    <tr>
      <td width="102">&nbsp;&nbsp;System Code</td>
      <td width="348"><select name="class_filter" id="class_filter">
        <option value="">Select</option>
        <option disabled>------------------------------------------</option>
<?php
foreach ($system_classes as $name => $value) {
?>
		<option value="<?php echo $name;?>" <?php if ($name == $_SESSION["system_codes_class"]) echo "selected";?>><?php echo $value;?></option>
<?php
	
}
?>
      </select>
      <input type="submit" name="button" id="button" value="Submit">
      <input name="action" type="hidden" id="action" value="show_class"></td>
    </tr>
  </table>
</form>
<?php 
if (!empty($_SESSION["system_codes_class"])){
?>
<br />
<table width="450" border="0" align="center" cellpadding="0" cellspacing="0" class="row_table_border">
  <tr class="row_table_head">
    <td width="60" align="center"><?php $table->display_order_links('ID','stg_settings_serial');?></td>
    <td width="270" align="left"><?php $table->display_order_links('Name','stg_value');?></td>
    <td colspan="2" align="center"><a href="codes_modify.php">New</a></td>
  </tr>
<?php
$i=0;
while ($row = $table->fetch_data()) {
$i++;
?>
  <tr class="row_table_<?php if ($i%2 == 0) echo "odd"; else echo "even";?>">
    <td align="center" class="main_text"><?php echo $row["stg_settings_serial"];?></td>
    <td align="left" class="main_text"><?php echo $row["stg_value"];?></td>
    <td width="60" align="center" class="main_text"><a href="codes_modify.php?lid=<?php echo $row["stg_settings_serial"];?>">Modify</a></td>
    <td width="60" height="30" align="center" class="main_text"><a href="codes_delete.php?lid=<?php echo $row["stg_settings_serial"];?>" onclick="return(confirm('Are you sure you want to delte this Code?'))">Delete</a></td>
  </tr>
<?php
}
?>
</table>
<?php
}
$db->show_footer();
?>