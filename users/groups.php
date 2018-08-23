<?php
include("../include/main.php");
include("../include/tables.php");

$db = new Main(1,'UTF-8');
$db->admin_title = "Users Groups";

$db->show_header();

$table = new draw_table('users_groups','usg_users_groups_ID','ASC');

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
                        <th scope="col"><?php $table->display_order_links('ID','usg_users_groups_ID');?></th>
                        <th scope="col"><?php $table->display_order_links('Name','usg_group_name');?></th>
                        <th scope="col">Used By Users</th>
                        <th scope="col">
                            <a href="groups_modify.php">
                                <i class="fas fa-plus-circle"></i>
                            </a>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    while ($row = $table->fetch_data()) {
                        $sql = "SELECT COUNT(*)as clo_total_users FROM users WHERE usr_users_groups_ID = ".$row["usg_users_groups_ID"];
                        $used_by_users = $db->query_fetch($sql);
                        ?>
                        <tr>
                            <th scope="row"><?php echo $row["usg_users_groups_ID"];?></th>
                            <td><?php echo $row["usg_group_name"];?></td>
                            <td><?php echo $used_by_users["clo_total_users"];?></td>
                            <td>
                                <a href="groups_modify.php?lid=<?php echo $row["usg_users_groups_ID"];?>"><i class="fas fa-edit"></i></a>&nbsp
                                <a href="groups_delete.php?lid=<?php echo $row["usg_users_groups_ID"];?>"
                                   onclick="return confirm('Are you sure you want to delete this group?');"><i class="fas fa-minus-circle"></i></a>
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
