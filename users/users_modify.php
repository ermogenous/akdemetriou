<?php
include("../include/main.php");
$db = new Main();
//$db->system_on_test = 'yes';
$db->admin_title = "Users Groups";

//if user Rights = 0
//if ($db->user_data["user_rights"] == 1 && $_GET["lid"] == "") {
//	header("Location: users_modify.php?lid=".$db->user_data["users_ID"]);
//	exit();
//}
if ($_POST["action"] == "insert") {
    $db->check_restriction_area('insert');
    $_POST["fld_usr_is_agent"] = $db->get_check_value($_POST["fld_usr_is_agent"]);
    $_POST["fld_usr_active"] = $db->get_check_value($_POST["fld_usr_active"]);

    $db->db_tool_insert_row('users', $_POST, 'fld_');
    header("Location: users.php");
    exit();

} else if ($_POST["action"] == "update") {
    $db->check_restriction_area('update');
    $_POST["fld_usr_is_agent"] = $db->get_check_value($_POST["fld_usr_is_agent"]);
    $_POST["fld_usr_active"] = $db->get_check_value($_POST["fld_usr_active"]);

    $db->db_tool_update_row('users', $_POST, "`usr_users_ID` = " . $_POST["lid"], $_POST["lid"], 'fld_');
    header("Location: users.php");
    exit();

}


if ($_GET["lid"] != "") {

    $sql = "SELECT * FROM `users` WHERE `usr_users_ID` = " . $_GET["lid"];
    $data = $db->query_fetch($sql);
}
else {
    $data["usr_active"] = 1;
}


$db->show_header();
?>


