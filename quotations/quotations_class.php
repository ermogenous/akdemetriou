<?php

class quotations
{

    private $quotationData;
    private $membersSql;
    private $clientPremium;
    private $membersPremium;

    public $excess;
    public $frequencyOfPayment;
    private $under10Free = true;
    private $foundSpouse = false;
    private $pricingTable = 'pricing';
    private $pricingVersion = '2-2018';

    public $premiumData;


    function __construct($quotationsID)
    {
        global $db;

        if ($db->user_data['usr_user_rights'] == 0 || $db->user_data['usg_approvals'] == 'ANSWER') {
            $this->quotationData = $db->query_fetch("SELECT * FROM quotations WHERE quotations_id = " . $quotationsID);
            $this->membersSql = "SELECT * FROM quotation_members
            JOIN quotations ON quotations.quotations_id = quotation_members.quotations_id
            WHERE quotation_members.quotations_id = " . $quotationsID
                . " AND quotation_members.order < 900";
        } else {
            $this->quotationData = $db->query_fetch("SELECT * FROM quotations WHERE quotations_id = " . $quotationsID . " AND user_id = " . $db->user_data['usr_users_ID']);
            $this->membersSql = "SELECT * FROM quotation_members
            JOIN quotations ON quotations.quotations_id = quotation_members.quotations_id
            WHERE quotation_members.quotations_id = " . $quotationsID
                . " AND user_id = " . $db->user_data['usr_users_ID']
                . " AND quotation_members.order < 900";
        }

        $this->excess = $this->quotationData["excess"];
        $this->frequencyOfPayment = $this->quotationData["frequency_of_payment"];

        //use the proper pricing table
        if ($db->compare2dates($this->quotationData['effective_date'], '2019-03-31', 'yyyy-mm-dd') == 1) {
            $this->pricingTable = 'pricing_4_2019';
            $this->pricingVersion = '4-2019';
        }

    }

