<?php
include("../include/main.php");
$db = new Main();
$db->admin_title = "Users Groups";

//if user Rights = 0
//if ($db->user_data["user_rights"] == 1 && $_GET["lid"] == "") {
//	header("Location: users_modify.php?lid=".$db->user_data["users_ID"]);
//	exit();
//}

function fix_lines($users_groups_ID)
{
    global $main, $db;

    $result = $db->query("SELECT * FROM `permissions` WHERE `prm_restricted` = 1 ORDER BY `prm_type` ASC");

    while ($row = $db->fetch_assoc($result)) {

        //check if exists in db
        $sql = "SELECT * FROM `permissions_lines` WHERE `prl_permissions_ID` = " . $row["prm_permissions_ID"] . " AND `prl_users_groups_ID` = " . $users_groups_ID;
        $res = $db->query($sql);

        //prepare the array
        $array["fld_prl_permissions_ID"] = $row["prm_permissions_ID"];
        $array["fld_prl_users_groups_ID"] = $users_groups_ID;
        $array["fld_prl_view"] = $db->get_check_value($_POST["lines_" . $row["prm_permissions_ID"] . "_view"]);
        $array["fld_prl_insert"] = $db->get_check_value($_POST["lines_" . $row["prm_permissions_ID"] . "_insert"]);
        $array["fld_prl_update"] = $db->get_check_value($_POST["lines_" . $row["prm_permissions_ID"] . "_update"]);
        $array["fld_prl_delete"] = $db->get_check_value($_POST["lines_" . $row["prm_permissions_ID"] . "_delete"]);
        $array["fld_prl_extra_1"] = $db->get_check_value($_POST["lines_" . $row["prm_permissions_ID"] . "_extra_1"]);
        $array["fld_prl_extra_2"] = $db->get_check_value($_POST["lines_" . $row["prm_permissions_ID"] . "_extra_2"]);
        $array["fld_prl_extra_3"] = $db->get_check_value($_POST["lines_" . $row["prm_permissions_ID"] . "_extra_3"]);
        $array["fld_prl_extra_4"] = $db->get_check_value($_POST["lines_" . $row["prm_permissions_ID"] . "_extra_4"]);
        $array["fld_prl_extra_5"] = $db->get_check_value($_POST["lines_" . $row["prm_permissions_ID"] . "_extra_5"]);

        if ($db->num_rows($res) > 0) {
            //update
            $data = $db->fetch_assoc($res);
            $db->db_tool_update_row('permissions_lines', $array, "`prl_permissions_lines_ID` = " . $data["prl_permissions_lines_ID"], $data["prl_permissions_lines_ID"], 'fld_');

        } else {
            //insert
            $data = $db->fetch_assoc($res);
            $sql = $db->db_tool_insert_row('permissions_lines', $array, 'fld_', 1);
        }


    }//while


}//function fix_lines

if ($db->user_data["usr_user_rights"] > 0) {

    header("Location: groups.php");
    exit();

}

if ($_POST["action"] == "insert") {

//find the permissions
    $sql = "SELECT * FROM `permissions` WHERE `prm_restricted` = 1 ORDER BY `prm_permissions_ID` ASC";
    $result = $db->query($sql);
    while ($row = $db->fetch_assoc($result)) {
        if ($_POST["per_" . $row["prm_permissions_ID"]] == 1) {
            $permissions .= "|" . $row["prm_permissions_ID"] . "|";
        }
    }

    $group_array["fld_usg_group_name"] = $_POST["name"];
    $group_array["fld_usg_restrict_ip"] = $_POST["restrict_ip"];
    $group_array["fld_usg_permissions"] = $permissions;
    $group_array["fld_usg_approvals"] = $_POST["approvals"];
    $db->db_tool_insert_row('users_groups', $group_array, 'fld_');
    fix_lines($db->insert_id());
    header("Location: groups.php");
    exit();

} else if ($_POST["action"] == "update") {
//find the permissions


    $sql = "SELECT * FROM `permissions` ORDER BY `prm_permissions_ID` ASC";
    $result = $db->query($sql);
    while ($row = $db->fetch_assoc($result)) {
        if ($_POST["per_" . $row["prm_permissions_ID"]] == 1) {
            $permissions .= "|" . $row["prm_permissions_ID"] . "|";
        }
    }

    $group_array["fld_usg_group_name"] = $_POST["name"];
    $group_array["fld_usg_restrict_ip"] = $_POST["restrict_ip"];
    $group_array["fld_usg_permissions"] = $permissions;
    $group_array["fld_usg_approvals"] = $_POST["approvals"];

    $db->db_tool_update_row('users_groups', $group_array, "`usg_users_groups_ID` = " . $_POST["lid"], $_POST["lid"], 'fld_');
    fix_lines($_POST["lid"]);

    header("Location: groups.php");
    exit();


}


