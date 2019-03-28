<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 12/4/2018
 * Time: 11:26 ΜΜ
 */

include("../include/main.php");
include('approvals_class.php');
$db = new Main();
//$db->system_on_test = 'yes';
$db->admin_title = "Pricing Modify";

//INSERT RECORD
if ($_POST["action"] == "insert") {
    $db->check_restriction_area('insert');

    $_POST["fld_user_id"] = $db->user_data["usr_users_ID"];
    $_POST["fld_effective_date"] = $db->convert_date_format($_POST["fld_effective_date"], 'dd/mm/yyyy', 'yyyy-mm-dd');

    if ($_POST["fld_individual_group"] == "G") {
        $_POST["fld_client_sur_name"] = '';
        $_POST["fld_client_id"] = '';
        $_POST["fld_client_age"] = '';
        $_POST["fld_client_birthdate"] = '';
    } else {
        //fix birthdate format
        $_POST["fld_client_birthdate"] = $db->convert_date_format($_POST["fld_client_birthdate"], 'dd/mm/yyyy', 'yyyy-mm-dd');
    }

    $quotationNewId = $db->db_tool_insert_row('quotations', $_POST, 'fld_', 1);

//add the members
    //if Individual
    if ($_POST["fld_individual_group"] == "I") {
        for ($i = 1; $i <= 50; $i++) {
            if ($_POST["memberNum-" . $i] == $i) {
                if ($_POST["showTypeNum-" . $i] == 'show' && $_POST["initialType-" . $i] == 'new') {
                    $member['fld_quotations_id'] = $quotationNewId;
                    $member['fld_name'] = $_POST["client_name-" . $i];
                    $member['fld_surname'] = $_POST["client_sur_name-" . $i];
                    $member['fld_id'] = $_POST["client_id-" . $i];
                    $member['fld_age'] = $_POST["client_age-" . $i];
                    $member['fld_birthdate'] = $_POST["client_birthdate-" . $i];
                    $member['fld_type'] = $_POST['client_type-' . $i];
                    $member['fld_order'] = $i;
                    $member['fld_total_members'] = 1;
                    $member['fld_individual_group'] = 'I';

                    $member["fld_birthdate"] = $db->convert_date_format($_POST["client_birthdate-" . $i], 'dd/mm/yyyy', 'yyyy-mm-dd');

                    $db->db_tool_insert_row('quotation_members', $member, 'fld_');
                }
            }
        }
    } //if GROUP
    else if ($_POST["fld_individual_group"] == "G") {
        for ($i = 1; $i <= 52; $i++) {

            if ($i == 1) {
                $age = 18;
            } else {
                $age = 17 + $i;
            }

            if ($_POST["gm_group_member_" . $i] > 0) {
                $member['fld_individual_group'] = 'G';
                $member['fld_quotations_id'] = $quotationNewId;
                $member['fld_name'] = "GROUP";
                $member['fld_surname'] = "AGE";
                $member['fld_id'] = "0";
                $member['fld_age'] = $age;
                $member['fld_birthdate'] = '0000-00-00';
                $member['fld_order'] = $i;
                $member['fld_total_members'] = $_POST["gm_group_member_" . $i];
                $db->db_tool_insert_row('quotation_members', $member, 'fld_');
            }
        }
        //add the groups
        $member['fld_individual_group'] = 'G';
        $member['fld_quotations_id'] = $quotationNewId;
        $member['fld_name'] = "GROUP";
        $member['fld_surname'] = "AGE";
        $member['fld_id'] = "0";
        $member['fld_age'] = "0";
        $member['fld_birthdate'] = '0000-00-00';


        $member['fld_type'] = 'single';
        $member['fld_order'] = "991";
        $member['fld_total_members'] = $_POST["gm_group_single"];
        $db->db_tool_insert_row('quotation_members', $member, 'fld_');

        $member['fld_type'] = 'married';
        $member['fld_order'] = "992";
        $member['fld_total_members'] = $_POST["gm_group_married"];
        $db->db_tool_insert_row('quotation_members', $member, 'fld_');

        $member['fld_type'] = 'family';
        $member['fld_order'] = "993";
        $member['fld_total_members'] = $_POST["gm_group_family"];
        $db->db_tool_insert_row('quotation_members', $member, 'fld_');

        $member['fld_type'] = 'sp_family';
        $member['fld_order'] = "994";
        $member['fld_total_members'] = $_POST["gm_group_sp_family"];
        $db->db_tool_insert_row('quotation_members', $member, 'fld_');

        $member['fld_type'] = 'group_discount';
        $member['fld_order'] = "995";
        $member['fld_total_members'] = $_POST["gm_group_discount"];
        $db->db_tool_insert_row('quotation_members', $member, 'fld_');

        $member['fld_type'] = 'policy_fees';
        $member['fld_order'] = "999";
        $member['fld_total_members'] = $_POST["gm_group_fees"];
        $db->db_tool_insert_row('quotation_members', $member, 'fld_');

    }


    header("Location: quotations.php?alert-success=Quotation Created Successfully");
    exit();
//UPDATE RECORD
} else if ($_POST["action"] == "update") {
    $db->check_restriction_area('update');

    $_POST["fld_effective_date"] = $db->convert_date_format($_POST["fld_effective_date"], 'dd/mm/yyyy', 'yyyy-mm-dd');

    if ($_POST["fld_individual_group"] == "G") {
        $_POST["fld_client_sur_name"] = '';
        $_POST["fld_client_id"] = '';
        $_POST["fld_client_age"] = '';
        $_POST["fld_client_birthdate"] = '';
    } else {
        $_POST["fld_client_birthdate"] = $db->convert_date_format($_POST["fld_client_birthdate"], 'dd/mm/yyyy', 'yyyy-mm-dd');
    }
    $db->db_tool_update_row('quotations', $_POST, "`quotations_id` = " . $_POST["lid"], $_POST["lid"], 'fld_');

    //add the members
    //if Individual
    if ($_POST["fld_individual_group"] == "I") {
        for ($i = 1; $i <= 50; $i++) {
            if ($_POST["memberNum-" . $i] == $i) {

                //delete if any Group rows in the db
                $sqlDelete = "DELETE FROM quotation_members WHERE quotations_id = " . $_POST["lid"] . " AND individual_group = 'G';";
                $db->query($sqlDelete);

                //load data into array for later use
                $member['fld_quotations_id'] = $_POST["lid"];
                $member['fld_name'] = $_POST["client_name-" . $i];
                $member['fld_surname'] = $_POST["client_sur_name-" . $i];
                $member['fld_id'] = $_POST["client_id-" . $i];
                $member['fld_age'] = $_POST["client_age-" . $i];
                $member['fld_birthdate'] = $_POST["client_birthdate-" . $i];
                $member['fld_type'] = $_POST['client_type-' . $i];
                $member['fld_order'] = $i;
                $member['fld_individual_group'] = 'I';
                $member['fld_total_members'] = 1;

                $member['fld_birthdate'] = $db->convert_date_format($member['fld_birthdate'], 'dd/mm/yyyy', 'yyyy-mm-dd');
                //if to insert
                if ($_POST["showTypeNum-" . $i] == 'show' && $_POST["initialType-" . $i] == 'new') {
                    $db->db_tool_insert_row('quotation_members', $member, 'fld_');
                } else if ($_POST["showTypeNum-" . $i] == 'show' && $_POST["initialType-" . $i] == 'update') {
                    $db->db_tool_update_row('quotation_members',
                        $member,
                        'quotation_members_ID = ' . $_POST["initialMemberDbId-" . $i],
                        $_POST["initialMemberDbId-" . $i],
                        'fld_'
                    );
                } else if ($_POST["showTypeNum-" . $i] == 'delete' && $_POST["initialType-" . $i] == 'update') {
                    $db->db_tool_delete_row('quotation_members',
                        $_POST["initialMemberDbId-" . $i],
                        'quotation_members_ID = ' . $_POST["initialMemberDbId-" . $i] . " AND quotations_id = " . $_POST["lid"]);
                }
            }
        }
    }//if GROUP
    else if ($_POST["fld_individual_group"] == "G") {
        //delete if any Individual rows in the db
        $sqlDelete = "DELETE FROM quotation_members WHERE quotations_id = " . $_POST["lid"] . " AND individual_group = 'I';";
        $db->query($sqlDelete);
        for ($i = 1; $i <= 52; $i++) {

            if ($i == 1) {
                $age = 18;
            } else {
                $age = 17 + $i;
            }

            $member['fld_individual_group'] = 'G';
            $member['fld_name'] = "GROUP";
            $member['fld_surname'] = "AGE";
            $member['fld_id'] = "0";
            $member['fld_age'] = $age;
            $member['fld_birthdate'] = '0000-00-00';
            $member['fld_order'] = $i;
            $member['fld_total_members'] = $_POST["gm_group_member_" . $i];

            //first check if the record exists.
            //if exists then update
            //echo $i."=>".$_POST["gm_group_member_".$i."_ID"]." -> ".$_POST["gm_group_member_" .$i];
            if ($_POST["gm_group_member_" . $i . "_ID"] > 0 && $_POST["gm_group_member_" . $i] > 0) {
                //echo $age."-> Update<br>";
                $db->db_tool_update_row('quotation_members',
                    $member,
                    "quotation_members_ID = " . $_POST["gm_group_member_" . $i . "_ID"],
                    $_POST["gm_group_member_" . $i . "_ID"],
                    "fld_");
            } //if does not exists then insert
            else if ($_POST["gm_group_member_" . $i . "_ID"] < 1 && $_POST["gm_group_member_" . $i] > 0) {
                $member['fld_quotations_id'] = $_POST["lid"];
                //echo $age."-> Insert<br>";
                $db->db_tool_insert_row('quotation_members', $member, 'fld_');
            } //else delete if a record exists
            else if ($_POST["gm_group_member_" . $i . "_ID"] > 0 && $_POST["gm_group_member_" . $i] < 1) {
                //echo $age."-> Delete<br>";
                $db->db_tool_delete_row('quotation_members', $_POST["gm_group_member_" . $i . "_ID"], "quotation_members_ID = " . $_POST["gm_group_member_" . $i . "_ID"]);
            }
        }
        //group lines
        //add the groups
        $member['fld_individual_group'] = 'G';
        $member['fld_quotations_id'] = $_POST["lid"];
        $member['fld_name'] = "GROUP";
        $member['fld_surname'] = "AGE";
        $member['fld_id'] = "0";
        $member['fld_age'] = "0";
        $member['fld_birthdate'] = '0000-00-00';


        $member['fld_type'] = 'single';
        $member['fld_order'] = "991";
        $member['fld_total_members'] = $_POST["gm_group_single"];
        $db->db_tool_insert_update_row('quotation_members',
            $member,
            "quotations_id = " . $_POST["lid"] . " AND type = 'single'",
            'quotation_members_ID',
            'fld_',
            '');

        $member['fld_type'] = 'married';
        $member['fld_order'] = "992";
        $member['fld_total_members'] = $_POST["gm_group_married"];
        $db->db_tool_insert_update_row('quotation_members',
            $member,
            "quotations_id = " . $_POST["lid"] . " AND type = 'married'",
            'quotation_members_ID',
            'fld_',
            '');

        $member['fld_type'] = 'family';
        $member['fld_order'] = "993";
        $member['fld_total_members'] = $_POST["gm_group_family"];
        $db->db_tool_insert_update_row('quotation_members',
            $member,
            "quotations_id = " . $_POST["lid"] . " AND type = 'family'",
            'quotation_members_ID',
            'fld_',
            '');

        $member['fld_type'] = 'sp_family';
        $member['fld_order'] = "994";
        $member['fld_total_members'] = $_POST["gm_group_sp_family"];
        $db->db_tool_insert_update_row('quotation_members',
            $member,
            "quotations_id = " . $_POST["lid"] . " AND type = 'sp_family'",
            'quotation_members_ID',
            'fld_',
            '');

        $member['fld_type'] = 'group_discount';
        $member['fld_order'] = "995";
        $member['fld_total_members'] = $_POST["gm_group_discount"];
        $db->db_tool_insert_update_row('quotation_members',
            $member,
            "quotations_id = " . $_POST["lid"] . " AND type = 'group_discount'",
            'quotation_members_ID',
            'fld_',
            '');

        $member['fld_type'] = 'policy_fees';
        $member['fld_order'] = "999";
        $member['fld_total_members'] = $_POST["gm_group_fees"];
        $db->db_tool_insert_update_row('quotation_members',
            $member,
            "quotations_id = " . $_POST["lid"] . " AND type = 'policy_fees'",
            'quotation_members_ID',
            'fld_',
            '');

    }
    header("Location: quotations.php?alert-success=Quotation Updated Successfully");
    exit();
}