<div class="container">
    <div class="row">
        <div class="col-lg-3 col-md-3 hidden-xs hidden-sm"></div>
        <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
            <form name="groups" method="post" action="" onSubmit="" class="justify-content-center">

                <div class="alert alert-dark text-center"><b><?php if ($_GET["lid"] == "") echo "Insert"; else echo "Update"; ?>
                    &nbsp;Users</b>
                </div>

                <div class="form-group row">
                    <div class="col"></div>
                    <div class="col">
                        <input name="fld_usr_active"
                               type="checkbox"
                               class="form-check-input"
                               id="fld_usr_active"
                               value="1" <?php if ($data["usr_active"] == 1) echo "checked=\"checked\""; ?> />
                        <label for="fld_usr_active" class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_name" class="col-sm-4 col-form-label">Name</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_name"
                               type="text" id="fld_usr_name"
                               class="form-control"
                               value="<?php echo $data["usr_name"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_name_gr" class="col-sm-4 col-form-label">Name GR</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_name_gr"
                               type="text" id="fld_usr_name_gr"
                               class="form-control"
                               value="<?php echo $data["usr_name_gr"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_name_en" class="col-sm-4 col-form-label">Name EN</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_name_en"
                               type="text" id="fld_usr_name_en"
                               class="form-control"
                               value="<?php echo $data["usr_name_en"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_description" class="col-sm-4 col-form-label">Description</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_description"
                               type="text" id="fld_usr_description"
                               class="form-control"
                               value="<?php echo $data["usr_description"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_username" class="col-sm-4 col-form-label">Username</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_username" type="text" id="fld_usr_username"
                               class="form-control"
                               value="<?php echo $data["usr_username"]; ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_password" class="col-sm-4 col-form-label">Password</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_password" type="text" id="fld_usr_password"
                               class="form-control"
                               value="<?php echo $data["usr_password"]; ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_restrict_ip" class="col-sm-4 col-form-label">Restrict IP</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_restrict_ip" type="text" id="fld_usr_restrict_ip"
                               class="form-control"
                               value="<?php echo $data["usr_restrict_ip"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_user_rights" class="col-sm-4 col-form-label">User Rights</label>
                    <div class="col-sm-8">
                        <select name="fld_usr_user_rights" id="fld_usr_user_rights" class="form-control">
                            <option value="0" <?php if ($data["usr_user_rights"] == 0) echo "selected=\"selected\""; ?>>
                                Administrator
                            </option>
                            <option value="1" <?php if ($data["usr_user_rights"] == 1) echo "selected=\"selected\""; ?>>
                                Semi Admin
                            </option>
                            <option value="2" <?php if ($data["usr_user_rights"] == 2) echo "selected=\"selected\""; ?>>
                                Advanced Users
                            </option>
                            <option value="3" <?php if ($data["usr_user_rights"] == 3) echo "selected=\"selected\""; ?>>
                                Normal Users
                            </option>
                            <option value="4" <?php if ($data["usr_user_rights"] == 4) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_1_name"); ?></option>
                            <option value="5" <?php if ($data["usr_user_rights"] == 5) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_2_name"); ?></option>
                            <option value="6" <?php if ($data["usr_user_rights"] == 6) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_3_name"); ?></option>
                            <option value="7" <?php if ($data["usr_user_rights"] == 7) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_4_name"); ?></option>
                            <option value="8" <?php if ($data["usr_user_rights"] == 8) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_5_name"); ?></option>
                            <option value="9" <?php if ($data["usr_user_rights"] == 9) echo "selected=\"selected\""; ?>><?php echo $db->get_setting("user_levels_extra_6_name"); ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_users_groups_ID" class="col-sm-4 col-form-label">Group</label>
                    <div class="col-sm-8">
                        <select name="fld_usr_users_groups_ID" id="fld_usr_users_groups_ID" class="form-control">
                            <option value=""></option>
                            <?php
                            $res = $db->query("SELECT * FROM `users_groups`");
                            while ($ug = $db->fetch_assoc($res)) {
                                ?>
                                <option value="<?php echo $ug["usg_users_groups_ID"]; ?>" <?php if ($data["usr_users_groups_ID"] == $ug["usg_users_groups_ID"]) echo "selected=\"selected\""; ?>><?php echo $ug["usg_group_name"]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col"></div>
                    <div class="col">
                        <input name="fld_usr_is_agent" type="checkbox" id="fld_usr_is_agent"
                               class="form-check-input"
                               value="1" <?php if ($data["usr_is_agent"] == 1) echo "checked=\"checked\""; ?> />
                        <label for="fld_usr_is_agent" class="form-check-label">Is Agent</label>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_agent_level" class="col-sm-4 col-form-label">Agent Level</label>
                    <div class="col-sm-8">
                        <select name="fld_usr_agent_level" id="fld_usr_agent_level" class="form-control">
                            <?php for ($i = 1; $i <= 10; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if ($data["usr_agent_level"] == $i) echo "selected=\"selected\""; ?>><?php echo $i; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_agent_code" class="col-sm-4 col-form-label">Agent Code</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_agent_code" type="text" id="fld_usr_agent_code"
                               class="form-control"
                               value="<?php echo $data["usr_agent_code"]; ?>"/>
                    </div>
                </div>
                <!--
                <div class="form-group row">
                    <label for="fld_usr_sub_agent_code" class="col-sm-4 col-form-label">Sub Agent Code</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_sub_agent_code" type="text" id="fld_usr_sub_agent_code"
                               class="form-control"
                               value="<?php echo $data["usr_sub_agent_code"]; ?>"/>
                    </div>
                </div>


                <div class="form-group row">
                    <label for="fld_usr_issuing_office_serial" class="col-sm-4 col-form-label">Issuing Office</label>
                    <div class="col-sm-8">
                        <select name="fld_usr_issuing_office_serial" id="fld_usr_issuing_office_serial" class="form-control">
                            <option value="0" <?php if ($data["usr_issuing_office_serial"] == '0') echo "selected=\"selected\""; ?>>Issuing Office/Administrator</option>
                            <?php
                $res = $db->query("SELECT * FROM `users` WHERE usr_user_rights IN (1,4) ");
                while ($issuing = $db->fetch_assoc($res)) {
                    ?>
                                <option value="<?php echo $issuing["usr_users_ID"]; ?>" <?php if ($data["usr_issuing_office_serial"] == $issuing["usr_users_ID"]) echo "selected=\"selected\""; ?>><?php echo $issuing["usr_name"]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                -->

                <div class="form-group row">
                    <label for="fld_usr_email" class="col-sm-4 col-form-label">Email</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_email" type="email" id="fld_usr_email"
                               class="form-control"
                               value="<?php echo $data["usr_email"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_email2" class="col-sm-4 col-form-label">Email 2</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_email2" type="email" id="fld_usr_email2"
                               class="form-control"
                               value="<?php echo $data["usr_email2"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_emailcc" class="col-sm-4 col-form-label">Email CC</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_emailcc" type="email" id="fld_usr_emailcc"
                               class="form-control"
                               value="<?php echo $data["usr_emailcc"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_emailbcc" class="col-sm-4 col-form-label">Email Bcc</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_emailbcc" type="email" id="fld_usr_emailbcc"
                               class="form-control"
                               value="<?php echo $data["usr_emailbcc"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_tel" class="col-sm-4 col-form-label">Tel</label>
                    <div class="col-sm-8">
                        <input name="fld_usr_tel" type="text" id="fld_usr_tel"
                               class="form-control"
                               value="<?php echo $data["usr_tel"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_signature_gr" class="col-sm-4 col-form-label">Signature Gr</label>
                    <div class="col-sm-8">
                        <textarea name="fld_usr_signature_gr" id="fld_usr_signature_gr"
                                  class="form-control"><?php echo $data["usr_signature_gr"]; ?></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_usr_signature_en" class="col-sm-4 col-form-label">Signature En</label>
                    <div class="col-sm-8">
                        <textarea name="fld_usr_signature_en" id="fld_usr_signature_en"
                                  class="form-control"><?php echo $data["usr_signature_en"]; ?></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="" class="col-sm-4 col-form-label"></label>
                    <div class="col-sm-8">
                        <input name="action" type="hidden" id="action"
                               value="<?php if ($_GET["lid"] == "") echo "insert"; else echo "update"; ?>">
                        <input name="lid" type="hidden" id="lid" value="<?php echo $_GET["lid"]; ?>">
                        <input type="submit" name="Submit" value=" Save User " class="btn btn-secondary">
                    </div>
                </div>


                <div class="form-group row">
                    <label class="col-sm-4 col-form-label">Restrict IP</label>
                    <div class="col-sm-8">
                        % -&gt; Gives access to all IP`s<br/>
                        !% Blocks All IP`s<br/>
                        Type an ip to allow or more separated by ,<br/>
                        Type an ip with ! to block<br/>
                        Example 192.168.1.164,192.168.1.165 Allow this IP`s<br/>
                        !192.168.1.165 Block<br/>
                        %,!81.21.35.457 Allow all except the ip<br/>
                        Use wild cards. Allow 192.168.1.???<br/>
                        Block !192.168.1.???<br/>
                        If users restrict IP is left empty then the restrict IP from the group is used.
                    </div>
                </div>
            </form>
        </div>
        <div class="col-lg-3 col-md-3 hidden-xs hidden-sm"></div>
    </div>
</div>
<!--





    <td height="26"><strong>Email2 </strong></td>
    <td height="26"><input name="fld_usr_email2" type="text" id="fld_usr_email2" value="<?php echo $data["usr_email2"]; ?>" size="50"/></td>
  </tr>
  <tr>
    <td height="26"><strong>Email CC </strong></td>
    <td height="26"><input name="fld_usr_emailcc" type="text" id="fld_usr_emailcc" value="<?php echo $data["usr_emailcc"]; ?>" size="50"/></td>
  </tr>
  <tr>
    <td height="26"><strong>Email Bcc </strong></td>
    <td height="26"><input name="fld_usr_emailbcc" type="text" id="fld_usr_emailbcc" value="<?php echo $data["usr_emailbcc"]; ?>" size="50"/></td>
  </tr>
  <tr>
    <td height="26"><strong>Tel</strong></td>
    <td height="26"><input name="fld_usr_tel" type="text" id="fld_usr_tel" value="<?php echo $data["usr_tel"]; ?>" size="50"/></td>
  </tr>
  <tr>
    <td height="26">&nbsp;</td>
    <td height="26">&nbsp;</td>
  </tr>
  <tr>
    <td height="26"><strong>Signature GR</strong></td>
    <td height="26"><textarea name="fld_usr_signature_gr" id="fld_usr_signature_gr" cols="45" rows="5"><?php echo $data["usr_signature_gr"]; ?></textarea></td>
  </tr>
  <tr>
    <td height="26"><strong>Signature EN</strong></td>
    <td height="26"><textarea name="fld_usr_signature_en" id="fld_usr_signature_en" cols="45" rows="5"><?php echo $data["usr_signature_en"]; ?></textarea></td>
  </tr>
  <tr>
    <td height="26">&nbsp;</td>
    <td height="26">&nbsp;</td>
  </tr>
  <tr>
    <td height="26"><input name="action" type="hidden" id="action" value="<?php if ($_GET["lid"] == "") echo "insert"; else echo "update"; ?>">
      <input name="lid" type="hidden" id="lid" value="<?php echo $_GET["lid"]; ?>"></td>
    <td height="26"><input type="submit" name="Submit" value="Submit"></td>
  </tr>
</table>

<br />
<table width="450" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#000000">
  <tr>
    <td colspan="2" align="center"><strong>HELP</strong></td>
    </tr>
  <tr>
    <td width="107">Restrict IP </td>
    <td width="343">% -&gt; Gives access to all IP`s<br />
      !% Blocks All IP`s<br />
      Type an ip to allow or more separated by ,<br />
      Type an ip with ! to block<br />
      Example 192.168.1.164,192.168.1.165 Allow this IP`s<br />
      !192.168.1.165 Block<br />
      %,!81.21.35.457 Allow all except the ip<br />
      Use wild cards. Allow 192.168.1.???<br />
    Block !192.168.1.???<br />
    If users restrict IP is left empty then the restrict IP from the group is used. </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
</form>
-->

<?php
$db->show_footer();
?>
