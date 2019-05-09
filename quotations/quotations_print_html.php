<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 13/4/2018
 * Time: 17:43 ΜΜ
 */

function getQuotationHtml($qId, $pdf = false)
{
    global $db;
    if ($qId != "") {

        if ($db->user_data['usr_user_rights'] == 0 || $db->user_data['usg_approvals'] == 'ANSWER'){
            $data = $db->query_fetch("SELECT * FROM quotations WHERE quotations_id = " . $qId );
        }
        else {
            $data = $db->query_fetch("SELECT * FROM quotations WHERE quotations_id = " . $qId . " AND user_id = " . $db->user_data['usr_users_ID']);
        }

        //if not found anything then get out
        if ($data["quotations_id"] < 1) {
            header("Location: quotations.php");
            exit();
        }
        //fix coverage type description
        if ($data["coverage_type"] == 'FULL') {
            $data["coverage_type"] = 'Medical Cover on Full Application';
        } else if ($data["coverage_type"] == 'MORATORIUM') {
            $data["coverage_type"] = 'Medical Cover on Partial Application';
        }else if ($data["coverage_type"] == 'CPME') {
            $data["coverage_type"] = 'Medical Cover on Continuing Personal Medical Exclusions';
        }else if ($data["coverage_type"] == 'OMHD') {
            $data["coverage_type"] = 'On Medical History Disregarded';
        }


        if ($db->user_data['usr_user_rights'] == 0 || $db->user_data['usg_approvals'] == 'ANSWER'){
            $membersSql = "SELECT * FROM `quotation_members` 
            JOIN quotations ON quotations.quotations_id = quotation_members.quotations_id WHERE quotation_members.quotations_id = " . $qId . " 
            AND quotation_members.order < 900";
        }
        else {
            $membersSql = "SELECT * FROM `quotation_members` 
            JOIN quotations ON quotations.quotations_id = quotation_members.quotations_id WHERE quotation_members.quotations_id = " . $qId . " 
            AND user_id = " . $db->user_data['usr_users_ID'] . "
            AND quotation_members.order < 900";
        }
        $membersData = $db->query($membersSql);
        $prem = new quotations($qId);
        $prem->calculatePremium();
        //print_r($prem->premiumData);

    } else {
        //header("Location: quotations.php");
        return '';
    }

    if ($pdf) {
        $edgeSize = 0;
        $middleSize = 100;
    } else {
        $edgeSize = 0;
        $middleSize = 100;
    }

    $country = '';
    if ($data['country'] == 'CYP'){
        $country = 'Cyprus';
    }
    else if($data['country'] == 'GRE'){
        $country = 'Greece';
    }

    $html = '
<table width="100%">
    <tr>
        <td width="' . $edgeSize . '%"></td>
        <td width="' . $middleSize . '%">
            <table width="100%">
                <tr>
                    <td colspan="2" style="font-size:25px; color:#80B8E0;" align="left">International Medical Insurance</td>
                </tr>
                <tr>
                    <td width="50%">
                            <strong>Name: </strong> ' . $data["client_name"] . ' ' . $data["client_sur_name"] . ' ' .
        $db->convert_date_format($data["client_birthdate"], 'yyyy-mm-dd', 'dd/mm/yyyy') . '<br>
                            <strong>Mobile: </strong>' . $data["client_mobile"] . '<br>
                            <strong>Email: </strong>' . $data["client_email"] . '<br>
                            <strong>Country: </strong>'.$country."<br>";

    if ($data['individual_group'] == 'I') {
        $html .= '<strong>Agent: </strong>' . $db->user_data["usr_name_en"] . '<br>';
    }
    $html .= '
                            <span><strong>Issue Date: </strong>' . date('d/m/Y G:i:s') . '</span>
                            <br><strong>Effective Date: </strong>'.$db->convert_date_format($data["effective_date"],'yyyy-mm-dd','dd/mm/yyyy') . '
                     </td>
                    <td align="right"><img src="../images/DCare_Logo.jpg" width="300"></td>
                </tr>
                <tr>
                    <td colspan="2"><hr class="thin_horizontal_rule"></td>
                </tr>
                <tr>
                    <td colspan="2" style="">
                        <span style="color:#fff;background-color:#007bff;border-color:#007bff">' . $data["package"] . ' - ' . $data["coverage_type"] . '</span>  
                        <strong> Annual Excess: &nbsp</strong>' . $data['excess'];
    if ($data['area_of_cover'] == 'WORLDWIDE') {
        $html .= '&nbsp&nbsp&nbsp<strong>Area of coverage: &nbsp</strong>Worldwide';
    } else {
        $html .= '<br><span style="font-size: 13px;"><strong>Area of coverage: </strong>Worldwide excluding USA, Canada, China, Hong Kong, Macau, Japan, Singapore and Taiwan</span>';
    }
    $html .= '<br><br>
                    </td>
                </tr>
                
                ';

//INDIVIDUAL
    if ($data['individual_group'] == 'I') {
        $html .= '

                <tr>
                    <td colspan="2">
                        <table width="100%">
                            <tr>
                                <td>
                                    ' . $data["client_name"] . ' ' . $data["client_sur_name"];


        $totalMembers = 0;
        if ($data["individual_group"] == "I") {
            $html .= " (" . $data["client_age"] . ")";
            if ($data["client_age"] <= 10) {
                $html .= "Under 10 Free";
            }
            while ($member = $db->fetch_assoc($membersData)) {
                $totalMembers++;
                if ($pdf) {
                    //$html .= '<br>';
                }
                $html .= ",&nbsp&nbsp" . $member["name"] . " " . $member["surname"] . " (" . $member["age"];
                //echo "=>".$prem->premiumData["members"]["free"]." -> ";
                if ($member["age"] <= 10
                    && $prem->premiumData["members"]["free"] == $member["quotation_members_ID"]) {
                    $html .= "-Under 10 Free*";
                }
                $html .= ")";

            }
        }
        $html .= '        
                                </td>
                                <td align="center"><strong>Total</strong></td>
                                <td align="center"><strong>' . $prem->premiumData["frequency"] . '</strong></td>
                                <td align="center"><strong>First Instalment</strong></td>
                            </tr>
                            <tr style="background-color:#EEEEEE">
                                <td></td>
                                <td align="center">€' . $prem->premiumData["gross_plus_stamps"] . '</td>
                                <td align="center">€' . $prem->premiumData["per_payment"] . '</td>
                                <td align="center">€' . $prem->premiumData["first_payment"] . '</td>
                            </tr>
                        </table>
                    </td>                
                </tr>
                <tr>
                    <td colspan="2">
                    <br><br>
                
                
                
                
                ';
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

        $html .= '
                <table width="100%">
                    <tr>
                        <td align="center" width="20%">Excess</td>
                        <td align="center"><input type="checkbox" class=""> 0</td>
                        <td align="center"><input type="checkbox" class=""> 150</td>
                        <td align="center"><input type="checkbox" class=""> 350</td>
                        <td align="center"><input type="checkbox" class=""> 650</td>
                        <td align="center"><input type="checkbox" class=""> 1700</td>
                        <td align="center"><input type="checkbox" class=""> 3500</td>
                        <td align="center"><input type="checkbox" class=""> 6500</td>
                    </tr>
                    <tr>
                        <td align="center"><input type="checkbox" class=""><strong>Annually</strong></td>';
        foreach ($excesses as $excess) {
            $html .= '<td align="center">' . $allPremiums["ANNUAL"][$excess]["total"] . '</td>';
        }

        $html .= '</tr>';

        if ($allPremiums['Selected']['frequency'] != 'ANNUAL') {
            $html .= '
                        <tr>
                            <td align="center">
                                <strong><input type="checkbox" class="">' . $allPremiums['Selected']['frequency'] . '
                                    Instalment</strong></td>';
            foreach ($excesses as $excess) {
                $html .= '<td align="center">' . $allPremiums["Selected"][$excess]["per_payment"] . '</td>';
            }
            $html .= '
                        </tr>
                        <tr>
                            <td align="center"><strong>First Instalment</strong></td>';
            foreach ($excesses as $excess) {
                $html .= '<td align="center">' . $allPremiums["Selected"][$excess]["first_payment"] . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>
                
                    </td>
                </tr>
                ';
        $html .= '
                <tr>
                    <td colspan="2"><br><br></td>
                </tr>
                <tr>
                    <td align="center" style="font-size: 12px;" colspan="2">';
        if ($data['country'] == 'CYP' && $prem->getPricingVersion() == '2-2018'){
            $html .= '*For enrolments of a husband and wife - All children under the age of 10 will be premium free 
                    for the first year, with a payment of 50% for year 2 and reverting to normal premiums in year 3.
                <br>';
        }

        $html .= 'Premium shown are indication only.
                The premium may change following submission on a fully completed application form.
                ';
        if ($data["country"] == 'CYP') {
            $html .= '**Insurance Premium Tax - €2 - is not included in the monthly premium. It will be paid with the first monthly instalment.';
        }
        $html .= '
                    </td>
                </tr>
                <tr>
                ';
        //depending of the number of people add a margin for the footer to go down
        if ($pdf)
            $extraMargin = 340 - ($totalMembers * 20);
        else
            $extraMargin = 600 - ($totalMembers * 20);
        $html .= '
                    <td colspan="2" height="' . $extraMargin . 'px">
                    
                    </td>
                </tr>';
    } //GROUP
    else if ($data['individual_group'] == 'G') {
        if ($db->user_data['usr_user_rights'] == 0 || $db->user_data['usg_approvals'] == 'ANSWER'){
            $sql = "SELECT * FROM `quotation_members` 
            JOIN quotations ON quotations.quotations_id = quotation_members.quotations_id WHERE quotation_members.quotations_id = " . $qId . " 
            AND quotation_members.order > 900";
        }
        else {
            $sql = "SELECT * FROM `quotation_members` 
            JOIN quotations ON quotations.quotations_id = quotation_members.quotations_id WHERE quotation_members.quotations_id = " . $qId . " 
            AND user_id = " . $db->user_data['usr_users_ID'] . "
            AND quotation_members.order > 900";
        }
        $gMembersData = $db->query($sql);
        $groupDataFound = false;
        while ($member = $db->fetch_assoc($gMembersData)){
            $gAllMembers[$member['type']] = $member['total_members'];
            //print_r($member);
            $groupDataFound = true;
        }

        if ($groupDataFound == false) {
            $html.= '<tr>
                    <td colspan="2">Must specify Group Data</td>
                  </tr>';
            return $html;
        }

        $totalGMembers = $gAllMembers['single'] + $gAllMembers['married'] + $gAllMembers['family'] + $gAllMembers['sp_family'];
        //print_r($gAllMembers);
        $totalMembers = 0;
        while ($member = $db->fetch_assoc($membersData)) {
            $totalMembers += $member['total_members'];
        }

        //calculations
        $totalWeight = $gAllMembers['single'] +
            ($gAllMembers['married'] * 2) +
            ($gAllMembers['family'] * 2.8) +
            ($gAllMembers['sp_family'] * 1.8);
        //echo 'total weight = '.$totalWeight;
        //print_r($gAllMembers);
        //apply group discount

        if ($gAllMembers['group_discount'] > 0){
            $discount = (100 - $gAllMembers['group_discount']) / 100;
        }
        else {
            $discount = 1;
        }
        $netPremium = round(($prem->premiumData['net_premium'] * $discount),2);



        $calcSingleAnnual = round(($netPremium / $totalWeight),2);
        $calcSingeMonthly = round(($calcSingleAnnual / 12),2);
        $calcMarriedAnnual = round(($calcSingleAnnual * 2),2);
        $calcFamilyAnnual = round(($calcSingleAnnual * 2.8),2);
        $calcSpFamilyAnnual = round(($calcSingleAnnual * 1.8),2);

        //prepare vars for output
        $SingleAnnual = 0;
        $singleMonthly = 0;
        if ($gAllMembers['single'] > 0) {
            $SingleAnnual = $calcSingleAnnual;
            $singleMonthly = $calcSingeMonthly;
        }

        $marriedAnnual = 0;
        $marriedMonthly = 0;
        if ($gAllMembers['married'] > 0) {
            $marriedAnnual = $calcMarriedAnnual;
            $marriedMonthly = round(($calcMarriedAnnual/12),2);
        }

        $familyAnnual = 0;
        $familyMonthly = 0;
        if ($gAllMembers['family'] > 0) {
            $familyAnnual = $calcFamilyAnnual;
            $familyMonthly = round(($calcFamilyAnnual/12),2);;
        }
        $spFamilyAnnual = 0;
        $spFamilyMonthly = 0;
        if ($gAllMembers['sp_family'] > 0) {
            $spFamilyAnnual = $calcSpFamilyAnnual;
            $spFamilyMonthly = round(($calcSpFamilyAnnual/12),2);;
        }

        $singlePremium = $calcSingleAnnual * $gAllMembers['single'];
        $marriedPremium = $calcMarriedAnnual * $gAllMembers['married'];
        $familyPremium = $calcFamilyAnnual * $gAllMembers['family'];
        $spFamilyPremium = $calcSpFamilyAnnual * $gAllMembers['sp_family'];

        $totalMonthly = $singleMonthly
            + $marriedMonthly
            + $familyMonthly
            + $spFamilyMonthly;
        $toalAnnual = $SingleAnnual
            + $marriedAnnual
            + $familyAnnual
            + $spFamilyAnnual;


        $monthlyInstallment = round(($netPremium / 12),2);
        $firstMonthlyInstallment = round(($monthlyInstallment + $prem->premiumData['stamps'] + $gAllMembers['policy_fees']),2);

        $quarterlyInstallment = round(($netPremium / 4),2);
        $firstQuarterlyInstallment = round(($quarterlyInstallment + $prem->premiumData['stamps'] + $gAllMembers['policy_fees']),2);

        $semiInstallment = round(($netPremium / 2),2);
        $firstSemiInstallment = round(($semiInstallment + $prem->premiumData['stamps'] + $gAllMembers['policy_fees']),2);

        $annualInstallment = round(($netPremium + $prem->premiumData['stamps'] + $gAllMembers['policy_fees']),2);

        $html .= '<tr>
            <td colspan="2">
            
                <table width="100%">
                    <tr style="background-color:#EEEEEE">
                        <td align="center"><strong>Status</strong></td>
                        <td align="center"><strong>No of Lives</strong></td>
                        <td align="center"><strong>Monthly Rate €</strong></td>
                        <td align="center"><strong>Annual Rate €</strong></td>
                        <td align="center"><strong>Premium €</strong></td>
                    </tr>
                    <tr>
                        <td align="center">Single<br>
                        Married<br>
                        Family<br>
                        Single Parent</td>
                        <td align="center">'.$gAllMembers['single'].'<br>
                        '.$gAllMembers['married'].'<br>
                        '.$gAllMembers['family'].'<br>
                        '.$gAllMembers['sp_family'].'<br></td>
                        <td align="center">'.$singleMonthly.'<br>
                        '.$marriedMonthly.'<br>
                        '.$familyMonthly.'<br>
                        '.$spFamilyMonthly.'</td>
                        <td align="center">'.$SingleAnnual.'<br>
                        '.$marriedAnnual.'<br>
                        '.$familyAnnual.'<br>
                        '.$spFamilyAnnual.'</td>
                        <td align="center">'.$singlePremium.'<br>
                        '.$marriedPremium.'<br>
                        '.$familyPremium.'<br>
                        '.$spFamilyPremium.'<br></td>
                    </tr>
                    <tr style="background-color:#EEEEEE">
                        <td align="center"><strong>Total</strong></td>
                        <td align="center">'.$totalGMembers.'</td>
                        <td align="center">€'.$totalMonthly.'</td>
                        <td align="center">€'.$toalAnnual.'</td>
                        <td align="center">€'.$netPremium.'</td>
                    </tr>
                </table>
                <br>
                <table>
                    <tr>
                        <td>Total Premium</td>
                        <td>€'.($netPremium + $prem->premiumData['stamps'] + $gAllMembers['policy_fees']).'</td>
                    </tr>
                    <tr>
                        <td>Total Employees</td>
                        <td>'.$totalGMembers.'</td>
                    </tr>
                    <tr>
                        <td>Total Lives</td>
                        <td>'.$totalMembers.'</td>
                    </tr>
                    <tr>';

        if ($prem->frequencyOfPayment == 'MONTHLY'){
            $freqLabelFirst = 'First Monthly  Installment';
            $freqLabelFirstAmount = $firstMonthlyInstallment;
            $freqLabelNext = 'Next Monthly Installment';
            $freqLabelNextAmount =  '€'.$monthlyInstallment;
        }
        else if ($prem->frequencyOfPayment == 'QUARTERLY'){
            $freqLabelFirst = 'First Quarterly  Installment';
            $freqLabelFirstAmount = $firstQuarterlyInstallment;
            $freqLabelNext = 'Next Quarterly Installment';
            $freqLabelNextAmount =  '€'.$quarterlyInstallment;
        }
        else if ($prem->frequencyOfPayment == 'SEMI - ANNUAL'){
            $freqLabelFirst = 'First Semi - Annual  Installment';
            $freqLabelFirstAmount = $firstSemiInstallment;
            $freqLabelNext = 'Next Semi - Annual Installment';
            $freqLabelNextAmount =  '€'.$semiInstallment;
        }
        else if ($prem->frequencyOfPayment == 'ANNUAL'){
            $freqLabelFirst = 'Annual Installment ';
            $freqLabelFirstAmount = $annualInstallment;
            $freqLabelNext = '';
            $freqLabelNextAmount =  '';
        }

        $html .='
                        <td>'.$freqLabelFirst.'</td>
                        <td>€'.$freqLabelFirstAmount.'</td>
                    </tr>
                    <tr>
                        <td>'.$freqLabelNext.'</td>
                        <td>'.$freqLabelNextAmount.'</td>
                    </tr>
                </table>
                
            
            </td>
        </tr>
        <tr>
            <td colspan="2"><hr style="h-divider"> </td>
        </tr>
        <tr>
        
            <td colspan="2" class="main_text_smaller"><strong><u>Notes:</u></strong><br>';

        if ($prem->frequencyOfPayment == 'MONTHLY'){
            $html .= '<br>-) These rates are annual premiums quoted in EUR, payable in 12 installments.';
        }
        else if ($prem->frequencyOfPayment == 'QUARTERLY'){
            $html .= '<br>-) These rates are annual premiums quoted in EUR, payable in 4 installments.';
        }
        else if ($prem->frequencyOfPayment == 'SEMI - ANNUAL'){
            $html .= '<br>-) These rates are annual premiums quoted in EUR, payable in 2 installments.';
        }else if ($prem->frequencyOfPayment == 'ANNUAL'){
            $html .= '<br>-) These rates are annual premiums quoted in EUR, payable in 1 installment.';
        }

        $html.= '
                <br>-) The Population adjustments will be amended within the next installment invoice.
                <br>-) Contract situs: Cyprus
                <br>-) Policy fees and Insurance Premium Tax will be paid with the first installment. The total amount 
                is '.($gAllMembers['policy_fees'] + $prem->premiumData['stamps']).' euros.
                <br>-) This quotation is valid for commencement dates up to: 30 days form the issue date.
                <br>-) Premiums are indicative and calculated based on the staff details you gave us.
                <br>-) This quote has been elaborated understanding that this is a mandatory group, where employees cannot chose 
                whether to have this insurance cover or not. Premium is 100% payable by the employer.
                <br>-) Should the number of lives or change in profile differ by 10% or more from the population quoted above, we reserve the right 
                to review and amend the rates and terms offered.
            </td>
        </tr>
        <tr>
            <td colspan="2" height="350px"></td>
        </tr>';


    }

    $html .= '         
                <tr>
                    <td colspan="2">
                        <table width="100%">
                            <tr>
                                <td width="33%" align="center">Client Signature<br><br><br>---------------</td>
                                <td width="33%" align="center">Agent Signature<br><br><br>---------------</td>
                                <td width="33%" align="center">Date<br><br><br>---------------</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <div style="font-size: 11px; font-weight: bold; color: #000000">
                        Underwritten by Lloyd’s Insurance Company S.A.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center" style="font-size: 11px; color:#666666;">
                        T +357 24822622 | F +357 24822623 | E info@akdemetriou.com | W www.akdemetriou.com
                        <br>
                        A.K. Demetriou Insurance Agents, Sub-agents & Consultants Ltd<br>
                        2 Tefkrou Anthia, 6045 Larnaca, Cyprus
                    </td>
                </tr>
                
            </table>
        </td>
        <td width="' . $edgeSize . '%"></td>
    </tr>
</table>';
    return $html;

}