//echo $db->prepare_text_as_html(print_r($_POST, true));
$lockedQuotation = false;
if ($_GET["lid"] != "") {

    $sql = "SELECT * FROM `quotations` WHERE `quotations_id` = " . $_GET["lid"] . " AND user_id = " . $db->user_data['usr_users_ID'];
    $data = $db->query_fetch($sql);

    $membersSql = "SELECT * FROM `quotation_members` 
            JOIN quotations ON quotations.quotations_id = quotation_members.quotations_id WHERE quotation_members.quotations_id = " . $_GET["lid"] . " 
            AND user_id = " . $db->user_data['usr_users_ID'];
    $membersData = $db->query($membersSql);

    //fix birthdate
    $data["client_birthdate"] = $db->convert_date_format($data["client_birthdate"], 'yyyy-mm-dd', 'dd/mm/yyyy');
    $data["effective_date"] = $db->convert_date_format($data["effective_date"], 'yyyy-mm-dd', 'dd/mm/yyyy');

    if ($data["quotations_id"] == null) {
        header("Location: quotations.php?na");
        exit();
    }

    //check if any pending approval exists
    //to disable saving the quotation
    $approvals = new approvals($_GET['lid']);
    $lockedQuotation = $approvals->lockQuotation();

    //include('quotations_class.php');
    //$quote = new quotations($_GET["lid"]);
    //$quote->calculatePremium();

}

