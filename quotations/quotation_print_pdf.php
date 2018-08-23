<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 15/5/2018
 * Time: 17:03 ΜΜ
 */

include("../include/main.php");
include("quotations_class.php");
$db = new Main();

include('quotations_print_html.php');

$html = getQuotationHtml($_GET["lid"], true);
//fix spaces
$html = str_replace('&nbsp', ' ', $html);

require_once '../vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf([
    'orientation' => 'P',
    'format' => 'A4',
    'margin_top' => 2,
    'margin_footer' => 2,
    'margin_left' => 5,
    'margin_right' => 5
]);

//margins

$mpdf->WriteHTML($html);
$mpdf->Output();

