<?php
class draw_table {

var $table;
var $order_by;
var $order;
var $extras;
var $extra_select_section;
var $extra_from_section;
var $extra_group_section;
var $total_rows;
var $pages_current;
var $pages_total;
var $per_page;
var $sql;
var $holder;
var $type_of_database = 'mysql';
var $select_section = '*';


public function __construct($table_name,$default_order,$default_order_type="ASC") {

	if ($_GET["per_page"] != "" && is_numeric($_GET["per_page"])) {
		$_SESSION["tables_per_page"] = $_GET["per_page"];	
	}
	if ($_SESSION["tables_per_page"] == "") {
		$_SESSION["tables_per_page"] = 25;
	}

	$this->table = $table_name;
	$this->per_page = $_SESSION["tables_per_page"];
	if ($_GET["tables_current"] == "") 
		$this->pages_current = 1;
	else
		$this->pages_current = $_GET["tables_current"];
	
	if ($_GET["tables_order"] == "") {
		$this->order = $default_order;
		$this->order_by = $default_order_type;
	}
	else {
		$this->order = $_GET["tables_order"];
		$this->order_by = $_GET["tables_order_type"];
	}
}

public function generate_data() {
	
	if ($this->type_of_database == 'mysql') {
		$this->generate_data_mysql();
		}
	else if($this->type_of_database == 'sybase') {
		$this->generate_data_sybase();
		}

}

public function generate_data_mysql() {
global $main,$db;	

	$sql = "SELECT ".$this->select_section." ".$this->extra_select_section." FROM ".$this->table." ".$this->extra_from_section;
	if ($this->extras != "") {
		$sql .= " WHERE ".$this->extras;
	}
	if ($this->extra_group_section != "") {
		$sql .= " GROUP BY ".$this->extra_group_section;
	}
	
	if ($this->order != "") {
		$sql .= " ORDER BY ".$this->order." ".$this->order_by." ";
	}
	$this->sql = $sql;
	
	$result = $db->query($sql);
	$this->total_rows = $db->num_rows($result);
	
	$this->pages_total = ceil($this->total_rows / $this->per_page);
	
	$this->sql .= "LIMIT ".(($this->pages_current -1) * $this->per_page).", ".$this->per_page;
	
	$this->holder = $db->query($this->sql);
	

}//generate_data

public function generate_data_sybase() {
global $main,$sybase;	

	$sql = "SELECT COUNT()as clo_num_rows FROM ".$this->table."";
	if ($this->extras != "") {
		$sql .= " WHERE ".$this->extras;
	}
		if ($this->extra_group_section != "") {
		$sql .= " GROUP BY ".$this->extra_group_section;
	}

	$this->sql = $sql;
	
	$result = $sybase->query_fetch($sql);
	$this->total_rows = $result["clo_num_rows"];
	
	$this->pages_total = ceil($this->total_rows / $this->per_page);

	//fix sql to be able to use LIMIT actions
	$sql = "SELECT identity(9) as rownum ".$this->extra_select_section." INTO #temp FROM ".$this->table." ".$this->extra_from_section;
	if ($this->extras != "") {
		$sql .= " WHERE ".$this->extras;
	}
	if ($this->extra_group_section != "") {
		$sql .= " GROUP BY ".$this->extra_group_section;
	}
	if ($this->order != "") {
		$sql .= " ORDER BY ".$this->order." ".$this->order_by." ";
	}

	$sql .= "SELECT * FROM #temp WHERE rownum BETWEEN ".((($this->pages_current -1) * $this->per_page)+1)." AND ".((($this->pages_current -1) * $this->per_page) + $this->per_page);
	
	$this->sql = $sql;
	$this->holder = $sybase->query($this->sql);
	

}//generate_data

public function fetch_data() {
global $db,$sybase;
if ($this->type_of_database == 'mysql') {
	return $db->fetch_assoc($this->holder);
}
else if ($this->type_of_database == 'sybase') {
	return $sybase->fetch_assoc($this->holder);
}


}//fetch data

public function show_pages_links() {

	echo "<div align=\"center\">";
		
		echo $this->total_rows." Results. Page ".$this->pages_current." OF ".$this->pages_total;
		echo " Goto Page - ";
		
		if ($this->pages_current != 1) 
			echo "<a href=\"?tables_current=".($this->pages_current - 1)."&tables_order=".$this->order."&tables_order_type=".$this->order_by."\"><<<</a>&nbsp;&nbsp;";
		
		if ($this->pages_total < 11) {
			for($i=1; $i<=$this->pages_total;$i++){
				if ($i != $this->pages_current) 
					echo "<a href=\"?tables_current=".$i."&tables_order=".$this->order."&tables_order_type=".$this->order_by."\">".$i."</a>&nbsp;&nbsp;";			
				else
					echo $i."&nbsp;&nbsp;";			
			}
		}// <6
		else {

			for($i=1; $i<=5;$i++){
				echo "<a href=\"?tables_current=".$i."&tables_order=".$this->order."&tables_order_type=".$this->order_by."\">".$i."</a>&nbsp;&nbsp;";
			}
			echo " - ";
			
			for ($i = $this->pages_current - 1; $i <= $this->pages_current + 1;$i++) {
				if ($i > 5 && $i < $this->pages_total-1) {
					echo "<a href=\"?tables_current=".$i."&tables_order=".$this->order."&tables_order_type=".$this->order_by."\">".$i."</a>&nbsp;&nbsp;";
					
				}
			}
			echo " - ";
			for ($i = $this->pages_total - 1; $i <= $this->pages_total;$i++) {
			
					echo "<a href=\"?tables_current=".$i."&tables_order=".$this->order."&tables_order_type=".$this->order_by."\">".$i."</a>&nbsp;&nbsp;";
			
			}
			
			if ($this->pages_current != $this->pages_total)
				echo "<a href=\"?tables_current=".($this->pages_current + 1)."&tables_order=".$this->order."&tables_order_type=".$this->order_by."\">>>></a>&nbsp;&nbsp;";
				
			
		} 
			
		
		
	echo "</div>";

}//show pages links

public function display_order_links($name,$value,$extras="") {

	echo "<a href=\"?tables_current=".$this->pages_current."&tables_order=".$value."&tables_order_type=";
	if ($this->order_by == "ASC") 
		echo "DESC";
	else
		echo "ASC";
	
	echo "&".$extras."\">".$name."</a>";

}

public function change_type_of_database($new_database) {

	$this->type_of_database = $new_database;

}

public function show_per_page_links($extra=0) {

	$return = "<a href=\"?per_page=25\">25</a>";
	$return .= "&nbsp;&nbsp;<a href=\"?per_page=50\">50</a>";
	$return .= "&nbsp;&nbsp;<a href=\"?per_page=75\">75</a>";
	$return .= "&nbsp;&nbsp;<a href=\"?per_page=100\">100</a>";
	if ($extra > 0) {
		$return .= "&nbsp;&nbsp;<a href=\"?per_page=200\">200</a>";
	}
	
	return $return;
}

}




?>