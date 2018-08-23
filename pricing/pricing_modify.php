<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 12/4/2018
 * Time: 10:28 ΜΜ
 */
include("../include/main.php");
$db = new Main();
//$db->system_on_test = 'yes';
$db->admin_title = "Pricing Modify";

if ($_POST["action"] == "insert") {
    $db->check_restriction_area('insert');

    $db->db_tool_insert_row('pricing', $_POST, 'fld_');
    header("Location: pricing.php");
    exit();

} else if ($_POST["action"] == "update") {
    $db->check_restriction_area('update');

    $db->db_tool_update_row('pricing', $_POST, "`pricing_id` = " . $_POST["lid"], $_POST["lid"], 'fld_');
    header("Location: pricing.php");
    exit();

}


if ($_GET["lid"] != "") {

    $sql = "SELECT * FROM `pricing` WHERE `pricing_id` = " . $_GET["lid"];
    $data = $db->query_fetch($sql);
}


$db->show_header();
?>


<div class="container">
    <div class="row">
        <div class="col-lg-3 col-md-3 hidden-xs hidden-sm"></div>
        <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
            <form name="groups" method="post" action="" onSubmit="" class="justify-content-center">

                <div class="alert alert-dark text-center"><b><?php if ($_GET["lid"] == "") echo "Insert"; else echo "Update"; ?>
                    &nbsp;Pricing</b>
                </div>

                <div class="form-group row">
                    <label for="fld_coverage_type" class="col-sm-4 col-form-label">Coverage Type</label>
                    <div class="col-sm-8">
                        <select name="fld_coverage_type" id="fld_coverage_type" class="form-control">
                            <option value="FULL" <?php if ($data["coverage_type"] == 'FULL') echo "selected=\"selected\""; ?>>
                                FULL
                            </option>
                            <option value="MORATORIUM" <?php if ($data["coverage_type"] == 'MORATORIUM') echo "selected=\"selected\""; ?>>
                                MORATORIUM
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_package" class="col-sm-4 col-form-label">Package</label>
                    <div class="col-sm-8">
                        <select name="fld_package" id="fld_package" class="form-control">
                            <option value="BASIC" <?php if ($data["package"] == 'BASIC') echo "selected=\"selected\""; ?>>
                                BASIC
                            </option>
                            <option value="CORE" <?php if ($data["package"] == 'CORE') echo "selected=\"selected\""; ?>>
                                CORE
                            </option>
                            <option value="CLASSIC" <?php if ($data["package"] == 'CLASSIC') echo "selected=\"selected\""; ?>>
                                CLASSIC
                            </option>
                            <option value="PRIME" <?php if ($data["package"] == 'PRIME') echo "selected=\"selected\""; ?>>
                                PRIME
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_area_of_cover" class="col-sm-4 col-form-label">Area Of Cover</label>
                    <div class="col-sm-8">
                        <select name="fld_area_of_cover" id="fld_area_of_cover" class="form-control">
                            <option value="WORLDWIDE" <?php if ($data["area_of_cover"] == 'WORLDWIDE') echo "selected=\"selected\""; ?>>
                                WORLDWIDE
                            </option>
                            <option value="WORLDWIDE EXCLUDING USA" <?php if ($data["area_of_cover"] == 'WORLDWIDE EXCLUDING USA') echo "selected=\"selected\""; ?>>
                                WORLDWIDE EXCLUDING USA
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_frequency_of_payment" class="col-sm-4 col-form-label">Area Of Cover</label>
                    <div class="col-sm-8">
                        <select name="fld_frequency_of_payment" id="fld_frequency_of_payment" class="form-control">
                            <option value="ANNUAL" <?php if ($data["frequency_of_payment"] == 'WORLDWIDE') echo "selected=\"selected\""; ?>>
                                ANNUAL
                            </option>
                            <option value="SEMI - ANNUAL" <?php if ($data["frequency_of_payment"] == 'WORLDWIDE') echo "selected=\"selected\""; ?>>
                                SEMI - ANNUAL
                            </option>
                            <option value="QUARTERLY" <?php if ($data["frequency_of_payment"] == 'WORLDWIDE') echo "selected=\"selected\""; ?>>
                                QUARTERLY
                            </option>
                            <option value="MONTHLY" <?php if ($data["frequency_of_payment"] == 'WORLDWIDE') echo "selected=\"selected\""; ?>>
                                MONTHLY
                            </option>
                        </select>
                    </div>
                </div>



                <div class="form-group row">
                    <label for="fld_age_from" class="col-sm-4 col-form-label">Age From</label>
                    <div class="col-sm-8">
                        <input name="fld_age_from" type="text" id="fld_age_from"
                               class="form-control"
                               value="<?php echo $data["age_from"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_age_to" class="col-sm-4 col-form-label">Age To</label>
                    <div class="col-sm-8">
                        <input name="fld_age_to" type="text" id="fld_age_to"
                               class="form-control"
                               value="<?php echo $data["age_to"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_excess" class="col-sm-4 col-form-label">Excess</label>
                    <div class="col-sm-8">
                        <input name="fld_excess" type="text" id="fld_excess"
                               class="form-control"
                               value="<?php echo $data["age_excess"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="fld_value" class="col-sm-4 col-form-label">Value</label>
                    <div class="col-sm-8">
                        <input name="fld_value" type="text" id="fld_value"
                               class="form-control"
                               value="<?php echo $data["value"]; ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="" class="col-sm-4 col-form-label"></label>
                    <div class="col-sm-8">
                        <input name="action" type="hidden" id="action"
                               value="<?php if ($_GET["lid"] == "") echo "insert"; else echo "update"; ?>">
                        <input name="lid" type="hidden" id="lid" value="<?php echo $_GET["lid"]; ?>">
                        <input type="submit" name="Submit" value=" Save Price " class="btn btn-secondary">
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
