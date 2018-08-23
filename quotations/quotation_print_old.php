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
    //fix coverage type description
    if ($data["coverage_type"] == 'FULL') {
        $data["coverage_type"] = 'Medical Cover on Full Application';
    }
    else if ($data["coverage_type"] == 'MORATORIUM'){
        $data["coverage_type"] = 'Medical Cover on Partial Application';
    }

    $membersSql = "SELECT * FROM `quotation_members` 
            JOIN quotations ON quotations.quotations_id = quotation_members.quotations_id WHERE quotation_members.quotations_id = " . $_GET["lid"] . " 
            AND user_id = " . $db->user_data['usr_users_ID'];
    $membersData = $db->query($membersSql);

    $prem = new quotations($_GET["lid"]);
    $prem->calculatePremium();
    //print_r($prem->premiumData);


} else {
    header("Location: quotations.php");
    exit();
}
$db->admin_layout_printer = 'yes';
$db->show_header();
?>


    <div class="container" style="height: 1100px;">
        <div class="row">
            <div class="col-8">
                <span class="light_blue_header">International Medical Insurance</span>
                <br>
                <br>
                <strong>Name: </strong> <?php echo $data["client_name"] . " " . $data["client_sur_name"]." ".
                    $db->convert_date_format($data["client_birthdate"],'yyyy-mm-dd','dd/mm/yyyy'); ?>&nbsp&nbsp
                <strong>ID: </strong><?php echo $data["client_id"]; ?><br>
                <strong>Mobile: </strong><?php echo $data["client_mobile"]; ?>&nbsp&nbsp
                <strong>Email: </strong><?php echo $data["client_email"]; ?><br>
                <strong>Agent: </strong><?php echo $db->user_data["usr_name_en"]; ?><br>
                <strong>Issue Date: </strong><?php echo date('d/m/Y G:i:s'); ?><br>
                <br>
            </div>
            <div class="col-4">
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
                <strong>Area of coverage:
                    &nbsp</strong>Worldwide excluding USA, Canada, China, Hong Kong, Macau, Japan, Singapore and Taiwan
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
                                echo " (" . $data["client_age"] . ")";
                                if ($data["client_age"] <= 10) {
                                    echo "Under 10 Free";
                                }
                                while ($member = $db->fetch_assoc($membersData)) {
                                    $totalMembers++;
                                    echo ",&nbsp&nbsp" . $member["name"] . " " . $member["surname"] . " (" . $member["age"] ;
                                    //echo "=>".$prem->premiumData["members"]["free"]." -> ";
                                    if ($member["age"] <= 10
                                    && $prem->premiumData["members"]["free"] == $member["quotation_members_ID"]) {
                                        echo "-Under 10 Free*";
                                    }
                                    echo ")";
                                }
                            }

                            ?>
                        </th>
                        <th scope="col" class="text-center align-middle">Total</th>
                        <th scope="col" class="text-center"><?php echo $prem->premiumData["frequency"]; ?></th>
                        <th scope="col" class="text-center">First Instalment</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="row"></th>
                        <td class="text-center">€<?php echo $prem->premiumData["gross_plus_stamps"]; ?></td>
                        <td class="text-center">€<?php echo $prem->premiumData["per_payment"]; ?></td>
                        <td class="text-center">€<?php echo $prem->premiumData["first_payment"]; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <br>
        <?php

        //calculate for the table
        $excesses = array(0, 150, 350, 650, 1700, 3500, 6500);
        foreach ($excesses as $excess) {
            $prem->excess = $excess;
            $prem->calculatePremium();

            //print_r($prem->premiumData);

            $allPremiums['Selected'][$excess]["total"] = $prem->premiumData["gross_plus_stamps"];
            $allPremiums['Selected'][$excess]["client"] = $prem->premiumData["client"];
            if (isset($prem->premiumData["members"])) {
                foreach ($prem->premiumData["members"] as $memberId => $memberData) {
                    $allPremiums['Selected'][$excess][$memberId] = $memberData;
                }
            }
            $allPremiums['Selected'][$excess]["stamps"] = $prem->premiumData["stamps"];
            $allPremiums['Selected'][$excess]["per_payment"] = $prem->premiumData["per_payment"];
            $allPremiums['Selected'][$excess]["first_payment"] = $prem->premiumData["first_payment"];

            $allPremiums['Selected']['frequency'] = $prem->frequencyOfPayment;
            if ($prem->frequencyOfPayment == 'MONTHLY') {
                $allPremiums['Selected']['frequency'] = 'Monthly';
            } else if ($prem->frequencyOfPayment == 'SEMI - ANNUAL') {
                $allPremiums['Selected']['frequency'] = 'Semi Annual';
            }
            if ($prem->frequencyOfPayment == 'QUARTERLY') {
                $allPremiums['Selected']['frequency'] = 'Quarterly';
            }
        }
        foreach ($excesses as $excess) {
            $prem->excess = $excess;
            $prem->setFrequency('ANNUAL');
            $prem->calculatePremium();

            $allPremiums['ANNUAL'][$excess]["client"] = $prem->premiumData["client"];

            if (isset($prem->premiumData["members"])) {
                foreach ($prem->premiumData["members"] as $memberId => $memberData) {
                    $allPremiums['ANNUAL'][$excess][$memberId] = $memberData;
                }
            }
            $allPremiums['ANNUAL'][$excess]["stamps"] = $prem->premiumData["stamps"];
            $allPremiums['ANNUAL'][$excess]["per_payment"] = $prem->premiumData["per_payment"];
            $allPremiums['ANNUAL'][$excess]["first_payment"] = $prem->premiumData["first_payment"];
            $allPremiums['ANNUAL'][$excess]["total"] = $prem->premiumData["gross_plus_stamps"];
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
                        //remove this line on this version
                        if ($data["individual_group"] == "I" && 1 == 2) {
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
                    while ($member = $db->fetch_assoc($membersData)) {
                        //remove this row in this layout
                        if (1 == 2) {
                            ?>
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
                            <?php
                        }
                    }
                    //remove this row
                    if (1 == 2) {
                        ?>
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
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan="2" class="text-right"><strong>Annually</strong> <input type="checkbox" class=""></td>
                        <?php foreach ($excesses as $excess) { ?>
                            <td class="text-center"><?php echo($allPremiums["ANNUAL"][$excess]["total"]); ?></td>
                        <?php } ?>
                    </tr>
                    <?php
                    if ($allPremiums['Selected']['frequency'] != 'ANNUAL') {
                        ?>
                        <tr>
                            <td colspan="2" class="text-right">
                                <strong><?php echo $allPremiums['Selected']['frequency']; ?>
                                    Instalment <input type="checkbox" class=""></strong></td>
                            <?php foreach ($excesses as $excess) { ?>
                                <td class="text-center"><?php echo $allPremiums["Selected"][$excess]["per_payment"]; ?></td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-right"><strong>First Instalment</strong></td>
                            <?php foreach ($excesses as $excess) { ?>
                                <td class="text-center"><?php echo $allPremiums["Selected"][$excess]["first_payment"]; ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-12 text-center main_text_smaller">
                *For enrolments of a husband and wife - All children under the age of 10 will be premium free
                for the first year, with a payment of 50% for year 2 and reverting to normal premiums in year 3.
                <br>Premium shown are indication only.
                The premium may change following submission on a fully completed application form.<br>
                <?php if ($data["country"] == 'CYP') { ?>
                    **Insurance Premium Tax - €2 - is not included in the monthly premium. It will be paid with the first monthly instalment.
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
                Agent Signature<br><br><br>---------------
            </div>
            <div class="col text-center">
                Date<br><br><br>---------------
            </div>
        </div>
    </div>

    <div class="container">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    Underwritten by certain Underwriters at Lloyd's of London
                </div>
            </div>
        </div>
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