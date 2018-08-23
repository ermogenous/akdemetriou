<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 6/7/2018
 * Time: 9:12 ΜΜ
 */

include("../include/main.php");
include("../include/tables.php");
include('approvals_class.php');

$db = new Main();
//$db->system_on_test = 'yes';
$db->admin_title = "Pricing Modify";

if ($_GET["lid"] != "") {
    $app = new approvals($_GET['lid']);
}

if ($_POST['action'] == 'create') {
    $app->create($_POST['message'], 0, $_POST['group']);
    $app = new approvals($_GET['lid']);
    header("Location: quotations.php");
    exit();
}

if ($_POST['action'] == 'delete') {

    $app = new approvals($_POST['lid']);
    $app->delete();
    header("Location: quotations.php");
    exit();

}


//INSERT RECORD
if ($_GET["lid"] != "") {

    $sql = 'SELECT * FROM quotations WHERE quotations_id = ' . $_GET['lid'];
    $qData = $db->query_fetch($sql);

    $sql = "SELECT * FROM quotation_approvals WHERE oqa_quotation_ID = " . $_GET['lid'] . ' ORDER BY oqa_quotation_approvals_ID DESC';
    $qaResult = $db->query($sql);

}

//check if the group data exists
$sqlCheck = 'SELECT
SUM(IF(quotation_members.order BETWEEN 991 AND 994,quotation_members.total_members,0))as clo_members_check
FROM
quotations
JOIN quotation_members ON quotations.quotations_id = quotation_members.quotations_id
WHERE
quotations.quotations_id = ' . $_GET['lid'];
$checkData = $db->query_fetch($sqlCheck);
$groupFieldsError = '';
if ($checkData['clo_members_check'] > 0) {
    //the fields looks ok
} else {
    $groupFieldsError = 'You must fill the group fields';
}


$db->show_header();
?>

<div class="container">
    <div class="row alert alert-secondary col-12 text-center">
        <div class="col-1"><a href="quotations.php">Back</a></div>
        <div class="col-11">Quotation Approvals</div>
    </div>


    <?php if ($groupFieldsError != '') { ?>
        <div class="alert alert-danger text-center row">
            <div class="col-12">
                <b><?php echo $groupFieldsError; ?>
                    <a href="quotations_modify.php?lid=<?php echo $qData['quotations_id'];?>">Back to quotation</a></b>
            </div>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col-4">Client Name [<?php echo $qData['quotations_id']; ?>]</div>
        <div class="col-8"><?php echo $qData['client_name']; ?></div>
    </div>
    <div class="row col-12" style="height: 25px;"></div>
    <form method="post">
        <!--NO APPROVAL -->
        <?php
        if ($app->noApproval == true || $app->status == 'D' || $app->processStatus == 'V') {
            ?>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-8"><?php if ($app->status == 'D') echo 'Deleted Approval - Create New'; else if ($app->processStatus == 'V') echo '';else { ?>No approval found<?php } ?></div>
            </div>
            <div class="row">
                <div class="col-4">Message</div>
                <div class="col-8"><textarea
                            name="message"
                            id="message"
                            class="form-control"
                    ></textarea></div>
            </div>
            <div class="row">
                <div class="col-4">To Group</div>
                <div class="col-8">
                    <select
                            name="group"
                            id="group"
                            class="form-control">

                        <?php
                        $sql = "SELECT * FROM users_groups WHERE usg_approvals = 'ANSWER'";
                        $result = $db->query($sql);
                        while ($group = $db->fetch_assoc($result)) {
                            ?>
                            <option value="<?php echo $group['usg_users_groups_ID']; ?>"><?php echo $group['usg_group_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-4"></div>
                <div class="col-8">
                    <?php if ($groupFieldsError == '') { ?>
                    <button type="submit" class="btn btn-primary">Request Approval</button>
                    <input type="hidden" id="action" name="action" value="create">
                    <?php } ?>
                </div>
            </div>
            <?php
        } if (1==1) {
            while ($approval = $db->fetch_assoc($qaResult)) {
                ?>
                <div class="row">
                    <div class="col-12" style="height: 20px;"></div>
                </div>
                <div class="row">
                    <div class="col-4">Requested on</div>
                    <div class="col-8"><?php echo $approval['oqa_send_date_time']; ?></div>
                </div>
                <div class="row">
                    <div class="col-4">Status</div>
                    <div class="col-8"><?php echo approvalGetStatusLabel($approval['oqa_status']); ?></div>
                </div>
                <div class="row">
                    <div class="col-4">Process Status</div>
                    <div class="col-8"><?php echo approvalsGetProcessStatusLabel($approval['oqa_process_status']); ?></div>
                </div>
                <div class="row">
                    <div class="col-4">Message</div>
                    <div class="col-8"><?php echo $approval['oqa_message']; ?></div>
                </div>
                <div class="row">
                    <div class="col-4">Reply Message</div>
                    <div class="col-8"><?php echo $approval['oqa_reply_message']; ?></div>
                </div>
                <div class="row">
                    <div class="col-4"></div>
                    <div class="col-8">
                        <?php if ($approval['oqa_status'] == 'A') { ?>
                            <form method="post">
                                <input type="submit" name="Submit" value="Delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this approval?');">
                                <input type="hidden" name="action" id="action" value="delete">
                                <input type="hidden" name="aid" id="aid" value="<?php echo $approval['oqa_quotation_approvals_ID'];?>">
                                <input type="hidden" name="lid" id="lid" value="<?php echo $_GET['lid'];?>">
                            </form>
                        <?php } ?>
                    </div>
                </div>
            <?php }
        } ?>
    </form>
</div>

<?php
$db->show_footer();
?>