    public function verifyAccess($superClass)
    {
        if ($superClass->user_data['usr_user_rights'] == 0) {
            return true;
        } else {
            if ($this->quotationData['quotations_id'] > 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    private function calculatePerson($data)
    {
        global $db;
        //prepare the data from the client first
        if ($this->quotationData["coverage_type"] == 'CPME' || $this->quotationData["coverage_type"] == 'OMHD') {
            $coverageType = 'FULL';
        } else {
            $coverageType = $this->quotationData["coverage_type"];
        }

        //find the proper pricing table
        //if ($this->quotationData[''])


        $sql = "SELECT * FROM " . $this->pricingTable . " WHERE 
                  coverage_type = '" . $coverageType . "' AND  
                  package = '" . $this->quotationData["package"] . "' AND 
                  area_of_cover = '" . $this->quotationData["area_of_cover"] . "' AND 
                  frequency_of_payment = '" . $this->frequencyOfPayment . "' AND 
                  excess = " . $this->excess . " ";

        //check first if its the client or a member
        //member
        $age = 0;
        //check if age is more than 69
        if ($data['age'] > 69) {
            $memberAge = 69;
        } else {
            $memberAge = $data['age'];
        }
        if ($data['client_age'] > 69) {
            $clientAge = 69;
        } else {
            $clientAge = $data['client_age'];
        }
        if ($data["quotation_members_ID"] != null) {
            $sql .= " AND age_from <= " . $memberAge . " AND age_to >= " . $memberAge;
            $age = $data["age"];
        } //client
        else {
            $sql .= " AND age_from <= " . $clientAge . " AND age_to >= " . $clientAge;
            $age = $data["client_age"];
        }
        //echo $sql."\n\n<br><br>";
        $result = $db->query($sql);

        if ($db->num_rows($result) > 1) {
            echo 'ERROR FOUND';
            exit();
        }
        $return = $db->fetch_assoc($result);
        //if greece add multiplier
        $multiplier = 1;
        if ($this->quotationData["country"] == "GRE") {
            $multiplier = 1.15;
        }
        //if loading then add it to the multiplier
        if ($this->quotationData['coverage_type'] == 'CPME' || $this->quotationData['coverage_type'] == 'OMHD') {
            $multiplier += ($this->quotationData['loading'] / 100);
        }
        $return["value"] = $return["value"] * $multiplier;

        //echo "Age:".$data["age"]." Price each:".$return["value"]."<br>";

        //check if under 10 which is free and if spouse exists
        $return["free"] = 0;
        //echo "->".$age." ".$this->under10Free." Spouse:".$this->foundSpouse."<br><br>";

        //under 10 discounts after 1/4/2019 is removed
        if ($this->pricingVersion == '2-2018') {
            if ($age <= 10
                && $this->under10Free == true
                && $this->foundSpouse == true
                && $data["type"] == 'DEPENDENT') {

                //echo "Here";
                if ($this->quotationData['under_10_discount'] == ''
                    || $this->quotationData['under_10_discount'] == 'free') {
                    $return["value"] = 0;
                    $return["free"] = 1;
                } else if ($this->quotationData['under_10_discount'] == 'chargeDiscount') {
                    $return["value"] = round(($return["value"] * 0.5), 2);
                } else if ($this->quotationData['under_10_discount'] == 'charge') {
                    //do nothing. charge the member
                }


            }
        }
        //check if the member is a spouse
        if ($data["type"] == 'SPOUSE') {
            $this->foundSpouse = true;
        }
        //echo "Age".$data["age"]." -> ".$this->under10Free." => ".$this->foundSpouse." -> ".$data["type"]."<br>\n\n";
        return $return;
    }

    public function calculatePremium()
    {
        global $db;
        unset ($this->premiumData);
        $this->premiumData["package"] = $this->quotationData["package"];
        $this->premiumData["frequency"] = $this->frequencyOfPayment;
        $this->premiumData["excess"] = $this->excess;
        $this->premiumData["coverage_type"] = $this->quotationData["coverage_type"];
        $this->premiumData["area_of_cover"] = $this->quotationData["area_of_cover"];
        $total_premium = 0;
        //calculate client only if individual
        if ($this->quotationData["individual_group"] == "I") {
            //echo "Individual<br>";
            $this->clientPremium = $this->calculatePerson($this->quotationData);

            $this->premiumData['per_client'] = $this->clientPremium['value'];
            $this->premiumData['client'] = $this->clientPremium['value'];
            $total_premium = $this->clientPremium['value'];

            //print_r($this->clientPremium);
        }
        $i = 0;
        $membersData = $db->query($this->membersSql);
        while ($member = $db->fetch_assoc($membersData)) {

            $memberData = $this->calculatePerson($member);
            $this->membersPremium[] = $memberData;
            //$this->premiumData["members"][$member['quotation_members_ID']]['per_member'] = $premData["value"];
            $this->premiumData["members"][$member['quotation_members_ID']] = $memberData["value"] * $member["total_members"];
            //print_r($memberData);
            //print_r($member);
            //echo "<br>\n\n\n\n";
            if ($memberData['free'] == 1) {
                //echo "Found here:".$member['quotation_members_ID']." Value:".$memberData["value"];
                $this->premiumData["members"]["free"] = $member['quotation_members_ID'];
            }

            $total_premium += $memberData["value"] * $member["total_members"];
            $i++;
            //echo $premData["value"]." -> ".$premData["total_members"]."<br>";
        }
        $this->premiumData['net_premium'] = $total_premium;
        $this->premiumData["country"] = $this->quotationData["country"];
        //get the stamps
        //check the country
        if ($this->quotationData["country"] == "CYP") {
            $this->premiumData["stamps"] = 2;
        } else if ($this->quotationData["country"] == "GRE") {
            $this->premiumData["stamps"] = 0;
        }
        if ($this->pricingVersion == '4-2019') {
            $this->premiumData["fees"] = 30;
        } else {
            $this->premiumData["fees"] = 60;
        }

        //make the total gross premium
        $this->premiumData["gross_premium"] = $this->premiumData["net_premium"]
            //+ $this->premiumData["stamps"]
            + $this->premiumData["fees"];
        $this->premiumData["gross_plus_stamps"] = $this->premiumData["gross_premium"] + $this->premiumData["stamps"];

        //make the frequency
        if ($this->quotationData["frequency_of_payment"] == "ANNUAL") {
            $this->premiumData["number_of_payments"] = 1;
            //stamps are included in the first installment only
            $this->premiumData["per_payment"] = $this->premiumData["gross_premium"];
            $this->premiumData["first_payment"] = $this->premiumData["per_payment"] + $this->premiumData["stamps"];
        } else if ($this->quotationData["frequency_of_payment"] == "SEMI - ANNUAL") {
            $this->premiumData["number_of_payments"] = 2;
                $this->premiumData["per_payment"] = round($this->premiumData["gross_premium"] / 2, 2);
                $this->premiumData["first_payment"] = $this->premiumData["per_payment"] + $this->premiumData["stamps"];

        } else if ($this->quotationData["frequency_of_payment"] == "QUARTERLY") {
            $this->premiumData["number_of_payments"] = 4;
                $this->premiumData["per_payment"] = round($this->premiumData["gross_premium"] / 4, 2);
                $this->premiumData["first_payment"] = $this->premiumData["per_payment"] + $this->premiumData["stamps"];

        } else if ($this->quotationData["frequency_of_payment"] == "MONTHLY") {
            $this->premiumData["number_of_payments"] = 12;
                $this->premiumData["per_payment"] = round($this->premiumData["gross_premium"] / 12, 2);
                $this->premiumData["first_payment"] = $this->premiumData["per_payment"] + $this->premiumData["stamps"];
        }

        //print_r($this->clientPremium);
        //print_r($this->membersPremium);
        //print_r($this->premiumData);
    }

    public function getTotalPremium()
    {
        return $this->quotationData["gross_premium"];
    }

    public function getAllDataArray()
    {
        return $this->quotationData;
    }

    public function setFrequency($freq)
    {
        $this->frequencyOfPayment = '';
        unset($this->frequencyOfPayment);
        $this->frequencyOfPayment = $freq;
    }

    public function enableUnder10Free()
    {
        $this->under10Free = true;
    }

    public function disableUnder10Free()
    {
        $this->under10Free = false;
    }

    public function getQuotationUserID()
    {
        return $this->quotationData['user_id'];
    }

    public function getQuotationData()
    {
        return $this->quotationData;
    }

}