<?php
include("../include/main.php");
$db = new Main();
$db->admin_title = "Users Permissions";

//fix the filename if type is menu.
//if at the end there is no / then put it.
if ($_POST["type"] == "folder") {
    if (substr($_POST["filename"], strlen($_POST["filename"]) - 1, 1) != '/')
        $_POST["filename"] = $_POST["filename"] . "/";
}

if ($_POST["action"] == "insert" || $_POST["action"] == "update") {

    $_POST["fld_prm_restricted"] = $db->get_check_value($_POST["fld_prm_restricted"]);
    $_POST["fld_prm_view"] = $db->get_check_value($_POST["fld_prm_view"]);
    $_POST["fld_prm_update"] = $db->get_check_value($_POST["fld_prm_update"]);
    $_POST["fld_prm_insert"] = $db->get_check_value($_POST["fld_prm_insert"]);
    $_POST["fld_prm_delete"] = $db->get_check_value($_POST["fld_prm_delete"]);
    $_POST["fld_prm_extra_1"] = $db->get_check_value($_POST["fld_prm_extra_1"]);
    $_POST["fld_prm_extra_2"] = $db->get_check_value($_POST["fld_prm_extra_2"]);
    $_POST["fld_prm_extra_3"] = $db->get_check_value($_POST["fld_prm_extra_3"]);
    $_POST["fld_prm_extra_4"] = $db->get_check_value($_POST["fld_prm_extra_4"]);
    $_POST["fld_prm_extra_5"] = $db->get_check_value($_POST["fld_prm_extra_5"]);
}

if ($_POST["action"] == "insert") {

    $db->db_tool_insert_row('permissions', $_POST, 'fld_');
    header("Location: permissions.php");
    exit();

} else if ($_POST["action"] == "update") {

    $db->db_tool_update_row('permissions', $_POST, "`prm_permissions_ID` = " . $_POST["lid"], $_POST["lid"], 'fld_');
    header("Location: permissions.php");
    exit();

}

//get data
if ($_GET["lid"] != "") {

    $sql = "SELECT * FROM `permissions` WHERE `prm_permissions_ID` = " . $_GET["lid"];
    $data = $db->query_fetch($sql);

}


$db->show_header();
?>

