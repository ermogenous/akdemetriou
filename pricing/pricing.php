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
$db->admin_title = "Pricing";

$db->show_header();

$table = new draw_table('pricing', 'pricing_ID', 'ASC');

//$table->extras = "1=1 " . $search_sql;


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
                        <th scope="col"><?php $table->display_order_links('ID', 'pricing_id'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Package', 'package'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Area Of Cover', 'area_of_cover'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Frequency', 'frequency_of_payment'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Age From', 'age_from'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Age To', 'age_to'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Excess', 'excess'); ?></th>
                        <th scope="col"><?php $table->display_order_links('Value', 'value'); ?></th>
                        <th scope="col">
                            <a href="pricing_modify.php">
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
                            <th scope="row"><?php echo $row["pricing_id"]; ?></th>
                            <td><?php echo $row["package"]; ?></td>
                            <td><?php echo $row["area_of_cover"]; ?></td>
                            <td><?php echo $row["frequency_of_payment"]; ?></td>
                            <td><?php echo $row["age_from"]; ?></td>
                            <td><?php echo $row["age_to"]; ?></td>
                            <td><?php echo $row["excess"]; ?></td>
                            <td><?php echo $row["value"]; ?></td>
                            <td>
                                <a href="pricing_modify.php?lid=<?php echo $row["pricing_id"];?>"><i class="fas fa-edit"></i></a>&nbsp
                                <a href="pricing_delete.php?lid=<?php echo $row["pricing_id"];?>"
                                   onclick="return confirm('Are you sure you want to delete this price?');"><i class="fas fa-minus-circle"></i></a>
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
?>
