<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 7/7/2018
 * Time: 8:37 ΜΜ
 */

include("../include/main.php");
include("../include/tables.php");
include('approvals_class.php');

$db = new Main(1, 'UTF-8');
$db->admin_title = "Approvals";

//check if user has access
if ($db->user_data['usr_user_rights'] == 0 || $db->user_data['usg_approvals'] == 'ANSWER') {
    //allow access
} else {
    header("Location: quotations.php");
    exit();
}

$db->show_header();

$table = new draw_table(
    'quotation_approvals 
    JOIN quotations ON oqa_quotation_ID = quotations_id', 'oqa_status, oqa_quotation_approvals_ID', 'DESC');


$table->generate_data();
?>

<div class="container">
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <div class="text-center"><?php $table->show_pages_links(); ?></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col"><?php $table->display_order_links('ID', 'oqa_quotation_approvals_ID'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Quotation', 'quotations_id'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Client', 'client_name'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Status', 'oqa_status'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Process Status', 'oqa_process_status'); ?></th>
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
                        <tr class="<?php echo getColor($row['oqa_status']); ?>">
                            <th scope="row"><?php echo $row["oqa_quotation_approvals_ID"]; ?></th>
                            <td><a href="quotations_modify.php?lid=<?php echo $row["quotations_id"]; ?>">
                                    <?php echo $row['quotations_id']; ?>
                                </a></td>
                            <td><?php echo $row["client_name"]; ?></td>
                            <td><?php echo approvalGetStatusLabel($row["oqa_status"]); ?></td>
                            <td><?php echo approvalsGetProcessStatusLabel($row["oqa_process_status"]); ?></td>
                            <td>
                                <?php if ($row['oqa_status'] == 'A') { ?>
                                    <a href="approvals_modify.php?lid=<?php echo $row["oqa_quotation_approvals_ID"]; ?>"><i
                                                class="fas fa-edit"></i></a>&nbsp
                                <?php }
                                if ($row['oqa_status'] == 'C') { ?>
                                    <a href="approvals_modify.php?lid=<?php echo $row["oqa_quotation_approvals_ID"]; ?>"><i
                                                class="fas fa-eye"></i></a>&nbsp
                                <?php }
                                if ($row["oqa_status"] == 'A' && $row["oqa_process_status"] == 'O' && $db->user_data['usg_approvals'] == 'REQUEST') { ?>
                                    <a href="approvals_delete.php?lid=<?php echo $row["oqa_quotation_approvals_ID"]; ?>"
                                       onclick="return confirm('Are you sure you want to delete this approval?');"><i
                                                class="fas fa-minus-circle"></i></a>
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

function getColor($status)
{
    if ($status == 'A') {
        return 'alert alert-warning';
    } else if ($status == 'C') {
        return 'alert alert-primary';
    } else if ($status == 'D') {
        return 'alert alert-danger';
    }
}

?>