$db->enable_jquery_ui();
$db->show_header();

?>

    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-xl-2 d-none d-lg-block"></div>
            <div class="col-lg-8 col-xl-8 col-xs-12 col-sm-12">
                <form name="groups" method="post" action="" onSubmit=""
                      class="justify-content-center needs-validation"
                      novalidate>

                    <div class="alert alert-dark text-center">
                        <b><?php if ($_GET["lid"] == "") echo "Insert"; else echo "Update"; ?>&nbsp;Quotation</b>
                    </div>
                    <?php if ($lockedQuotation == true) { ?>
                        <div class="alert alert-danger text-center">
                            Approval found. Quotation is locked for editing.
                        </div>
                    <?php } ?>
                    <div class="form-group row">
                        <label for="fld_individual_group" class="col-sm-4 col-form-label">Individual/Group</label>
                        <div class="col-sm-8">
                            <select name="fld_individual_group" id="fld_individual_group"
                                    class="form-control"
                                    onchange="checkIndividualGroupOptions();"
                                    required>
                                <?php if ($db->user_data["usr_user_rights"] != 4) { ?>
                                <option value="">
                                    <?php } ?>

                                </option>
                                <option value="I" <?php if ($data["individual_group"] == 'I') echo "selected=\"selected\""; ?>>
                                    Individual
                                </option>
                                <?php if ($db->user_data["usr_user_rights"] != 4) { ?>
                                    <option value="G" <?php if ($data["individual_group"] == 'G') echo "selected=\"selected\""; ?>>
                                        Group
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="fld_language" class="col-md-2 col-form-label">Language</label>
                        <div class="col-md-4">
                            <select name="fld_language" id="fld_language" class="form-control">
                                <option value="ENG" <?php if ($data["language"] == 'ENG') echo "selected=\"selected\""; ?>>
                                    English
                                </option>
                                <option value="GRE" <?php if ($data["language"] == 'GRE') echo "selected=\"selected\""; ?>>
                                    Greek
                                </option>
                            </select>
                        </div>
                        <label for="fld_country" class="col-md-2 col-form-label">Country</label>
                        <div class="col-md-4">
                            <select name="fld_country" id="fld_country" class="form-control">
                                <option value="CYP" <?php if ($data["country"] == 'CYP') echo "selected=\"selected\""; ?>>
                                    Cyprus
                                </option>
                                <option value="GRE" <?php if ($data["country"] == 'GRE') echo "selected=\"selected\""; ?>>
                                    Greece
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="fld_client_name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-4">
                            <input name="fld_client_name" type="text" id="fld_client_name"
                                   class="form-control"
                                   value="<?php echo $data["client_name"]; ?>"
                                   required>
                            <div class="invalid-feedback">
                                Please provide a name.
                            </div>
                        </div>
                        <label for="fld_client_sur_name" class="col-sm-2 col-form-label">SurName</label>
                        <div class="col-sm-4">
                            <input name="fld_client_sur_name" type="text" id="fld_client_sur_name"
                                   class="form-control"
                                   value="<?php echo $data["client_sur_name"]; ?>"
                                   required>
                            <div class="invalid-feedback">
                                Please provide a surname.
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="fld_client_mobile" class="col-sm-2 col-form-label">Mobile</label>
                        <div class="col-sm-4">
                            <input name="fld_client_mobile" type="text" id="fld_client_mobile"
                                   class="form-control"
                                   value="<?php echo $data["client_mobile"]; ?>">
                        </div>
                        <label for="fld_client_email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-4">
                            <input name="fld_client_email" type="email" id="fld_client_email"
                                   class="form-control"
                                   value="<?php echo $data["client_email"]; ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="fld_client_id" class="col-sm-2 col-form-label">Client ID</label>
                        <div class="col-sm-4">
                            <input name="fld_client_id" type="text" id="fld_client_id"
                                   class="form-control"
                                   value="<?php echo $data["client_id"]; ?>">
                            <div class="invalid-feedback">
                                Please provide an ID.
                            </div>
                        </div>
                        <label for="fld_client_address" class="col-sm-2 col-form-label">Address</label>
                        <div class="col-sm-4">
                            <input name="fld_client_address" type="text" id="fld_client_address"
                                   class="form-control"
                                   value="<?php echo $data["client_address"]; ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="fld_client_age" class="col-sm-2 col-form-label">BirthDate</label>
                        <div class="col-sm-4">
                            <input name="fld_client_birthdate" type="text" id="fld_client_birthdate"
                                   class="form-control"
                                   placeholder="dd/mm/yyyy"
                                   onchange="autofillAgeField();"
                                   value="<?php echo $data["client_birthdate"]; ?>"
                                   required>

                            <div class="invalid-feedback">
                                Please provide client`s Birthdate.
                            </div>
                        </div>
                        <label for="fld_client_age" class="col-sm-2 col-form-label">
                            Age &nbsp&nbsp&nbsp
                            <i class="fas fa-sync-alt" onclick="getAgeFromBirthDate();" style="cursor: pointer;"></i>
                        </label>
                        <div class="col-sm-4 container-fluid">
                            <input name="fld_client_age" type="text" id="fld_client_age"
                                   class="form-control"
                                   value="<?php echo $data["client_age"]; ?>"
                                   required>
                            <div class="invalid-feedback">
                                Please provide valid client`s Age.
                            </div>
                        </div>
                    </div>


                    <div class="alert alert-secondary text-center">
                        <strong>Policy Information</strong>
                    </div>

                    <div class="form-group row">
                        <label for="fld_coverage_type" class="col-sm-4 col-form-label">Coverage Type</label>
                        <div class="col-sm-8">
                            <select name="fld_coverage_type" id="fld_coverage_type" class="form-control"
                                    onchange="coverageTypeCheck()">
                                <option value="FULL" <?php if ($data["coverage_type"] == 'FULL') echo "selected=\"selected\""; ?>>
                                    Medical Cover on Full Application
                                </option>
                                <option value="MORATORIUM" <?php if ($data["coverage_type"] == 'MORATORIUM') echo "selected=\"selected\""; ?>>
                                    Medical Cover on Partial Application
                                </option>
                                <option value="CPME" <?php if ($data["coverage_type"] == 'CPME') echo "selected=\"selected\""; ?>>
                                    Medical Cover on Continuing Personal Medical Exclusions
                                </option>
                                <option value="OMHD" <?php if ($data["coverage_type"] == 'OMHD') echo "selected=\"selected\""; ?>>
                                    On Medical History Disregarded
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" id="loadingRow">
                        <label for="fld_loading" class="col-sm-4 col-form-label">Loading %</label>
                        <div class="col-sm-8">
                            <input name="fld_loading" type="text" id="fld_loading"
                                   class="form-control"
                                   value="<?php echo $data["loading"]; ?>"
                                   required>
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
                                    Worldwide excluding USA, Canada, China...
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="fld_frequency_of_payment" class="col-sm-4 col-form-label">Frequency Of
                            Payment</label>
                        <div class="col-sm-8">
                            <select name="fld_frequency_of_payment" id="fld_frequency_of_payment" class="form-control">
                                <option value="ANNUAL" <?php if ($data["frequency_of_payment"] == 'ANNUAL') echo "selected=\"selected\""; ?>>
                                    ANNUAL
                                </option>
                                <option value="SEMI - ANNUAL" <?php if ($data["frequency_of_payment"] == 'SEMI - ANNUAL') echo "selected=\"selected\""; ?>>
                                    SEMI - ANNUAL
                                </option>
                                <option value="QUARTERLY" <?php if ($data["frequency_of_payment"] == 'QUARTERLY') echo "selected=\"selected\""; ?>>
                                    QUARTERLY
                                </option>
                                <option value="MONTHLY" <?php if ($data["frequency_of_payment"] == 'MONTHLY') echo "selected=\"selected\""; ?>>
                                    MONTHLY
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="fld_excess" class="col-sm-4 col-form-label">Excess</label>
                        <div class="col-sm-8">
                            <select name="fld_excess" id="fld_excess" class="form-control">
                                <option value="0" <?php if ($data["excess"] == '0') echo "selected=\"selected\""; ?>>
                                    0
                                </option>
                                <option value="150" <?php if ($data["excess"] == '150') echo "selected=\"selected\""; ?>>
                                    150
                                </option>
                                <option value="350" <?php if ($data["excess"] == '350') echo "selected=\"selected\""; ?>>
                                    350
                                </option>
                                <option value="650" <?php if ($data["excess"] == '650') echo "selected=\"selected\""; ?>>
                                    650
                                </option>
                                <option value="1700" <?php if ($data["excess"] == '1700') echo "selected=\"selected\""; ?>>
                                    1700
                                </option>
                                <option value="3500" <?php if ($data["excess"] == '3500') echo "selected=\"selected\""; ?>>
                                    3500
                                </option>
                                <option value="6500" <?php if ($data["excess"] == '6500') echo "selected=\"selected\""; ?>>
                                    6500
                                </option>
                            </select>
                        </div>
                    </div>

                    <?php
                    //under 10 discounts is removed after 1/4/2019
                    if (1 == 2) {
                        ?>
                        <div class="form-group row">
                            <label for="fld_under_10_discount" class="col-sm-4 col-form-label">Under 10
                                Discounts</label>
                            <div class="col-sm-5">
                                <select name="fld_under_10_discount" id="fld_under_10_discount" class="form-control">
                                    <option value="free" <?php if ($data["under_10_discount"] == 'free') echo "selected=\"selected\""; ?>>
                                        Under 10 Free
                                    </option>
                                    <option value="charge" <?php if ($data["under_10_discount"] == 'charge') echo "selected=\"selected\""; ?>>
                                        Charge Under 10
                                    </option>
                                    <option value="chargeDiscount" <?php if ($data["under_10_discount"] == 'chargeDiscount') echo "selected=\"selected\""; ?>>
                                        Charge Under 10 + 50% Discount
                                    </option>
                                </select>

                            </div>
                            <div class="col-sm-3">
                                Spouse Must Exists
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group row">
                        <label for="fld_effective_date" class="col-sm-4 col-form-label">Effective Date</label>
                        <div class="col-sm-4">
                            <input name="fld_effective_date" type="text" id="fld_effective_date"
                                   class="form-control"
                                   placeholder="dd/mm/yyyy"
                                   required>
                            <script>
                                $(function () {
                                    $("#fld_effective_date").datepicker();
                                    $("#fld_effective_date").datepicker("option", "dateFormat", "dd/mm/yy");
                                    $("#fld_effective_date").val('<?php echo $data["effective_date"]?>');

                                });
                            </script>
                            <div class="invalid-feedback">
                                Please provide a valid effective date.
                            </div>
                        </div>
                    </div>

                    <!-- Members ------------------------------------------------------------------------------------------------------------------------------------------------->
                    <!-- Individual HTML-->
                    <div id="individualMembersContainer" class="d-none">
                        <div class="alert alert-secondary text-center">
                            <strong>Members Information</strong>
                            <i class="fas fa-plus-square" onclick="addMember()" style="cursor: pointer;"></i>
                        </div>


                        <?php for ($i = 1; $i <= 50; $i++) { ?>
                            <div class="container d-none" id="membersContainer-<?php echo $i; ?>">
                                <?php echo $i; ?>

                            </div>
                        <?php } ?>
                    </div>

                    <!-- GROUP HTML-->
                    <div class="container d-none" id="groupMembersContainer">

                    </div>
                    <!-- Buttons ---------------------------------------------------------------------------------------------------------------------------------------->
                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label"></label>
                        <div class="col-sm-8">
                            <input name="action" type="hidden" id="action"
                                   value="<?php if ($_GET["lid"] == "") echo "insert"; else echo "update"; ?>">
                            <input name="lid" type="hidden" id="lid" value="<?php echo $_GET["lid"]; ?>">
                            <?php if ($lockedQuotation == false) { ?>
                                <input type="submit" name="Submit" value=" Save Quotation " class="btn btn-secondary">
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-2 col-xl-2 d-none d-lg-block"></div>
        </div>
    </div>
    <script>

        var totalMembers = 0;
        //generate the update members
        <?php
        $membersArray = array();
        if ($_GET["lid"] > 0) {
            while ($member = $db->fetch_assoc($membersData)) {
                $member["birthdate"] = $db->convert_date_format($member["birthdate"], 'yyyy-mm-dd', 'dd/mm/yyyy');
                if ($data["individual_group"] == "I") {
                    echo "addMember('" .
                        $member["name"] . "','" .
                        $member["surname"] . "','" .
                        $member["id"] . "','" .
                        $member["age"] . "','" .
                        $member["birthdate"] . "','" .
                        $member["type"] .
                        "','update', " .
                        $member["quotation_members_ID"] . ");";
                }
                if ($member['order'] < 900) {
                    $membersArray[$member["age"]]["value"] = $member["total_members"];
                    $membersArray[$member["age"]]["ID"] = $member["quotation_members_ID"];
                } //group totals
                else if ($member['type'] == 'single') {
                    $gMembersArray['0']['single'] = $member['total_members'];
                } else if ($member['type'] == 'married') {
                    $gMembersArray['0']['married'] = $member['total_members'];
                } else if ($member['type'] == 'family') {
                    $gMembersArray['0']['family'] = $member['total_members'];
                } else if ($member['type'] == 'sp_family') {
                    $gMembersArray['0']['sp_family'] = $member['total_members'];
                } else if ($member['type'] == 'policy_fees') {
                    $gMembersArray['0']['policy_fees'] = $member['total_members'];
                } else if ($member['type'] == 'group_discount') {
                    $gMembersArray['0']['group_discount'] = $member['total_members'];
                }
            }


        }
        ?>

        function coverageTypeCheck() {
            let option = $('#fld_coverage_type').val();
            if (option == 'CPME' || option == 'OMHD') {
                $('#loadingRow').show();
                $('#fld_loading').removeAttr('disabled');
            }
            else {
                $('#loadingRow').hide();
                $('#fld_loading').attr('disabled', 'disabled');
            }
        }

        coverageTypeCheck();

        function addMember(memberName = '', memberSurname = '', memberId = '', memberAge = '', memberBirthdate = '', memberType = '', type = 'new', memberDbId = 0) {

            //get the content
            var content = getMemberHTML(memberName, memberSurname, memberId, memberAge, memberBirthdate, memberType, type, memberDbId);

            //fill the content into the div
            document.getElementById('membersContainer-' + totalMembers).innerHTML = content;

            //show the div
            document.getElementById('membersContainer-' + totalMembers).classList.remove('d-none');


        }

        function removeMember(memberNum) {

            if (confirm('Are you sure you want to delete this member?')) {
                //set the hidden field to delete
                document.getElementById('showTypeNum-' + memberNum).value = 'delete';

                //get the header to red and strikethrough
                document.getElementById('divHead-' + memberNum).classList.remove('alert-dark');
                document.getElementById('divHead-' + memberNum).classList.add('alert-danger');
                document.getElementById('divHead-' + memberNum).classList.add('main_text_strikethrough');

                //hide the contents
                document.getElementById('divBody-' + memberNum).classList.add('d-none');

                // hide the trash icon
                document.getElementById('trashIcon-' + memberNum).classList.add('d-none');

                //show the undo icon
                document.getElementById('undoIcon-' + memberNum).classList.remove('d-none');

                if (document.getElementById('client_age-' + memberNum).value == '') {
                    document.getElementById('client_age-' + memberNum).value = 0;
                }
                if (document.getElementById('client_name-' + memberNum).value == '') {
                    document.getElementById('client_name-' + memberNum).value = '#';
                }
                if (document.getElementById('client_sur_name-' + memberNum).value == '') {
                    document.getElementById('client_sur_name-' + memberNum).value = '#';
                }
                if (document.getElementById('client_birthdate-' + memberNum).value == '') {
                    document.getElementById('client_birthdate-' + memberNum).value = '#';
                }

            }
        }

        function undoRemoveMember(memberNum) {

            if (confirm('Are you sure you want to undo?')) {
                //set the hidden field to show
                document.getElementById('showTypeNum-' + memberNum).value = 'show';

                //fix the header back to normal
                document.getElementById('divHead-' + memberNum).classList.add('alert-dark');
                document.getElementById('divHead-' + memberNum).classList.remove('alert-danger');
                document.getElementById('divHead-' + memberNum).classList.remove('main_text_strikethrough');

                //show the contents
                document.getElementById('divBody-' + memberNum).classList.remove('d-none');

                //hide the undo icon
                document.getElementById('undoIcon-' + memberNum).classList.add('d-none');

                //show the trash icon
                document.getElementById('trashIcon-' + memberNum).classList.remove('d-none');

                if (document.getElementById('client_age-' + memberNum).value == '0') {
                    document.getElementById('client_age-' + memberNum).value = '';
                }
                if (document.getElementById('client_name-' + memberNum).value == '#') {
                    document.getElementById('client_name-' + memberNum).value = '';
                }
                if (document.getElementById('client_sur_name-' + memberNum).value == '#') {
                    document.getElementById('client_sur_name-' + memberNum).value = '';
                }
                if (document.getElementById('client_birthdate-' + memberNum).value == '#') {
                    document.getElementById('client_birthdate-' + memberNum).value = '';
                }
            }
        }

        function getGroupMembersHTML() {

            var memberHTML = `
            <div class="alert alert-dark text-center">
                <strong>Group Members List</strong>
            </div>
            <?php for ($i = 1; $i <= 52; $i++) { ?>
            <div class="form-group row">
                <label for="gm_group_member_<?php echo $i;?>" class="col-sm-4 col-form-label">
                Age
                <?php
                if ($i == 1) {
                    echo "0-18";
                    $age = 18;
                } else {
                    echo $i + 17;
                    $age = $i + 17;
                }
                if ($membersArray[$age]["value"] < 1) {
                    $membersArray[$age]["value"] = '';
                }
                ?>
                </label>
                <div class='col-8'>
                    <input name="gm_group_member_<?php echo $i;?>" type="number" id="gm_group_member_<?php echo $i;?>"
                                   class="form-control"
                                   value="<?php echo $membersArray[$age]["value"];?>">
                     <input type="hidden" id="gm_group_member_<?php echo $i;?>_ID" name="gm_group_member_<?php echo $i;?>_ID" value="<?php echo $membersArray[$age]["ID"]; ?>">
                </div>
            </div>
            <?php } ?>
            <div class="alert alert-dark text-center"><strong>Groups Totals</strong></div>
            <div class="form-group row">
                <label for="gm_group_single" class="col-sm-4 col-form-label">
                Single
                </label>
                <div class="col-8">
                    <input name="gm_group_single" type="number" id="gm_group_single"
                                   class="form-control"
                                   onKeyUp="calculateGroupDiscount()"
                                   value="<?php echo $gMembersArray['0']['single'];?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="gm_group_married" class="col-sm-4 col-form-label">
                Married
                </label>
                <div class="col-8">
                    <input name="gm_group_married" type="number" id="gm_group_married"
                                   class="form-control"
                                   onKeyUp="calculateGroupDiscount()"
                                   value="<?php echo $gMembersArray['0']['married'];?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="gm_group_family" class="col-sm-4 col-form-label">
                Family
                </label>
                <div class="col-8">
                    <input name="gm_group_family" type="number" id="gm_group_family"
                                   class="form-control"
                                   onKeyUp="calculateGroupDiscount()"
                                   value="<?php echo $gMembersArray['0']['family'];?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="gm_group_sp_family" class="col-sm-4 col-form-label">
                Single Parent Family
                </label>
                <div class="col-8">
                    <input name="gm_group_sp_family" type="number" id="gm_group_sp_family"
                                   class="form-control"
                                   onKeyUp="calculateGroupDiscount()"
                                   value="<?php echo $gMembersArray['0']['sp_family'];?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="gm_group_fees" class="col-sm-4 col-form-label">
                Total Policy Fees
                </label>
                <div class="col-8">
                    <input name="gm_group_fees" type="number" id="gm_group_fees"
                                   class="form-control"
                                   value="<?php echo $gMembersArray['0']['policy_fees'];?>">
                </div>
            </div>
            <div class="form-group row">
                <label for="gm_group_discount" class="col-sm-4 col-form-label"
                id="lbl_gm_group_discount" name="lbl_gm_group_discount">
                    Group Discount
                </label>
                <div class="col-8">
                    <input name="gm_group_discount" type="number" id="gm_group_discount"
                                   class="form-control"
                                   value="<?php echo $gMembersArray['0']['group_discount'];?>">
                </div>
            </div>

        `;
            return memberHTML;
        }

        function checkIndividualGroupOptions() {

            if ($('#fld_individual_group').val() == 'G') {
                var html = getGroupMembersHTML();
                document.getElementById('groupMembersContainer').classList.remove('d-none');
                document.getElementById('individualMembersContainer').classList.add('d-none');
                document.getElementById('groupMembersContainer').innerHTML = html;
                document.getElementById('fld_client_sur_name').value = '';
                document.getElementById('fld_client_sur_name').disabled = true;
                document.getElementById('fld_client_age').value = '';
                document.getElementById('fld_client_age').disabled = true;
                document.getElementById('fld_client_id').value = '';
                document.getElementById('fld_client_id').disabled = true;
                document.getElementById('fld_client_birthdate').value = '';
                document.getElementById('fld_client_birthdate').disabled = true;

                let exists = false;
                $('#fld_coverage_type  option').each(function () {
                    if (this.value == 'CPME') {
                        exists = true;
                    }
                });
                if (exists == false) {
                    $('#fld_coverage_type').append($('<option>', {
                        value: 'CPME',
                        text: 'Medical Cover on Continuing Personal Medical Exclusions'
                    }));
                    $('#fld_coverage_type').append($('<option>', {
                        value: 'OMHD',
                        text: 'On Medical History Disregarded'
                    }));
                }
            }
            else if ($('#fld_individual_group').val() == 'I') {
                document.getElementById('groupMembersContainer').classList.add('d-none');
                document.getElementById('individualMembersContainer').classList.remove('d-none');
                document.getElementById('fld_client_sur_name').disabled = false;
                document.getElementById('fld_client_age').disabled = false;
                document.getElementById('fld_client_id').disabled = false;
                document.getElementById('fld_client_birthdate').disabled = false;

                $('#fld_coverage_type option[value="CPME"]').remove();
                $('#fld_coverage_type option[value="OMHD"]').remove();

            }
            else {
                document.getElementById('groupMembersContainer').classList.add('d-none');
                document.getElementById('individualMembersContainer').classList.add('d-none');
            }

        }

        function getMemberHTML(memberName = '', memberSurname = '', memberId = '', memberAge = '', memberBirthdate = '', memberType = '', type = 'new', memberDbId = 0) {
            totalMembers++;
            var memberTypeSpouse = '';
            var memberTypeDependent = '';
            if (memberType == '') {
                if (totalMembers == 1) {
                    memberTypeSpouse = `selected="selected"`;
                }
                else {
                    memberTypeDependent = `selected="selected"`;
                    memberTypeSpouse = `disabled="true"`;
                }
            }
            else {
                if (memberType == 'SPOUSE') {
                    memberTypeSpouse = `selected="selected"`;
                }
                else {
                    memberTypeDependent = `selected="selected"`;
                }
            }

            var memberHTML = `
                    <div class="alert alert-dark text-center" id="divHead-` + totalMembers + `">
                        <strong>
                        Member ` + totalMembers + `
                        <i class="fas fa-plus-square" onclick="addMember()" style="cursor: pointer;"></i>
                        <i class="fas fa-trash"
                        id="trashIcon-` + totalMembers + `"
                        onclick="removeMember(` + totalMembers + `);" style="cursor: pointer;"></i>
                        <i class="fas fa-undo d-none" id="undoIcon-` + totalMembers + `"
                        onclick="undoRemoveMember(` + totalMembers + `);" style="cursor: pointer;"></i>
                        </strong>
                    </div>
                    <div id="divBody-` + totalMembers + `">
                        <div class="form-group row">
                            <label for="client_name-` + totalMembers + `" class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-4">
                                <input name="client_name-` + totalMembers + `" type="text" id="client_name-` + totalMembers + `"
                                       class="form-control"
                                       value="` + memberName + `"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide a name.
                                </div>
                            </div>
                            <label for="client_sur_name-` + totalMembers + `" class="col-sm-2 col-form-label">SurName</label>
                            <div class="col-sm-4">
                                <input name="client_sur_name-` + totalMembers + `" type="text" id="client_sur_name-` + totalMembers + `"
                                       class="form-control"
                                       value="` + memberSurname + `"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide a surname.
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="client_id-` + totalMembers + `" class="col-sm-2 col-form-label">ID</label>
                            <div class="col-sm-4">
                                <input name="client_id-` + totalMembers + `" type="text" id="client_id-` + totalMembers + `"
                                       class="form-control"
                                       value="` + memberId + `">
                                <div class="invalid-feedback">
                                    Please provide an ID.
                                </div>
                            </div>
                            <label for="client_birthdate-` + totalMembers + `" class="col-sm-2 col-form-label">Birthdate</label>
                            <div class="col-sm-4">
                                <input name="client_birthdate-` + totalMembers + `" type="text" id="client_birthdate-` + totalMembers + `"
                                       class="form-control"
                                       value="` + memberBirthdate + `"
                                       onchange="autofillAgeFieldMember(` + totalMembers + `)"
                                       placeholder="dd/mm/yyyy"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide a birth date.
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="client_type-` + totalMembers + `" class="col-sm-2 col-form-label">Type</label>
                            <div class="col-sm-4">
                                <select name="client_type-` + totalMembers + `" id="client_type-` + totalMembers + `" class="form-control">
                                <option value="SPOUSE" ` + memberTypeSpouse + `>
                                    Spouse
                                </option>
                                <option value="DEPENDENT" ` + memberTypeDependent + `>
                                    Dependent
                                </option>
                            </select>
                            </div>
                            <label for="client_age-` + totalMembers + `" class="col-sm-2 col-form-label">
                                Age
                                &nbsp&nbsp&nbsp
                                <i class="fas fa-sync-alt" onclick="getAgeFromBirthDateMember(` + totalMembers + `);" style="cursor: pointer;"></i>
                            </label>
                            <div class="col-sm-4">
                                <input name="client_age-` + totalMembers + `" type="text" id="client_age-` + totalMembers + `"
                                       class="form-control"
                                       value="` + memberAge + `"
                                       required>
                                <div class="invalid-feedback">
                                    Please provide a valid clients Age.
                                </div>
                            </div>
                        </div>
                        <input type="hidden"
                        id="showTypeNum-` + totalMembers + `"
                        name="showTypeNum-` + totalMembers + `" value="show">

                        <input type="hidden"
                        id="initialType-` + totalMembers + `"
                        name="initialType-` + totalMembers + `" value="` + type + `">

                        <input type="hidden"
                        id="initialMemberDbId-` + totalMembers + `"
                        name="initialMemberDbId-` + totalMembers + `" value="` + memberDbId + `">

                        <input type="hidden"
                        id="memberNum-` + totalMembers + `"
                        name="memberNum-` + totalMembers + `" value="` + totalMembers + `">
                    </div>`;
            return memberHTML;
        }

        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        //<<MIC
                        //loop into all the age fields

                        var i;
                        var noErrorFound = true;
                        var allowedAge = <?php if ($db->user_data['usr_user_rights'] <= 2) {
                            echo '100';
                        } else {
                            echo '69';
                        } ?>;

                        //check the members
                        for (i = 1; i <= totalMembers; i++) {
                            var fieldValue = $('#client_age-' + i).val();
                            if ($.isNumeric(fieldValue) == false || fieldValue < 0 || fieldValue > allowedAge) {
                                $('#client_age-' + i).addClass('is-invalid');
                                $('#client_age-' + i).removeClass('is-valid');
                                event.preventDefault();
                                event.stopPropagation();
                                noErrorFound = false;
                            }
                            else {
                                $('#client_age-' + i).addClass('is-valid');
                                $('#client_age-' + i).removeClass('is-invalid');
                                noErrorFound = true;
                            }
                        }
                        //check the age of the client
                        if ($('#fld_client_age').val() > allowedAge) {
                            $('#fld_client_age').addClass('is-invalid');
                            $('#fld_client_age').removeClass('is-valid');
                            event.preventDefault();
                            event.stopPropagation();
                            noErrorFound = false;
                        } else {
                            $('#fld_client_age').addClass('is-valid');
                            $('#fld_client_age').removeClass('is-invalid');
                            noErrorFound = true;
                        }

                        //check the effective date.
                        let effectiveDate = $('#fld_effective_date').val();


                        if (isDate(effectiveDate)) {
                            $('#fld_effective_date').addClass('is-valid');
                            $('#fld_effective_date').removeClass('is-invalid');
                        }
                        else {
                            $('#fld_effective_date').addClass('is-invalid');
                            $('#fld_effective_date').removeClass('is-valid');
                            event.preventDefault();
                            event.stopPropagation();
                            noErrorFound = false;
                        }


                        //>>MIC


                        if (noErrorFound) {
                            form.classList.add('was-validated');
                        }
                    }, false);
                });
            }, false);
        })();

        function isDate(txtDate) {
            var currVal = txtDate;
            if (currVal == '')
                return false;
            //Declare Regex
            var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
            var dtArray = currVal.match(rxDatePattern); // is format OK?

            if (dtArray == null)
                return false;

            //Checks for dd/mm/yyyy format.
            dtDay = dtArray[1];
            dtMonth = dtArray[3];
            dtYear = dtArray[5];

            if (dtMonth < 1 || dtMonth > 12)
                return false;
            else if (dtDay < 1 || dtDay > 31)
                return false;
            else if ((dtMonth == 4 || dtMonth == 6 || dtMonth == 9 || dtMonth == 11) && dtDay == 31)
                return false;
            else if (dtMonth == 2) {
                var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
                if (dtDay > 29 || (dtDay == 29 && !isleap))
                    return false;
            }
            return true;
        }

        function getAgeFromBirthDate() {

            var date = document.getElementById('fld_client_birthdate').value;
            var dateSplit = date.split('/');

            var birthday = new Date(dateSplit[2] + '-' + dateSplit[1] + '-' + dateSplit[0]);
            var ageDifMs = Date.now() - birthday.getTime();
            var ageDate = new Date(ageDifMs); // miliseconds from epoch
            var age = Math.abs(ageDate.getUTCFullYear() - 1970);

            document.getElementById('fld_client_age').value = age;
        }

        function getAgeFromBirthDateMember(ID) {
            var date = document.getElementById('client_birthdate-' + ID).value;
            var dateSplit = date.split('/');

            var birthday = new Date(dateSplit[2] + '-' + dateSplit[1] + '-' + dateSplit[0]);
            var ageDifMs = Date.now() - birthday.getTime();
            var ageDate = new Date(ageDifMs); // miliseconds from epoch
            var age = Math.abs(ageDate.getUTCFullYear() - 1970);

            document.getElementById('client_age-' + ID).value = age;
        }

        function autofillAgeField() {
            if (document.getElementById('fld_client_age').value == '') {
                getAgeFromBirthDate();
            }
        }

        function autofillAgeFieldMember(ID) {
            if (document.getElementById('client_age-' + ID).value == '') {
                getAgeFromBirthDateMember(ID);
            }
        }

        function calculateGroupDiscount() {
            var single = 0;
            var married = 0;
            var family = 0;
            var spFamily = 0;
            if (document.getElementById('gm_group_single').value > 0) {
                single = document.getElementById('gm_group_single').value * 1;
            }
            if (document.getElementById('gm_group_single').value > 0) {
                married = document.getElementById('gm_group_married').value * 1;
            }
            if (document.getElementById('gm_group_single').value > 0) {
                family = document.getElementById('gm_group_family').value * 1;
            }
            if (document.getElementById('gm_group_single').value > 0) {
                spFamily = document.getElementById('gm_group_sp_family').value * 1;
            }

            var total = single + married + family + spFamily;
            var discount = 0;
            var label = '';
            if (total > 0 && total < 20) {
                discount = 0;
                label = '0-19';
            }
            else if (total >= 20 && total <= 29) {
                discount = 5;
                label = '20-29';
            }
            else if (total >= 30 && total <= 39) {
                discount = 10;
                label = '30-39';
            }
            else if (total >= 40 && total <= 49) {
                discount = 15;
                label = '40-49';
            }
            else if (total >= 50) {
                discount = 15;
                label = '>=50';
            }
            document.getElementById('gm_group_discount').value = discount;
            document.getElementById('lbl_gm_group_discount').innerHTML = 'Group Discount -> ' + label;
        }

        //run startup functions
        //check the drop down of individual groups
        checkIndividualGroupOptions();

    </script>

<?php
$db->show_footer();
?>