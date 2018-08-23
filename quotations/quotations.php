<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 12/4/2018
 * Time: 10:28 ΜΜ
 */

include("../include/main.php");
include("../include/tables.php");

$db = new Main(1, 'UTF-8');
$db->admin_title = "Quotations";

$db->show_header();

$table = new draw_table('quotations', 'quotations_id', 'DESC');
$table->extra_select_section = ' ,(SELECT oqa_process_status FROM quotation_approvals WHERE oqa_quotation_ID = quotations_id ORDER BY oqa_quotation_approvals_ID DESC LIMIT 1)as clo_approval_pstatus,
(SELECT oqa_status FROM quotation_approvals WHERE oqa_quotation_ID = quotations_id ORDER BY oqa_quotation_approvals_ID DESC LIMIT 1)as clo_approval_status';
if ($db->user_data["usr_user_rights"] != 0) {
    $table->extras = 'user_id = ' . $db->user_data["usr_users_ID"];
}
else {
    $table->extras = 'user_id = ' . $db->user_data["usr_users_ID"];
}

if ($_POST['search'] == 'search') {

    if ($table->extras != '') {
        $table->extras .= ' AND';
    }

    if ($_POST['searchType'] == 'NameSurname') {
        $table->extras .= " (client_name LIKE '%" . $_POST['searchText'] . "%' OR client_sur_name LIKE '%" . $_POST['searchText'] . "%')";
    } else if ($_POST['searchType'] == 'mobile') {
        $table->extras .= " client_mobile LIKE '%" . $_POST['searchText'] . "%'";
    } else if ($_POST['searchType'] == 'Email') {
        $table->extras .= " client_email LIKE '%" . $_POST['searchText'] . "%'";
    }

}


$table->generate_data();
?>

<div class="container">
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <p>
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#searchForm"
                        aria-expanded="false" aria-controls="searchForm">
                    Define Search
                </button>
            </p>
            <div class="collapse" id="searchForm">
                <div class="card card-body">
                    <form method="post">

                        <div class="row">
                            <div class="col">
                                <input type="text" class="form-control" placeholder="Search Text" id="searchText"
                                       name="searchText">
                            </div>
                            <div class="col">
                                <select class="form-control" placeholder="Search Type" id="searchType"
                                        name="searchType">
                                    <option value="NameSurname">Name/Surname</option>
                                    <option value="mobile">Mobile</option>
                                    <option value="email">Email</option>
                                </select>
                            </div>
                            <div class="col">
                                <input type="hidden" id="search" name="search" value="search">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-1"></div>
    </div>
</div>


<div class="container">
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <div class="text-center"><?php $table->show_pages_links(); ?></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <?php if ($db->user_data["usr_user_rights"] <= 2) { ?>
                            <th scope="col"><?php $table->display_order_links('ID', 'quotations_id'); ?></th>
                        <?php } ?>
                        <?php if ($db->user_data["usr_user_rights"] == 0) { ?>
                            <th scope="col"><?php $table->display_order_links('User', 'user_id'); ?></th>
                        <?php } ?>
                        <th scope="col"><?php $table->display_order_links('Client ID', 'client_id'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Name', 'client_name'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Surname', 'client_sur_name'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Package', 'package'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Frequency', 'frequency_of_payment'); ?></th>
                        <th scope="col">
                            <a href="quotations_modify.php">
                                <i class="fas fa-plus-circle"></i>
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $table->fetch_data()) {
                        ?>
                        <tr>
                            <?php if ($db->user_data["usr_user_rights"] <= 2) { ?>
                                <th scope="row"><?php echo $row["quotations_id"]; ?></th>
                            <?php }
                            if ($db->user_data["usr_user_rights"] == 0) { ?>
                                <td><?php echo $row['user_id']; ?></td>
                            <?php } ?>
                            <td><?php echo $row["client_id"]; ?></td>
                            <td><?php echo $row["client_name"]; ?></td>
                            <td><?php echo $row["client_sur_name"]; ?></td>
                            <td><?php echo $row["package"]; ?></td>
                            <td><?php echo $row["frequency_of_payment"]; ?></td>
                            <td>
                                <a href="quotations_modify.php?lid=<?php echo $row["quotations_id"]; ?>"><i
                                            class="fas fa-edit"></i></a>&nbsp
                                <a href="quotations_delete.php?lid=<?php echo $row["quotations_id"]; ?>"
                                   onclick="return confirm('Are you sure you want to delete this quotation?');"><i
                                            class="fas fa-minus-circle"></i></a>&nbsp
                                <a href="quotation_print.php?lid=<?php echo $row["quotations_id"]; ?>"
                                   target="_blank"><i class="fas fa-print"></i></a>&nbsp
                                <a href="quotation_print_pdf.php?lid=<?php echo $row["quotations_id"]; ?>"
                                   target="_blank"><i class="fa fa-file-pdf" aria-hidden="true"></i></a>&nbsp
                                <?php if ($db->user_data['usr_user_rights'] == 0) { ?>
                                    <a href="quotations_send_email.php?lid=<?php echo $row["quotations_id"]; ?>"><i
                                                class="fas fa-envelope"></i></a>
                                <?php } ?>
                                <?php if (($db->user_data['usg_approvals'] == 'REQUEST' || $db->user_data['usr_user_rights'] == 0) && $row['individual_group'] == 'G') { ?>
                                    <a href="quotation_approvals.php?lid=<?php echo $row["quotations_id"]; ?>"><i
                                                class="fas fa-random <?php echo getApprovalColor($row['clo_approval_status'],$row['clo_approval_pstatus']);?>"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center"><?php echo $table->show_per_page_links(); ?></div>
        </div>
        <div class="col-lg-1"></div>
    </div>
</div>

<?php
$db->show_footer();

function getApprovalColor($status, $pStatus){
    $ret = '';
    if ($status == 'D') {
        $ret = 'redColor';
    }
    else if ($pStatus == 'O'){
        $ret = 'goldColor';
    }
    else if ($pStatus == 'R'){
        $ret = 'redColor';
    }
    else if ($pStatus == 'A'){
        $ret = 'greenColor';
    }
    else if ($pStatus == 'V'){
        $ret = 'purpleColor';
    }

    return $ret;
}

?>
