<?php
include("../include/main.php");
include("../include/tables.php");

$db = new Main(1,'UTF-8');
$db->admin_title = "Users Permissions";

$db->show_header();

$table = new draw_table('permissions','prm_permissions_ID','ASC');

$table->generate_data();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
            <div class="text-center"><?php $table->show_pages_links(); ?></div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th scope="col"><?php $table->display_order_links('ID','prm_permissions_ID');?></th>
                        <th scope="col"><?php $table->display_order_links('Name','prm_name');?></th>
                        <th scope="col"><?php $table->display_order_links('File Name','prm_filename');?></th>
                        <th scope="col"><?php $table->display_order_links('Restricted','prm_restricted');?></th>
                        <th scope="col"><?php $table->display_order_links('Type','prm_type');?></th>
                        <th scope="col">
                            <a href="permissions_modify.php">
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
                            <th scope="row"><?php echo $row["prm_permissions_ID"];?></th>
                            <td><?php echo $row["prm_name"];?></td>
                            <td><?php echo $row["prm_filename"];?></td>
                            <td><?php echo $row["prm_restricted"]; ?></td>
                            <td><?php echo $row["prm_type"]; ?></td>
                            <td>
                                <a href="permissions_modify.php?lid=<?php echo $row["prm_permissions_ID"];?>"><i class="fas fa-edit"></i></a>&nbsp
                                <a href="permissions_delete.php?lid=<?php echo $row["prm_permissions_ID"];?>"
                                   onclick="return confirm('Are you sure you want to delete this permission?');"><i class="fas fa-minus-circle"></i></a>
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
