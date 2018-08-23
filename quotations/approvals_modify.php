<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 7/7/2018
 * Time: 9:10 ΜΜ
 */

include("../include/main.php");
include('quotations_print_html.php');
include('quotations_class.php');

$db = new Main();
$db->admin_title = "Approvals Modify";

//INSERT RECORD
if ($_POST["action"] == "update") {
    $db->check_restriction_area('update');

    if ($_POST['Submit'] == 'Approve') {
        $data['oqa_process_status'] = 'A';
    } else if ($_POST['Submit'] == 'Reject') {
        $data['oqa_process_status'] = 'R';
    } else if ($_POST['Submit'] == 'Re-evaluate') {
        $data['oqa_process_status'] = 'V';
    }
    $data['oqa_reply_message'] = $_POST['fld_reply_message'];
    $data['oqa_reply_date_time'] = date('Y-m-d G:i:s');
    $data['oqa_status'] = 'C';

    $db->db_tool_update_row('quotation_approvals',
        $data,
        "oqa_quotation_approvals_ID = " . $_POST["lid"],
        $_POST["lid"],
        "",
        'execute',
        '');

    header("Location: approvals.php?alert-success=Approval Updated Successfully");
    exit();
}

if ($_GET["lid"] != "") {

    $sql = "SELECT * FROM `quotation_approvals` JOIN quotations ON oqa_quotation_ID = quotations_id WHERE `oqa_quotation_approvals_ID` = " . $_GET["lid"];
    $data = $db->query_fetch($sql);


}

$db->show_header();

?>

    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-xl-2 d-none d-lg-block"></div>
            <div class="col-lg-8 col-xl-8 col-xs-12 col-sm-12">
                <form name="groups" method="post" action="" onSubmit=""
                      class="justify-content-center needs-validation"
                      novalidate>

                    <div class="alert alert-dark text-center row">
                        <div class="col-1"><a href="approvals.php">Back</a></div>
                        <div class="col-11">
                            <b><?php if ($_GET["lid"] == "") echo "Insert"; else echo "Update"; ?>&nbsp;Approval</b>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-4">Quotation</div>
                        <div class="col-sm-8">
                            <?php echo $data['quotations_id']; ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-4">Client</div>
                        <div class="col-sm-8">
                            <?php echo $data['client_name']; ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-4">Message</div>
                        <div class="col-sm-8">
                            <?php echo $data['oqa_message']; ?>
                        </div>
                    </div>

                    <?php
                    if ($data['oqa_status'] == 'A' && $data['oqa_process_status'] == 'O') {
                        ?>
                        <div class="form-group row">
                            <label for="fld_reply_message" class="col-md-4 col-form-label">Reply Message</label>
                            <div class="col-md-8">
                            <textarea name="fld_reply_message" id="fld_reply_message"
                                      class="form-control"><?php echo $data["oqa_reply_message"]; ?></textarea>
                            </div>
                        </div>


                        <!-- Buttons ---------------------------------------------------------------------------------------------------------------------------------------->

                        <div class="form-group row">
                            <label for="" class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-8">
                                <input name="action" type="hidden" id="action" value="update">
                                <input name="lid" type="hidden" id="lid" value="<?php echo $_GET["lid"]; ?>">
                                <input type="submit" name="Submit" value="Approve" class="btn btn-primary">
                                <input type="submit" name="Submit" value="Reject" class="btn btn-danger">
                                <input type="submit" name="Submit" value="Re-evaluate" class="btn btn-secondary">
                            </div>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="form-group row">
                            <label for="fld_reply_message" class="col-md-4 col-form-label">Reply Message</label>
                            <div class="col-md-8"><?php echo $data["oqa_reply_message"]; ?></div>
                        </div>
                        <?php
                    }
                    ?>
                </form>
            </div>
            <div class="col-lg-2 col-xl-2 d-none d-lg-block"></div>
        </div>

        <div class="row alert alert-primary">
            <div class="col-11">Quotation</div>
            <div class="col-1">
                <i class="fas fa-plus-square"
                   onclick="showQuotationHtml();"
                   style="cursor: pointer"
                   id="showQuotationHtmlButton"></i>
                <i class="fas fa-minus-square"
                   onclick="hideQuotationHtml()"
                   style="cursor: pointer; display: none;"
                   id="hideQuotationHtmlButton"></i>
            </div>
        </div>
        <div class="row" style="display: none" id="quotationHTML">
            <?php
            $quotationHTML = getQuotationHtml($data['quotations_id']);
            echo $quotationHTML;
            ?>
        </div>

    </div>
    <script>
        function showQuotationHtml() {
            $('#quotationHTML').show();
            $('#showQuotationHtmlButton').hide();
            $('#hideQuotationHtmlButton').show();
        }

        function hideQuotationHtml() {
            $('#quotationHTML').hide();
            $('#showQuotationHtmlButton').show();
            $('#hideQuotationHtmlButton').hide();
        }
    </script>
<?php
$db->show_footer();
?>