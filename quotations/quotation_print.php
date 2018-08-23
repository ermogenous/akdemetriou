<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 13/4/2018
 * Time: 17:43 ΜΜ
 */

include("../include/main.php");
include("quotations_class.php");
$db = new Main();
//$db->system_on_test = 'yes';
$db->admin_title = "Quotation Print";

include('quotations_print_html.php');

$db->admin_layout_printer = 'yes';
$db->show_header();

echo '<div class="container">';

echo getQuotationHtml($_GET["lid"]);

echo '</div>';

$db->show_footer();