<div class="container">
    <div class="row">
        <div class="col-lg-3 col-md-3 hidden-xs hidden-sm"></div>
        <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
            <form name="groups" method="post" action="" onsubmit="">
                <div class="alert alert-dark text-center">
                    <b><?php if ($_GET["lid"] == "") echo "Insert"; else echo "Update"; ?>
                        &nbsp;Permission</b>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_name" class="col-sm-4 col-form-label">Name</label>
                    <div class="col-sm-8">
                        <input name="fld_prm_name" type="text" id="fld_prm_name"
                               value="<?php echo $data["prm_name"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_filename" class="col-sm-4 col-form-label">File Name</label>
                    <div class="col-sm-8">
                        <input name="fld_prm_filename" type="text" id="fld_prm_filename"
                               value="<?php echo $data["prm_filename"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_type" class="col-sm-4 col-form-label">Type</label>
                    <div class="col-sm-8">
                        <select name="fld_prm_type" id="fld_prm_type">
                            <option value="menu" <?php if ($data["prm_type"] == "menu") echo "selected=\"selected\""; ?>>
                                Menu
                            </option>
                            <option value="file" <?php if ($data["prm_type"] == "file") echo "selected=\"selected\""; ?>>
                                File
                            </option>
                            <option value="folder" <?php if ($data["prm_type"] == "folder") echo "selected=\"selected\""; ?>>
                                Folder
                            </option>
                            <option value="menu2" <?php if ($data["prm_type"] == "menu2") echo "selected=\"selected\""; ?>>
                                Menu2
                            </option>
                            <option value="menu3" <?php if ($data["prm_type"] == "menu3") echo "selected=\"selected\""; ?>>
                                Menu3
                            </option>
                            <option value="menu4" <?php if ($data["prm_type"] == "menu4") echo "selected=\"selected\""; ?>>
                                Menu4
                            </option>
                            <option value="menu5" <?php if ($data["prm_type"] == "menu5") echo "selected=\"selected\""; ?>>
                                Menu5
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_parent" class="col-sm-4 col-form-label">Parent</label>
                    <div class="col-sm-8">
                        <select name="fld_prm_parent" id="fld_prm_parent">
                            <option value="0" <?php if ($data["prm_parent"] == 0) echo "selected=\"selected\""; ?>>
                                ROOT
                            </option>
                            <?php
                            $sql = "SELECT * FROM `permissions` WHERE `prm_parent` = 0";
                            $result = $db->query($sql);
                            while ($root = $db->fetch_assoc($result)) {
                                ?>
                                <option value="<?php echo $root["prm_permissions_ID"]; ?>" <?php if ($data["prm_parent"] == $root["prm_permissions_ID"]) echo "selected=\"selected\""; ?>><?php echo $root["prm_name"]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_restricted" class="col-sm-4 col-form-label">Restricted </label>
                    <div class="col-sm-8">

                        <input name="fld_prm_restricted" type="checkbox" id="fld_prm_restricted"
                               class="form-check-input"
                               value="1" <?php if ($data["prm_restricted"] == 1) echo "checked=\"checked\""; ?> />
                        To enable restrictions

                    </div>
                </div>

                <div class="alert alert-dark text-center"><strong>Insert Permissions</strong></div>

                <div class="form-group row">
                    <label for="fld_prm_view" class="col-sm-4 col-form-label">View </label>
                    <div class="col-sm-8">

                        <input name="fld_prm_view" type="checkbox" id="fld_prm_view"
                               class="form-check-input"
                               value="1" <?php if ($data["prm_view"] == 1) echo "checked=\"checked\""; ?> />
                        For opening/viewing
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_insert" class="col-sm-4 col-form-label">Insert </label>
                    <div class="col-sm-8">

                        <input name="fld_prm_insert" type="checkbox" id="fld_prm_insert"
                               class="form-check-input"
                               value="1" <?php if ($data["prm_insert"] == 1) echo "checked=\"checked\""; ?> />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_update" class="col-sm-4 col-form-label">Edit </label>
                    <div class="col-sm-8">

                        <input name="fld_prm_update" type="checkbox" id="fld_prm_update"
                               class="form-check-input"
                               value="1" <?php if ($data["prm_update"] == 1) echo "checked=\"checked\""; ?> />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_delete" class="col-sm-4 col-form-label">Delete </label>
                    <div class="col-sm-8">

                        <input name="fld_prm_delete" type="checkbox" id="fld_prm_delete"
                               class="form-check-input"
                               value="1" <?php if ($data["prm_delete"] == 1) echo "checked=\"checked\""; ?> />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_extra_1" class="col-sm-4 col-form-label">Extra 1</label>
                    <div class="col-sm-8">

                        <input name="fld_prm_extra_1" type="checkbox" id="fld_prm_extra_1"
                               class="form-check-input"
                               value="1" <?php if ($data["prm_extra_1"] == 1) echo "checked=\"checked\""; ?> />
                        <input name="fld_prm_extra_name_1" type="text" id="fld_prm_extra_name_1"
                               value="<?php echo $data["prm_extra_name_1"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_extra_2" class="col-sm-4 col-form-label">Extra 2</label>
                    <div class="col-sm-8">

                        <input name="fld_prm_extra_2" type="checkbox" id="fld_prm_extra_2"
                               class="form-check-input"
                               value="1" <?php if ($data["prm_extra_2"] == 1) echo "checked=\"checked\""; ?> />
                        <input name="fld_prm_extra_name_2" type="text" id="fld_prm_extra_name_2"
                               value="<?php echo $data["prm_extra_name_2"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_extra_3" class="col-sm-4 col-form-label">Extra 3</label>
                    <div class="col-sm-8">

                        <input name="fld_prm_extra_3" type="checkbox" id="fld_prm_extra_3"
                               class="form-check-input"
                               value="1" <?php if ($data["prm_extra_3"] == 1) echo "checked=\"checked\""; ?> />
                        <input name="fld_prm_extra_name_3" type="text" id="fld_prm_extra_name_3"
                               value="<?php echo $data["prm_extra_name_3"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_extra_4" class="col-sm-4 col-form-label">Extra 4</label>
                    <div class="col-sm-8">

                        <input name="fld_prm_extra_4" type="checkbox" id="fld_prm_extra_4"
                               class="form-check-input"
                               value="1" <?php if ($data["prm_extra_4"] == 1) echo "checked=\"checked\""; ?> />
                        <input name="fld_prm_extra_name_4" type="text" id="fld_prm_extra_name_4"
                               value="<?php echo $data["prm_extra_name_4"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_extra_5" class="col-sm-4 col-form-label">Extra 5</label>
                    <div class="col-sm-8">

                        <input name="fld_prm_extra_5" type="checkbox" id="fld_prm_extra_5"
                               class="form-check-input"
                               value="1" <?php if ($data["prm_extra_5"] == 1) echo "checked=\"checked\""; ?> />
                        <input name="fld_prm_extra_name_5" type="text" id="fld_prm_extra_name_5"
                               value="<?php echo $data["prm_extra_name_5"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_prm_extra_5" class="col-sm-4 col-form-label"></label>
                    <div class="col-sm-8">
                        <input name="action" type="hidden" id="action"
                               value="<?php if ($_GET["lid"] == "") echo "insert"; else echo "update"; ?>">
                        <input name="lid" type="hidden" id="lid" value="<?php echo $_GET["lid"]; ?>">
                        <input type="submit" name="Submit" value="Save Permission" class="btn btn-secondary">
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