if ($_GET["lid"] != "") {

    $sql = "SELECT * FROM `users_groups` WHERE `usg_users_groups_ID` = " . $_GET["lid"];
    $data = $db->query_fetch($sql);
    $permissions = explode("||", $data["usg_permissions"]);
}


$db->show_header();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-3 col-md-3 hidden-xs hidden-sm"></div>
        <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
            <form name="groups" method="post" action="" onsubmit="">
                <?php
                $result = $db->query("SELECT * FROM `permissions` WHERE `prm_restricted` = 1 ORDER BY `prm_type` ASC");
                $total = $db->num_rows($result);
                ?>
                <div class="alert alert-dark text-center">
                    <b><?php if ($_GET["lid"] == "") echo "Insert"; else echo "Update"; ?>
                        &nbsp;Users Group</b>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-sm-4 col-form-label">Name</label>
                    <div class="col-sm-8">
                        <input name="name" type="text" id="name"
                               class="form-control"
                               value="<?php echo $data["usg_group_name"]; ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="restrict_ip" class="col-sm-4 col-form-label">Restrict IP</label>
                    <div class="col-sm-8">
                        <input name="restrict_ip" type="text" id="restrict_ip"
                               value="<?php echo $data["usg_restrict_ip"]; ?>"
                               class="form-control"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="approvals" class="col-sm-4 col-form-label">Approvals</label>
                    <div class="col-sm-8">
                        <select name="approvals" id="approvals"
                                class="form-control"
                                required>
                            <option value="NO" <?php if ($data['usg_approvals'] == '' || $data['usg_approvals'] == 'NO') echo 'selected';?>>No approvals</option>
                            <option value="REQUEST" <?php if ($data['usg_approvals'] == 'REQUEST') echo 'selected';?>>Can request Approvals</option>
                            <option value="ANSWER" <?php if ($data['usg_approvals'] == 'ANSWER') echo 'selected';?>>Can Answer Approvals</option>
                        </select>
                    </div>
                </div>

                <?php
                while ($row = $db->fetch_assoc($result)) {
                    //get the data dor the line if the users group exists
                    if ($_GET["lid"] != "") {
                        $line = $db->query_fetch("SELECT * FROM `permissions_lines` WHERE `prl_permissions_ID` = " . $row["prm_permissions_ID"] . " AND `prl_users_groups_ID` = " . $_GET["lid"]);
                    }
                    if ($row["prm_type"] == 'menu')
                        $font = 'text-danger';
                    else if ($row["prm_type"] == 'folder')
                        $font = 'text-info';
                    else if ($row["prm_type"] == 'file')
                        $font = 'text-success';
                    ?>


                    <div class="form-group row">
                        <label for="name" class="col-sm-4 col-form-label <?php echo $font;?>">[<?php echo $row["prm_type"];?>] <?php echo $row["prm_name"];?></label>
                        <div class="col-sm-8">
                            <?php
                            if ($row["prm_view"] == 1) {

                                echo "View <input name=\"lines_" . $row["prm_permissions_ID"] . "_view\" id=\"lines_" . $row["prm_permissions_ID"] . "\" type=\"checkbox\" value=\"1\"";
                                if ($line["prl_view"] == 1) echo "checked=\"checked\"";
                                echo " />&nbsp;";

                            }
                            if ($row["prm_insert"] == 1) {
                                echo "Insert <input name=\"lines_" . $row["prm_permissions_ID"] . "_insert\" id=\"lines_" . $row["prm_permissions_ID"] . "\" type=\"checkbox\" value=\"1\"";
                                if ($line["prl_insert"] == 1) echo "checked=\"checked\"";
                                echo " />&nbsp;";

                            }
                            if ($row["prm_update"] == 1) {
                                echo "Edit <input name=\"lines_" . $row["prm_permissions_ID"] . "_update\" id=\"lines_" . $row["prm_permissions_ID"] . "\" type=\"checkbox\" value=\"1\"";
                                if ($line["prl_update"] == 1) echo "checked=\"checked\"";
                                echo " />&nbsp;";

                            }
                            if ($row["prm_delete"] == 1) {
                                echo "Delete <input name=\"lines_" . $row["prm_permissions_ID"] . "_delete\" id=\"lines_" . $row["prm_permissions_ID"] . "\" type=\"checkbox\" value=\"1\"";
                                if ($line["prl_delete"] == 1) echo "checked=\"checked\"";
                                echo " />&nbsp;";

                            }
                            if ($row["prm_extra_1"] == 1) {
                                echo $row["prm_extra_name_1"] . "<input name=\"lines_" . $row["prm_permissions_ID"] . "_extra_1\" id=\"lines_" . $row["prm_permissions_ID"] . "\" type=\"checkbox\" value=\"1\"";
                                if ($line["prl_extra_1"] == 1) echo "checked=\"checked\"";
                                echo " />&nbsp;";

                            }
                            if ($row["prm_extra_2"] == 1) {
                                echo $row["extra_name_2"] . "<input name=\"lines_" . $row["permissions_ID"] . "_extra_2\" id=\"lines_" . $row["permissions_ID"] . "\" type=\"checkbox\" value=\"1\"";
                                if ($line["prl_extra_2"] == 1) echo "checked=\"checked\"";
                                echo " />&nbsp;";

                            }
                            if ($row["prm_extra_3"] == 1) {
                                echo $row["prm_extra_name_3"] . "<input name=\"lines_" . $row["prm_permissions_ID"] . "_extra_3\" id=\"lines_" . $row["prm_permissions_ID"] . "\" type=\"checkbox\" value=\"1\"";
                                if ($line["prl_extra_3"] == 1) echo "checked=\"checked\"";
                                echo " />&nbsp;";

                            }
                            if ($row["prm_extra_4"] == 1) {
                                echo $row["prm_extra_name_4"] . "<input name=\"lines_" . $row["prm_permissions_ID"] . "_extra_4\" id=\"lines_" . $row["prm_permissions_ID"] . "\" type=\"checkbox\" value=\"1\"";
                                if ($line["prl_extra_4"] == 1) echo "checked=\"checked\"";
                                echo " />&nbsp;";

                            }
                            if ($row["prm_extra_5"] == 1) {
                                echo $row["prm_extra_name_5"] . "<input name=\"lines_" . $row["prm_permissions_ID"] . "_extra_5\" id=\"lines_" . $row["prm_permissions_ID"] . "\" type=\"checkbox\" value=\"1\"";
                                if ($line["prl_extra_5"] == 1) echo "checked=\"checked\"";
                                echo " />&nbsp;";

                            }
                            ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="form-group row">
                    <label for="name" class="col-sm-4 col-form-label"></label>
                    <div class="col-sm-8">
                        <input name="action" type="hidden" id="action"
                               value="<?php if ($_GET["lid"] == "") echo "insert"; else echo "update"; ?>">
                        <input name="lid" type="hidden" id="lid" value="<?php echo $_GET["lid"]; ?>">
                        <input type="submit" name="Submit" value="Save Users Group" class="btn btn-secondary">
                    </div>
                </div>

            </form>
        </div>
        <div class="col-lg-3 col-md-3 hidden-xs hidden-sm"></div>
    </div>
</div>

<?php
$db->show_footer();
?>
