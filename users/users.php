<?php
include("../include/main.php");
include("../include/tables.php");

$db = new Main(1, 'UTF-8');
$db->admin_title = "Users";

$db->show_header();

if ($_POST["action"] == 'search') {

    $_SESSION["users_name"] = $_POST["srh_name"];
    $_SESSION["users_group"] = $_POST["srh_group"];
    $_SESSION["users_rights"] = $_POST["srh_rights"];

    if ($_SESSION["users_name"] != "") {

        $search_sql = " AND (`usr_name` LIKE '%" . $_SESSION["users_name"] . "%' OR usr_username LIKE '%" . $_SESSION["users_name"] . "%' OR usr_agent_code LIKE '%" . $_SESSION["users_name"] . "%')";

    }//name
    if ($_SESSION["users_group"] != "") {

        $search_sql .= " AND `usr_users_groups_ID` = " . $_SESSION["users_group"];

    }//group
    if ($_SESSION["users_rights"] != "none") {

        $search_sql .= " AND `usr_user_rights` = '" . $_SESSION["users_rights"] . "'";

    }//group


}


$table = new draw_table('users', 'usr_users_ID', 'ASC');
$table->extra_from_section = "LEFT OUTER JOIN users_groups ON usg_users_groups_ID = usr_users_groups_ID";
//$table->extra_select_section = ",hotel_class.stg_value as clo_hotel_class";

$table->extras = "1=1 " . $search_sql;

$table->per_page = 100;
$table->generate_data();
?>
<br/>

<div class="container">
    <form class="form-inline justify-content-center">

            <input name="srh_name"
                   id="srh_name"
                   type="text"
                   class="form-control mb-2 mr-sm-2"
                   value="<?php echo $_SESSION["users_name"]; ?>"
                   placeholder="Name">


            <select name="srh_group" id="srh_group" class="form-control mb-2 mr-sm-2" placeholder="Group">
                <option value="">All Groups</option>
                <?php
                $sql = "SELECT * FROM `users_groups` ORDER BY `usg_group_name` ASC";
                $res = $db->query($sql);
                while ($grp = $db->fetch_assoc($res)) {
                    ?>
                    <option value="<?php echo $grp["usg_users_groups_ID"]; ?>" <?php if ($_SESSION["users_group"] == $grp["usg_users_groups_ID"]) echo "selected=\"selected\""; ?>><?php echo $grp["usg_group_name"] . " [" . $grp["usg_users_groups_ID"] . "]"; ?></option>
                <?php } ?>
            </select>


            <select name="srh_rights" id="srh_rights" class="form-control mb-2 mr-sm-2">
                <option value="none" <?php if ($_SESSION["users_rights"] == 'none') echo "selected=\"selected\""; ?>>
                    ALL Rights
                </option>
                <option value="0" <?php if ($_SESSION["users_rights"] == '0') echo "selected=\"selected\""; ?>>
                    Administrator
                </option>
                <option value="1" <?php if ($_SESSION["users_rights"] == 1) echo "selected=\"selected\""; ?>>Super
                    Users
                </option>
                <option value="2" <?php if ($_SESSION["users_rights"] == 2) echo "selected=\"selected\""; ?>>
                    Advanced Users
                </option>
                <option value="3" <?php if ($_SESSION["users_rights"] == 3) echo "selected=\"selected\""; ?>>Normal
                    Users
                </option>
                <option value="4" <?php if ($_SESSION["users_rights"] == 4) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_1_name"); ?></option>
                <option value="5" <?php if ($_SESSION["users_rights"] == 5) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_2_name"); ?></option>
                <option value="6" <?php if ($_SESSION["users_rights"] == 6) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_3_name"); ?></option>
                <option value="7" <?php if ($_SESSION["users_rights"] == 7) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_4_name"); ?></option>
                <option value="8" <?php if ($_SESSION["users_rights"] == 8) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_5_name"); ?></option>
                <option value="9" <?php if ($_SESSION["users_rights"] == 9) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_6_name"); ?></option>
            </select>


            <input name="action" type="hidden" id="action" value="search"/>
            <input type="submit" name="Submit" value="Search" class="form-control mb-2 mr-sm-2"/>

    </form>
</div>

<br/>

<div class="container">
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
            <div class="text-center"><?php $table->show_pages_links(); ?></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col"><?php $table->display_order_links('ID', 'usr_users_ID'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Name', 'usr_name'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Rights', 'usr_user_rights'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Group', 'usg_group_name'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Active', 'usr_active'); ?></th>
                        <th scope="col">
                            <a href="users_modify.php">
                                <i class="fas fa-plus-circle"></i>
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $table->fetch_data()) {
                        ?>
                        <tr <?php if($row["usr_active"] == 0) echo 'class="alert alert-danger"';?>>
                            <th scope="row"><?php echo $row["usr_users_ID"]; ?></th>
                            <td><?php echo $row["usr_name"]; ?></td>
                            <td><?php echo $row["usr_user_rights"]; ?></td>
                            <td><?php echo $row["usg_group_name"]; ?></td>
                            <td><?php echo $row["usr_active"]; ?></td>
                            <td>
                                <a href="users_modify.php?lid=<?php echo $row["usr_users_ID"];?>"><i class="fas fa-edit"></i></a>&nbsp
                                <a href="users_delete.php?lid=<?php echo $row["usr_users_ID"];?>"
                                   onclick="return confirm('Are you sure you want to delete this group?');"><i class="fas fa-minus-circle"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>

<?php
$db->show_footer();
?>
