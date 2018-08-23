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

if ($_GET["lid"] != "") {
    $data = $db->query_fetch("SELECT * FROM quotations WHERE quotations_id = " . $_GET["lid"] . " AND user_id = " . $db->user_data['usr_users_ID']);

    //if not found anything then get out
    if ($data["quotations_id"] < 1) {
        header("Location: quotations.php");
        exit();
    }

    $membersSql = "SELECT * FROM `quotation_members` 
            JOIN quotations ON quotations.quotations_id = quotation_members.quotations_id WHERE quotation_members.quotations_id = " . $_GET["lid"] . " 
            AND user_id = " . $db->user_data['usr_users_ID'];
    $membersData = $db->query($membersSql);

    $prem = new quotations($_GET["lid"]);
    $prem->calculatePremium();
    //print_r($prem);

} else {
    header("Location: quotations.php");
    exit();
}
$db->admin_layout_printer = 'yes';
$db->show_header();
?>


    <div class="container" style="height: 1100px;">
        <div class="row">
            <div class="col-9">
                <span class="light_blue_header">International Medical Insurance</span>
                <br>
                <br>
                <strong>Name: </strong> <?php echo $data["client_name"] . " " . $data["client_sur_name"]; ?><br>
                <strong>ID: </strong><?php echo $data["client_id"]; ?><br>
                <strong>Mobile: </strong><?php echo $data["client_mobile"]; ?><br>
                <strong>Email: </strong><?php echo $data["client_email"]; ?><br>
                <strong>Agent: </strong><?php echo $db->user_data["usr_name_en"]; ?><br>
                <strong>Issue Date: </strong><?php echo date('d/m/Y G:i:s'); ?><br>
                <br>
            </div>
            <div class="col-3">
                <img src="../images/DCare_Logo.jpg" width="300">
            </div>
        </div>

        <div class="col-12">
            <hr class="thin_horizontal_rule">
        </div>

        <div class="row col-12">
            <span class="btn-primary">&nbsp
                <?php echo $data["package"]; ?> - <?php echo $data["coverage_type"]; ?>&nbsp
            </span>
            &nbsp&nbsp
            <strong>Annual Excess: &nbsp</strong><?php echo $data['excess']; ?>
            <?php
            if ($data['area_of_cover'] == 'WORLDWIDE') {
                ?>
                &nbsp&nbsp&nbsp<strong>Area of coverage: &nbsp</strong>Worldwide
            <?php
            }
            ?>


        </div>
        <div class="row col-12">

            <?php
            if ($data['area_of_cover'] == 'WORLDWIDE EXCLUDING USA') {
                ?>
                <strong>Area of coverage: &nbsp</strong>Worldwide excluding USA, Canada, China, Hong Kong, Macau, Japan, Singapore and Taiwan
            <?php
            }
            ?>
        </div>
        <br>
        <div class="row col-12">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">
                            <?php
                            echo $data["client_name"] . " " . $data["client_sur_name"];

                            $totalMembers = 0;
                            if ($data["individual_group"] == "I") {
                                echo "(" . $data["client_age"] . ")";
                                while ($member = $db->fetch_assoc($membersData)) {
                                    $totalMembers++;
                                    echo "&nbsp&nbsp" . $member["name"] . " " . $member["surname"] . "(" . $member["age"] . ")";
                                }
                            }

                            ?>
                        </th>
                        <th scope="col" class="text-center align-middle">Total</th>
                        <th scope="col" class="text-center"><?php echo $prem->premiumData["frequency"]; ?></th>
                        <th scope="col" class="text-center">First Installment</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row"></th>
                        <td class="text-center">€<?php echo $prem->premiumData["gross_premium"]; ?></td>
                        <td class="text-center">€<?php echo $prem->premiumData["per_payment"]; ?></td>
                        <td class="text-center">€<?php echo $prem->premiumData["first_payment"]; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <br><br><br>
        <?php

        //calculate for the table
        $excesses = array(0, 150, 350, 650, 1700, 3500, 6500);
        foreach ($excesses as $excess) {
            $prem->excess = $excess;
            $prem->frequencyOfPayment = 'ANNUAL';
            $prem->calculatePremium();

            $allPremiums['ANNUAL'][$excess]["total"] = $prem->premiumData["client"];
            $allPremiums['ANNUAL'][$excess]["client"] = $prem->premiumData["client"];

            if (isset($prem->premiumData["members"])) {
                foreach ($prem->premiumData["members"] as $memberId => $memberData) {
                    $allPremiums['ANNUAL'][$excess][$memberId] = $memberData;
                    $allPremiums['ANNUAL'][$excess]["total"] += $memberData;
                }
            }
        }
        foreach ($excesses as $excess) {
            $prem->excess = $excess;
            $prem->frequencyOfPayment = 'MONTHLY';
            $prem->calculatePremium();

            $allPremiums['MONTHLY'][$excess]["total"] = $prem->premiumData["client"];
            $allPremiums['MONTHLY'][$excess]["client"] = $prem->premiumData["client"];
            if (isset($prem->premiumData["members"])) {
                foreach ($prem->premiumData["members"] as $memberId => $memberData) {
                    $allPremiums['MONTHLY'][$excess][$memberId] = $memberData;
                    $allPremiums['MONTHLY'][$excess]["total"] += $memberData;
                }
            }

        }
        ?>

        <div class="row col-12">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col" class="text-center">Excess</th>
                        <th scope="col" class="text-center">Age</th>
                        <th scope="col" class="text-center">0 <input type="checkbox" class=""></th>
                        <th scope="col" class="text-center">150 <input type="checkbox" class=""></th>
                        <th scope="col" class="text-center">350 <input type="checkbox" class=""></th>
                        <th scope="col" class="text-center">650 <input type="checkbox" class=""></th>
                        <th scope="col" class="text-center">1700 <input type="checkbox" class=""></th>
                        <th scope="col" class="text-center">3500 <input type="checkbox" class=""></th>
                        <th scope="col" class="text-center">6500 <input type="checkbox" class=""></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <?php
                        if ($data["individual_group"] == "I") {
                            ?>
                            <th scope="row"><?php echo $data["client_name"] . " " . $data["client_sur_name"]; ?></th>
                            <td class="text-center"><?php echo $data["client_age"]; ?></td>
                            <?php foreach ($excesses as $excess) { ?>
                                <td class="text-center"><?php echo $allPremiums["ANNUAL"][$excess]["client"]
                                        . "<br>(" . $allPremiums["MONTHLY"][$excess]["client"] . ")"; ?></td>
                                <?php
                            }
                        }
                        ?>
                    </tr>
                    <?php
                    $membersData = $db->query($membersSql);
                    while ($member = $db->fetch_assoc($membersData)) { ?>
                        <tr>
                            <th scope="row"><?php echo $member["name"] . " " . $member["surname"]; ?></th>
                            <td class="text-center"><?php
                                echo $member["age"];
                                if ($data["individual_group"] == "G") {
                                    echo " X " . $member["total_members"];
                                }
                                ?></td>
                            <?php foreach ($excesses as $excess) { ?>
                                <td class="text-center"><?php echo $allPremiums["ANNUAL"][$excess][$member["quotation_members_ID"]]
                                        . "<br>(" . $allPremiums["MONTHLY"][$excess][$member["quotation_members_ID"]] . ")"; ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="2" class="text-right">
                            <strong>
                                Total Annually <input type="checkbox" class=""><br>
                                (Monthly) <input type="checkbox" class="">
                            </strong></td>
                        <?php foreach ($excesses as $excess) { ?>
                            <td class="text-center"><?php echo $allPremiums["ANNUAL"][$excess]["total"]
                                    . "<br>(" . $allPremiums["MONTHLY"][$excess]["total"] . ")"; ?></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-right"><strong>Monthly Installment</strong></td>
                        <?php foreach ($excesses as $excess) { ?>
                            <td class="text-center"><?php echo round(($allPremiums["MONTHLY"][$excess]["total"] / 12), 2); ?></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-right"><strong>First Installment</strong></td>
                        <?php foreach ($excesses as $excess) { ?>
                            <td class="text-center"><?php echo round(($allPremiums["MONTHLY"][$excess]["total"] / 12), 2)
                                    + $prem->premiumData["stamps"]; ?></td>
                        <?php } ?>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12 text-center main_text_smaller">
                All dependent children aged between 14 days and under 10 years are covered at no additional cost for the
                first year of coverage
                only when both parents or guardians are insured under a DCARE sub-plan.<br>
                <?php if ($data["country"] == 'CYP') { ?>
                    *Insurance Premium Tax - €2 - is not included in the monthly premium. It will be paid with the first monthly installment.
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- SIGNATURES--------------------------------------------------------------------------------------------------------------------------------------->
    <div class="container">
        <div class="row">
            <div class="col text-center">
                Client Signature<br><br><br>---------------
            </div>
            <div class="col text-center">
                Insurer Signature<br><br><br>---------------
            </div>
            <div class="col text-center">
                Date<br><br><br>---------------
            </div>
        </div>
    </div>
    <br>


    <div class="container">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    Underwritten by certain Underwriters at Lloyd's of London
                </div>
            </div>
        </div>
        <br>
        <div class="container">
            <div class="row">
                <div class="col text-left main_text_small_gray">
                    T +357 24822622 | F +357 24822623 | E info@akdemetriou.com | W www.akdemetriou.com
                    <br><br>
                    A.K. Demetriou Insurance Agents, Sub-agents & Consultants Ltd<br>
                    2 Tefkrou Anthia, 6045 Larnaca, Cyprus
                </div>
            </div>
        </div>
    </div>
<?php
$db->show_footer();
?>