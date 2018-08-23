<?
include ("encoding_convert.php");
$string_to_UTF8 = new utf8();

class Sybase {

var $connection;
var $connection_success = 'no';
var $num_rows = 0;

public function Sybase() {

	if ($this->connection = odbc_connect("YDROTEST","dba","sql"))
		$this->connection_success = 'yes';
	else
		$this->connection_success = 'no';

}

public function query($sql) {

	if ($result = odbc_exec($this->connection,$sql)) {
		//$result2 = odbc_exec($this->connection,$sql);
		//odbc_fetch_row($result2);
		//$this->num_rows = odbc_num_rows($result2);
		return $result;
	}
	else {
		echo $sql;
	}

}

public function fetch_assoc($rs,$convert=0){
global $string_to_UTF8;

 if (odbc_fetch_row($rs)){
  $line=array("odbc_affected_rows"=>odbc_num_rows($rs));
  for($f=1;$f<=odbc_num_fields($rs);$f++){
   $fn=odbc_field_name($rs,$f);
   $fct=odbc_result($rs,$fn);
   if (is_string($fct)) {
   	//echo $string_to_UTF8->detect($fct);
	if ($convert != 0) {
   		$fct = $string_to_UTF8->convert($fct,'WINDOWS-1253','UTF-8');
	}
   }
   $newline=array($fn => $fct);
   $line=array_merge($line,$newline);
   //echo $f.": ".$fn."=".$fct."<br>";
  }

  return $line;
 }
 else{
  return false;
 }
}//function fetch_assoc

public function query_fetch($sql) {

	$result = $this->query($sql);
	return $this->fetch_assoc($result);

}

public function num_rows($result) {
	return $this->num_rows;
}

public function fix_date($date) {
	$res = explode("/",$date);
	return $res[2]."-".$res[1]."-".$res[0];
}//fix date

}

//$sybase = odbc_connect("YDROTESTDB","php","sql");
//=======================================================================================================================================





class insurance_queries {

public function agent_production_per_product_periods_old($year,$month_from,$month_to,$agent,$product,$product_way = 'LIKE') {

	$sql = "SELECT  
(".$year.") as clo_year,           
(".$month_from.") as clo_period_from,
(".$month_to.") as clo_period_to,
(-1 * sum (IF clo_year = inped_year AND clo_period_to = inped_period THEN inpolicyendorsement.inped_premium_debit_credit * inpolicyendorsement.inped_premium ELSE 0 ENDIF)) As clo_period_to_premium,
(-1 * sum(IF clo_year = inped_year AND inped_period <= clo_period_to AND inped_period >= clo_period_from THEN inpolicyendorsement.inped_premium_debit_credit * inpolicyendorsement.inped_premium ELSE 0 ENDIF)) As clo_ytd_premium,           
inag_agent_code As clo_sort1,           
inity_major_category As clo_sort2,           
inag_long_description As clo_desc1,           
(Select incd_long_description From inpcodes where incd_record_type = '01' And incd_record_code = inity_major_category ) As clo_desc2

FROM ininsurancetypes  
LEFT OUTER JOIN inmajorcodes  ON ininsurancetypes.inity_major_category = inmajorcodes.incd_record_code
LEFT OUTER JOIN inminorcodes  ON ininsurancetypes.inity_minor_category = inminorcodes.incd_record_code ,           
inpolicies ,           
inpolicyendorsement ,           
inagents ,           
ingeneralagents ,           
inpparam    

WHERE ( inpolicies.inpol_policy_serial = inpolicyendorsement.inped_policy_serial ) 
and          ( inpolicies.inpol_insurance_type_serial = ininsurancetypes.inity_insurance_type_serial ) 
and          ( ingeneralagents.inga_agent_serial = inpolicies.inpol_general_agent_serial ) 
and          ( inpolicies.inpol_agent_serial = inagents.inag_agent_serial ) 
and          ( ( inpolicyendorsement.inped_status = '1' ) 
and          ( inpparam.inpr_module_code = 'IN' ) ) 
AND  1=1  
AND (inped_year*100+inped_period) >= (clo_year*100+clo_period_from) 
AND (inped_year*100+inped_period) <= (clo_year*100+clo_period_to) 
AND clo_sort1 = '".$agent."' 
AND clo_sort2 ".$product_way." '".$product."' 

GROUP BY inpparam.inpr_module_code, clo_sort1, clo_desc1, clo_sort2, clo_desc2 
ORDER BY  clo_sort1 ASC , clo_sort2 ASC";
return $sql;
}

public function claims_details_per_claim() {
	return "SELECT  
inclaims.inclm_claim_number ,           
inpolicies.inpol_policy_number ,           
inagents.inag_agent_code ,           
inclients.incl_long_description ,           
inclients.incl_first_name ,           
initems.initm_item_code ,           
inclaims.inclm_process_status ,           
inclaims.inclm_date_of_event ,           
inclaims.inclm_status ,           
inclaims.inclm_open_date ,           
inagents.inag_agent_serial ,           
inclaims.inclm_claim_serial ,           
DATE('2009/06/30') as clo_as_at_date,           
space(060) as clo_sort1,           
space(060) as clo_sort2,           
space(060) as clo_sort3,           
space(180) as clo_desc1,           
space(180) as clo_desc2,           
space(180) as clo_desc3,           
IF clo_process_status IN ('C', 'W') THEN inclm_closed_period ELSE 0 ENDIF  AS clo_closed_period,           
IF clo_process_status IN ('C', 'W') THEN inclm_closed_year ELSE 0 ENDIF  AS clo_closed_year,           
COALESCE((Select sum(inirq_debit_credit * inirq_value) from inclaimrecpayrequests, inpcodes where inirq_claim_serial = inclaims.inclm_claim_serial AND inirq_proposed_pay_date <= clo_as_at_date AND inirq_status = '1' AND inirq_against_reserve = incd_pcode_serial AND incd_reserve_type = 'C6') ,0) as clo_amount_paid,           
COALESCE((Select sum(inirq_debit_credit * inirq_value) from inclaimrecpayrequests, inpcodes where inirq_claim_serial = inclaims.inclm_claim_serial AND inirq_proposed_pay_date <= clo_as_at_date AND inirq_status = '1' AND inirq_against_reserve = incd_pcode_serial AND incd_reserve_type = 'C5'),0) as clo_recoveries_recieved,           
COALESCE((Select sum(inirq_value * inirq_debit_credit) from inclaimrecpayrequests, inpcodes where inirq_claim_serial = inclaims.inclm_claim_serial AND inirq_proposed_pay_date <= clo_as_at_date AND inirq_status = '1' AND inirq_against_reserve = incd_pcode_serial AND incd_reserve_type = 'C6' AND incd_reset_on_recovery = 'N'), 0) as clo_payments_recovery_exp,           
COALESCE((Select sum(inclr_debit_credit * inclr_value) From inclaimreserves, inpcodes Where inclr_claim_serial = inclaims.inclm_claim_serial AND inclr_date <= clo_as_at_date AND inclr_status = '1' AND inclr_code_serial = incd_pcode_serial AND incd_reserve_type = 'C6'),0) as clo_estimated_reserve,           
COALESCE((Select sum(inclr_debit_credit * inclr_value) From inclaimreserves, inpcodes Where inclr_claim_serial = inclaims.inclm_claim_serial AND inclr_date <= clo_as_at_date AND inclr_status = '1' AND inclr_code_serial = incd_pcode_serial AND incd_reserve_type = 'C5'),0) as clo_estimated_recoveries,           
COALESCE((Select sum(inclr_debit_credit * inclr_value) From inclaimreserves, inpcodes Where inclr_claim_serial = inclaims.inclm_claim_serial AND inclr_date <= clo_as_at_date AND inclr_status = '1' AND inclr_code_serial = incd_pcode_serial AND incd_reserve_type = 'C6' AND incd_reset_on_recovery = 'N'),0) as clo_estimated_rec_expence_reserve,           
CASE inclm_process_status WHEN 'C' THEN IF clo_estimated_reserve - clo_amount_paid <> 0 THEN 'O' ELSE IF clo_estimated_recoveries - clo_recoveries_recieved <> 0 THEN 'R' ELSE 'C' ENDIF ENDIF WHEN 'R' THEN IF clo_estimated_reserve - clo_amount_paid - (clo_estimated_rec_expence_reserve - clo_payments_recovery_exp) <> 0 THEN 'O' ELSE 'R' ENDIF WHEN 'W' THEN IF clo_estimated_reserve - clo_amount_paid <> 0 OR clo_estimated_recoveries - clo_recoveries_recieved <> 0 THEN 'O' ELSE 'W' ENDIF WHEN 'O' THEN IF clo_estimated_reserve = 0 AND clo_estimated_recoveries = 0 THEN 'P' ELSE IF clo_estimated_reserve - clo_amount_paid = 0 AND clo_estimated_recoveries - clo_recoveries_recieved <> 0 THEN 'R' ELSE IF clo_estimated_reserve - clo_amount_paid = 0 AND clo_estimated_recoveries - clo_recoveries_recieved = 0 THEN 'C' ELSE 'O' ENDIF ENDIF ENDIF ELSE inclm_process_status END as clo_process_status FROM inclaims  LEFT OUTER JOIN initems  ON inclaims.inclm_item_serial = initems.initm_item_serial ,           
inclients ,           
inpolicies ,           
inagents ,           
ingeneralagents ,           
ininsurancetypes    

WHERE ( inpolicies.inpol_client_serial = inclients.incl_client_serial ) 
and          ( inclaims.inclm_policy_serial = inpolicies.inpol_policy_serial ) 
and          ( inpolicies.inpol_agent_serial = inagents.inag_agent_serial ) 
and          ( ingeneralagents.inga_agent_serial = inpolicies.inpol_general_agent_serial ) 
and          ( ininsurancetypes.inity_insurance_type_serial = inpolicies.inpol_insurance_type_serial ) 
and          ( ( inclaims.inclm_open_date <= clo_as_at_date ) 
and          ( inclaims.inclm_process_status <> 'I') 
and          ( inclaims.inclm_status <> 'D') )  
AND  1=1  
AND inclm_status in ('O','A','D') 
AND clo_process_status in ('P','O','W','R','C') 

AND (((clo_closed_year * 100) + clo_closed_period) >= (YEAR('2009/01/01') * 100) + MONTH('2009/01/01') OR clo_closed_year = 0) ORDER BY 28         ASC  ,inclm_claim_number ASC";

}

public function claims_oustanding_payments($asatdate,$from_period,$agent,$product,$product_way = 'LIKE',$extra_sql = '' ){
	return "SELECT  
inagents.inag_agent_code ,             
inagents.inag_agent_serial ,           
         
DATE('".$asatdate."') as clo_as_at_date,                 
IF clo_process_status IN ('C', 'W') THEN inclm_closed_period ELSE 0 ENDIF  AS clo_closed_period,           
IF clo_process_status IN ('C', 'W') THEN inclm_closed_year ELSE 0 ENDIF  AS clo_closed_year,           
COALESCE((Select sum(inirq_debit_credit * inirq_value) from inclaimrecpayrequests, inpcodes where inirq_claim_serial = inclaims.inclm_claim_serial AND inirq_proposed_pay_date <= clo_as_at_date AND inirq_status = '1' AND inirq_against_reserve = incd_pcode_serial AND incd_reserve_type = 'C6') ,0) as clo_amount_paid,           
COALESCE((Select sum(inirq_debit_credit * inirq_value) from inclaimrecpayrequests, inpcodes where inirq_claim_serial = inclaims.inclm_claim_serial AND inirq_proposed_pay_date <= clo_as_at_date AND inirq_status = '1' AND inirq_against_reserve = incd_pcode_serial AND incd_reserve_type = 'C5'),0) as clo_recoveries_recieved,           
COALESCE((Select sum(inirq_value * inirq_debit_credit) from inclaimrecpayrequests, inpcodes where inirq_claim_serial = inclaims.inclm_claim_serial AND inirq_proposed_pay_date <= clo_as_at_date AND inirq_status = '1' AND inirq_against_reserve = incd_pcode_serial AND incd_reserve_type = 'C6' AND incd_reset_on_recovery = 'N'), 0) as clo_payments_recovery_exp,           
COALESCE((Select sum(inclr_debit_credit * inclr_value) From inclaimreserves, inpcodes Where inclr_claim_serial = inclaims.inclm_claim_serial AND inclr_date <= clo_as_at_date AND inclr_status = '1' AND inclr_code_serial = incd_pcode_serial AND incd_reserve_type = 'C6'),0) as clo_estimated_reserve,           
COALESCE((Select sum(inclr_debit_credit * inclr_value) From inclaimreserves, inpcodes Where inclr_claim_serial = inclaims.inclm_claim_serial AND inclr_date <= clo_as_at_date AND inclr_status = '1' AND inclr_code_serial = incd_pcode_serial AND incd_reserve_type = 'C5'),0) as clo_estimated_recoveries,           
COALESCE((Select sum(inclr_debit_credit * inclr_value) From inclaimreserves, inpcodes Where inclr_claim_serial = inclaims.inclm_claim_serial AND inclr_date <= clo_as_at_date AND inclr_status = '1' AND inclr_code_serial = incd_pcode_serial AND incd_reserve_type = 'C6' AND incd_reset_on_recovery = 'N'),0) as clo_estimated_rec_expence_reserve,           
CASE inclm_process_status WHEN 'C' THEN IF clo_estimated_reserve - clo_amount_paid <> 0 THEN 'O' ELSE IF clo_estimated_recoveries - clo_recoveries_recieved <> 0 THEN 'R' ELSE 'C' ENDIF ENDIF WHEN 'R' THEN IF clo_estimated_reserve - clo_amount_paid - (clo_estimated_rec_expence_reserve - clo_payments_recovery_exp) <> 0 THEN 'O' ELSE 'R' ENDIF WHEN 'W' THEN IF clo_estimated_reserve - clo_amount_paid <> 0 OR clo_estimated_recoveries - clo_recoveries_recieved <> 0 THEN 'O' ELSE 'W' ENDIF WHEN 'O' THEN IF clo_estimated_reserve = 0 AND clo_estimated_recoveries = 0 THEN 'P' ELSE IF clo_estimated_reserve - clo_amount_paid = 0 AND clo_estimated_recoveries - clo_recoveries_recieved <> 0 THEN 'R' ELSE IF clo_estimated_reserve - clo_amount_paid = 0 AND clo_estimated_recoveries - clo_recoveries_recieved = 0 THEN 'C' ELSE 'O' ENDIF ENDIF ENDIF ELSE inclm_process_status END as clo_process_status 

FROM inclaims  
LEFT OUTER JOIN initems  ON inclaims.inclm_item_serial = initems.initm_item_serial ,           
inclients ,           
inpolicies ,           
inagents ,           
ingeneralagents ,           
ininsurancetypes    

WHERE ( inpolicies.inpol_client_serial = inclients.incl_client_serial ) 
and          ( inclaims.inclm_policy_serial = inpolicies.inpol_policy_serial ) 
and          ( inpolicies.inpol_agent_serial = inagents.inag_agent_serial ) 
and          ( ingeneralagents.inga_agent_serial = inpolicies.inpol_general_agent_serial ) 
and          ( ininsurancetypes.inity_insurance_type_serial = inpolicies.inpol_insurance_type_serial ) 
and          ( ( inclaims.inclm_open_date <= clo_as_at_date ) 
and          ( inclaims.inclm_process_status <> 'I') 
and          ( inclaims.inclm_status <> 'D') )  
AND  1=1  
AND inclm_status in ('O','A') 
AND clo_process_status in ('P','O','W','R','C') 
AND (((clo_closed_year * 100) + clo_closed_period) >= (YEAR('".substr($asatdate,0,4)."/".$from_period."/01') * 100) + MONTH('".substr($asatdate,0,4)."/".$from_period."/01') OR clo_closed_year = 0) 
AND inagents.inag_agent_code = '".$agent."'
AND LEFT(inclm_claim_number , '2') ".$product_way." '".$product."'
".$extra_sql."
ORDER BY inagents.inag_agent_code ASC";
}


public function claims_oustanding_payments_updated($asatdate,$from_period,$year,$agent,$product,$product_way = 'LIKE',$extra_sql = '' , $extra_select = ''){
	return "SELECT  
inagents.inag_agent_code ,             
inagents.inag_agent_serial ,           
         
DATE('".$asatdate."') as clo_as_at_date,                 
IF clo_process_status IN ('C', 'W') THEN inclm_closed_period ELSE 0 ENDIF  AS clo_closed_period,           
IF clo_process_status IN ('C', 'W') THEN inclm_closed_year ELSE 0 ENDIF  AS clo_closed_year,           
COALESCE((Select sum(inirq_debit_credit * inirq_value) from inclaimrecpayrequests, inpcodes where inirq_claim_serial = inclaims.inclm_claim_serial AND inirq_proposed_pay_date <= clo_as_at_date AND inirq_status = '1' AND inirq_against_reserve = incd_pcode_serial AND incd_reserve_type = 'C6') ,0) as clo_amount_paid,           
COALESCE((Select sum(inirq_debit_credit * inirq_value) from inclaimrecpayrequests, inpcodes where inirq_claim_serial = inclaims.inclm_claim_serial AND inirq_proposed_pay_date <= clo_as_at_date AND inirq_status = '1' AND inirq_against_reserve = incd_pcode_serial AND incd_reserve_type = 'C5'),0) as clo_recoveries_recieved,           
COALESCE((Select sum(inirq_value * inirq_debit_credit) from inclaimrecpayrequests, inpcodes where inirq_claim_serial = inclaims.inclm_claim_serial AND inirq_proposed_pay_date <= clo_as_at_date AND inirq_status = '1' AND inirq_against_reserve = incd_pcode_serial AND incd_reserve_type = 'C6' AND incd_reset_on_recovery = 'N'), 0) as clo_payments_recovery_exp,           
COALESCE((Select sum(inclr_debit_credit * inclr_value) From inclaimreserves, inpcodes Where inclr_claim_serial = inclaims.inclm_claim_serial AND inclr_date <= clo_as_at_date AND inclr_status = '1' AND inclr_code_serial = incd_pcode_serial AND incd_reserve_type = 'C6'),0) as clo_estimated_reserve,           
COALESCE((Select sum(inclr_debit_credit * inclr_value) From inclaimreserves, inpcodes Where inclr_claim_serial = inclaims.inclm_claim_serial AND inclr_date <= clo_as_at_date AND inclr_status = '1' AND inclr_code_serial = incd_pcode_serial AND incd_reserve_type = 'C5'),0) as clo_estimated_recoveries,           
COALESCE((Select sum(inclr_debit_credit * inclr_value) From inclaimreserves, inpcodes Where inclr_claim_serial = inclaims.inclm_claim_serial AND inclr_date <= clo_as_at_date AND inclr_status = '1' AND inclr_code_serial = incd_pcode_serial AND incd_reserve_type = 'C6' AND incd_reset_on_recovery = 'N'),0) as clo_estimated_rec_expence_reserve,           
CASE inclm_process_status WHEN 'C' THEN IF clo_estimated_reserve - clo_amount_paid <> 0 THEN 'O' ELSE IF clo_estimated_recoveries - clo_recoveries_recieved <> 0 THEN 'R' ELSE 'C' ENDIF ENDIF WHEN 'R' THEN IF clo_estimated_reserve - clo_amount_paid - (clo_estimated_rec_expence_reserve - clo_payments_recovery_exp) <> 0 THEN 'O' ELSE 'R' ENDIF WHEN 'W' THEN IF clo_estimated_reserve - clo_amount_paid <> 0 OR clo_estimated_recoveries - clo_recoveries_recieved <> 0 THEN 'O' ELSE 'W' ENDIF WHEN 'O' THEN IF clo_estimated_reserve = 0 AND clo_estimated_recoveries = 0 THEN 'P' ELSE IF clo_estimated_reserve - clo_amount_paid = 0 AND clo_estimated_recoveries - clo_recoveries_recieved <> 0 THEN 'R' ELSE IF clo_estimated_reserve - clo_amount_paid = 0 AND clo_estimated_recoveries - clo_recoveries_recieved = 0 THEN 'C' ELSE 'O' ENDIF ENDIF ENDIF ELSE inclm_process_status END as clo_process_status,
(SELECT indr_birth_date FROM indrivers WHERE indr_driver_serial = inclm_driver_serial)as clo_driver_birth_date,
(SELECT indr_permit_date FROM indrivers WHERE indr_driver_serial = inclm_driver_serial)as clo_driver_licence_date,
COALESCE(fn_evaluate_age(clo_driver_birth_date,inclm_date_of_event),0)as clo_driver_age,
COALESCE(fn_evaluate_age(clo_driver_licence_date,inclm_date_of_event),0)as clo_driver_licence_years,
inclm_claim_serial
".$extra_select."

FROM inclaims  
LEFT OUTER JOIN initems  ON inclaims.inclm_item_serial = initems.initm_item_serial ,           
inclients ,           
inpolicies ,           
inagents ,           
ingeneralagents ,           
ininsurancetypes    

WHERE ( inpolicies.inpol_client_serial = inclients.incl_client_serial ) 
and          ( inclaims.inclm_policy_serial = inpolicies.inpol_policy_serial ) 
and          ( inpolicies.inpol_agent_serial = inagents.inag_agent_serial ) 
and          ( ingeneralagents.inga_agent_serial = inpolicies.inpol_general_agent_serial ) 
and          ( ininsurancetypes.inity_insurance_type_serial = inpolicies.inpol_insurance_type_serial ) 
and          ( ( inclaims.inclm_open_date <= clo_as_at_date ) 
and          ( inclaims.inclm_process_status <> 'I') 
and          ( inclaims.inclm_status <> 'D') )  
AND  1=1  
AND inclm_status in ('O','A') 
AND clo_process_status in ('P','O','W','R','C') 
AND (((clo_closed_year * 100) + clo_closed_period) >= (YEAR('".$year."/".$from_period."/01') * 100) + MONTH('".$year."/".$from_period."/01') OR clo_closed_year = 0) 
AND inagents.inag_agent_code LIKE '".$agent."'
AND LEFT(inclm_claim_number , '2') ".$product_way." '".$product."'
".$extra_sql."
ORDER BY inagents.inag_agent_code ASC";

}

public function agent_production_per_product_periods($year,$month_from,$month_to,$agent,$klados,$klados_way = 'LIKE',$product = '%%',$extra_sql = '') {

	$sql = "SELECT  
('".$year."') as clo_year,           
('".$month_from."') as clo_period_from,
('".$month_to."') as clo_period_to,
(-1 * sum (IF clo_year = inped_year AND clo_period_to = inped_period THEN inpolicyendorsement.inped_premium_debit_credit * inpolicyendorsement.inped_premium ELSE 0 ENDIF)) As clo_period_to_premium,
(-1 * sum(IF clo_year = inped_year AND inped_period <= clo_period_to AND inped_period >= clo_period_from THEN inpolicyendorsement.inped_premium_debit_credit * inpolicyendorsement.inped_premium ELSE 0 ENDIF)) As clo_ytd_premium,
(-1 * sum(inpolicyendorsement.inped_premium_debit_credit * inped_mif)) As clo_period_mif,
(-1 * sum(inpolicyendorsement.inped_premium_debit_credit * inped_fees)) AS clo_period_fees,
(-1 * sum(inpolicyendorsement.inped_premium_debit_credit * inped_stamps)) AS clo_period_stamps,
SUM((SELECT SUM(intrd_value * intrd_debit_credit) FROM intransactiondetails WHERE intrd_policy_serial = inpolicyendorsement.inped_policy_serial AND intrd_endorsement_serial = inpolicyendorsement.inped_endorsement_serial AND intrd_transaction_type IN ('C0','C1','C2','C3','C4','C5') AND intrd_owner = 'O'))as clo_commission,
inag_agent_code As clo_sort1,           
inity_major_category As clo_sort2,  
inity_insurance_type AS clo_sort3,
inpol_policy_number,
inpol_period_starting_date,
inpol_process_status,
inpol_status,

         
inag_long_description As clo_desc1,           
(Select incd_long_description From inpcodes where incd_record_type = '01' And incd_record_code = inity_major_category ) As clo_desc2

FROM ininsurancetypes  
LEFT OUTER JOIN inmajorcodes  ON ininsurancetypes.inity_major_category = inmajorcodes.incd_record_code
LEFT OUTER JOIN inminorcodes  ON ininsurancetypes.inity_minor_category = inminorcodes.incd_record_code ,           
inpolicies ,           
inpolicyendorsement ,           
inagents ,           
ingeneralagents ,           
inpparam    

WHERE ( inpolicies.inpol_policy_serial = inpolicyendorsement.inped_financial_policy_abs ) 
and          ( inpolicies.inpol_insurance_type_serial = ininsurancetypes.inity_insurance_type_serial ) 
and          ( ingeneralagents.inga_agent_serial = inpolicies.inpol_general_agent_serial ) 
and          ( inpolicies.inpol_agent_serial = inagents.inag_agent_serial ) 
and          ( ( inpolicyendorsement.inped_status = '1' ) 
and          ( inpparam.inpr_module_code = 'IN' ) ) 
AND  1=1  
AND (inped_year*100+inped_period) >= (clo_year*100+clo_period_from) 
AND (inped_year*100+inped_period) <= (clo_year*100+clo_period_to) 
AND clo_sort1 LIKE '".$agent."' 
AND clo_sort2 ".$klados_way." '".$klados."'
AND clo_sort3 LIKE '".$product."'
".$extra_sql."

GROUP BY inpparam.inpr_module_code, clo_sort1, clo_desc1, clo_sort2, clo_desc2 ,clo_sort3,inpol_policy_number,inpol_period_starting_date,inpol_process_status,inpol_status
ORDER BY  clo_sort1 ASC , clo_sort2 ASC , clo_sort3 ASC";
return $sql;

}//function agent_production_per_product_periods


public function agent_production_per_product_periods_process_status($year,$month_from,$month_to,$agent,$klados,$klados_way = 'LIKE',$product = '%%') {

$sql = "  
SELECT  inpolicies.inpol_policy_number ,
inclients.incl_first_name ,
inclients.incl_long_description ,
inagents.inag_agent_code ,
inagents.inag_long_description ,
inpolicies.inpol_policy_serial ,
inag_agent_code as clo_sort1,
space(060) as clo_sort2,
space(060) as clo_sort3,
ingeneralagents.inga_agent_code ,
ingeneralagents.inga_long_description ,
inag_short_description as clo_desc1,
space(180) as clo_desc2,
space(180) as clo_desc3,
IF inped_endorsement_serial = inpol_last_cancellation_endorsement_serial THEN IF inpol_status = 'A' AND inpol_replaced_by_policy_serial = 0 AND inpol_cancellation_date IS NOT NULL THEN 'C' ELSE 'L' ENDIF ELSE inpol_process_status ENDIF as clo_process_status,
((inped_fees * inped_premium_debit_credit) * -1) + IF clo_process_status = 'E' THEN COALESCE((SELECT ((a.inped_fees * a.inped_premium_debit_credit) * -1) FROM inpolicyendorsement a,
inpolicies b WHERE a.inped_policy_serial = b.inpol_policy_serial AND a.inped_endorsement_serial = b.inpol_last_cancellation_endorsement_serial AND b.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial), 0) ELSE 0 ENDIF as clo_fees,
((inped_stamps * inped_premium_debit_credit) * -1) + IF clo_process_status = 'E' THEN COALESCE((SELECT ((a.inped_stamps * a.inped_premium_debit_credit) * -1) FROM inpolicyendorsement a,
inpolicies b WHERE a.inped_policy_serial = b.inpol_policy_serial AND a.inped_endorsement_serial = b.inpol_last_cancellation_endorsement_serial AND b.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial), 0) ELSE 0 ENDIF as clo_stamps,
((inped_x_premium * inped_premium_debit_credit) * -1) + IF clo_process_status = 'E' THEN COALESCE((SELECT ((a.inped_x_premium * a.inped_premium_debit_credit) * -1) FROM inpolicyendorsement a, inpolicies b WHERE a.inped_policy_serial = b.inpol_policy_serial AND a.inped_endorsement_serial = b.inpol_last_cancellation_endorsement_serial AND b.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial), 0) ELSE 0 ENDIF as clo_x_premium,
COALESCE((SELECT LIST(DISTINCT intrh_document_number) FROM intransactiondetails, intransactionheaders WHERE intrd_policy_serial = inpol_policy_serial AND intrd_endorsement_serial = IF clo_process_status = 'C' THEN inpol_last_cancellation_endorsement_serial ELSE IF clo_process_status <> 'L' THEN inpol_last_endorsement_serial ELSE 0 ENDIF ENDIF AND intrd_trh_auto_serial = intrh_auto_serial AND intrd_related_type IN ('A', 'C')), '') As clo_document_number,
SPACE(1) as CLO_PR,
-1 * IF clo_process_status = 'E'  AND inpol_status IN ('O', 'C') THEN COALESCE((SELECT SUM(inplg_return_premium) FROM inpparam, inpolicies oldpol JOIN inpolicyloadings ON oldpol.inpol_policy_serial = inplg_policy_serial JOIN inloadings ON inplg_loading_serial = inldg_loading_serial WHERE UPPER(inpr_module_code) = 'IN' AND oldpol.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial AND inldg_loading_type <> 'X' AND ((inldg_loading_type = oldpol.inpol_cover) OR (oldpol.inpol_cover = 'B' AND inpr_act_in_fire_theft = 'N') OR (oldpol.inpol_cover = 'C' AND inpr_act_in_comprehensive = 'N'))), 0) ELSE 0 ENDIF as clo_refund_outstanding_endorsement,
((inped_premium * inped_premium_debit_credit) * -1) + IF clo_process_status = 'E' THEN IF inpol_status IN ('C','O') THEN clo_refund_outstanding_endorsement ELSE COALESCE((SELECT ((a.inped_premium * a.inped_premium_debit_credit) * -1) FROM inpolicyendorsement a, inpolicies b WHERE a.inped_policy_serial = b.inpol_policy_serial AND a.inped_endorsement_serial = b.inpol_last_cancellation_endorsement_serial AND b.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial), 0) ENDIF ELSE 0 ENDIF as clo_premium,
SPACE(2) as CLO_MF,
((inped_mif * inped_premium_debit_credit) * -1) + IF clo_process_status = 'E' THEN IF inpol_status IN ('C','O') THEN ROUND(clo_refund_outstanding_mif_pr_endorsement * CASE clo_replacing_policy_pstatus WHEN 'N' THEN inity_mif_new WHEN 'R' THEN inity_mif_renewal ELSE inity_mif_endorsment END / 100, 2) ELSE COALESCE((SELECT ((a.inped_mif * a.inped_premium_debit_credit) * -1) FROM inpolicyendorsement a, inpolicies b WHERE a.inped_policy_serial = b.inpol_policy_serial AND a.inped_endorsement_serial = b.inpol_last_cancellation_endorsement_serial AND b.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial), 0) ENDIF ELSE 0 ENDIF as clo_mif,
IF clo_process_status = 'E' THEN (SELECT oldpol.inpol_process_status FROM inpolicies oldpol WHERE oldpol.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial) ELSE '' ENDIF as clo_replacing_policy_pstatus,
-1 * IF clo_process_status = 'E'  AND inpol_status IN ('O', 'C') THEN COALESCE((SELECT SUM(inplg_return_premium) FROM inpparam, inpolicies oldpol JOIN inpolicyloadings ON oldpol.inpol_policy_serial = inplg_policy_serial JOIN inloadings ON inplg_loading_serial = inldg_loading_serial WHERE UPPER(inpr_module_code) = 'IN' AND oldpol.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial AND inldg_loading_type <> 'X' AND inldg_mif_applied <> 'N' AND ((inldg_loading_type = oldpol.inpol_cover) OR (oldpol.inpol_cover = 'B' AND inpr_act_in_fire_theft = 'N') OR (oldpol.inpol_cover = 'C' AND inpr_act_in_comprehensive = 'N'))), 0) ELSE 0 ENDIF as clo_refund_outstanding_mif_pr_endorsement,
SPACE(3) as CLO_COMMISSIONS,
COALESCE((SELECT SUM(intrd_value * intrd_debit_credit)  * -1 FROM intransactiondetails WHERE intrd_policy_serial = inpol_policy_serial AND (intrd_endorsement_serial = IF clo_process_status = 'C' THEN inpol_last_cancellation_endorsement_serial ELSE IF clo_process_status <> 'L' THEN inpol_last_endorsement_serial ELSE 0 ENDIF ENDIF) AND COALESCE(intrd_claim_serial, 0) = 0 AND intrd_transaction_type IN ('C0', 'C1','C2','C3','C4','C5','CF') AND intrd_status <> '9' /*Excl.Deleted*/ AND intrd_related_type = IF inga_branch_agent = 'B' THEN 'A' ELSE 'G' ENDIF), 0) as clo_commission_charge,
IF clo_process_status = 'E' THEN COALESCE((SELECT SUM(intrd_value * intrd_debit_credit) FROM intransactiondetails, inpolicies a WHERE a.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial AND intrd_policy_serial = a.inpol_policy_serial AND intrd_endorsement_serial = a.inpol_last_cancellation_endorsement_serial AND COALESCE(intrd_claim_serial, 0) = 0 AND intrd_transaction_type IN ('C0', 'C1','C2','C3','C4','C5','CF') AND intrd_status <> '9' /*Excl.Deleted*/ AND intrd_related_type = IF inga_branch_agent = 'B' THEN 'A' ELSE 'G' ENDIF), 0) ELSE 0 ENDIF as clo_commission_reduction,
clo_commission_charge - clo_commission_reduction as clo_commission_net,
COALESCE((SELECT FIRST intrd_related_serial FROM intransactiondetails WHERE intrd_policy_serial = inpol_policy_serial AND (intrd_endorsement_serial = IF clo_process_status = 'C' THEN inpol_last_cancellation_endorsement_serial ELSE inpol_last_endorsement_serial ENDIF) AND COALESCE(intrd_claim_serial, 0) = 0 AND intrd_status <> '9' /*Excl.Deleted*/ AND intrd_transaction_type IN ('C0', 'C1','C2','C3','C4','C5','CF') AND intrd_related_type = 'A'), 0) as clo_commission_agent,
IF clo_commission_agent <> inpol_agent_serial THEN (SELECT a.inag_agent_code FROM inagents a WHERE a.inag_agent_serial = clo_commission_agent) ELSE '' ENDIF as clo_commission_agent_code,
inity_major_category,
inity_insurance_type


FROM 
inpolicies  LEFT OUTER JOIN intemplates  ON inpolicies.inpol_template_serial = intemplates.intmpl_template_serial ,
inclients ,
inagents ,
inpolicyendorsement ,
ininsurancetypes ,
ingeneralagents    

WHERE ( inpolicies.inpol_client_serial = inclients.incl_client_serial ) 
and          ( ininsurancetypes.inity_insurance_type_serial = inpolicies.inpol_insurance_type_serial ) 
and          ( inpolicies.inpol_general_agent_serial = ingeneralagents.inga_agent_serial ) 
and          ( inpolicies.inpol_agent_serial = inagents.inag_agent_serial ) 
and          ( inpolicies.inpol_policy_serial = inpolicyendorsement.inped_policy_serial ) 
and          ((inpolicyendorsement.inped_endorsement_serial = inpolicies.inpol_last_endorsement_serial) 
or           (inpolicyendorsement.inped_endorsement_serial = inpolicies.inpol_last_cancellation_endorsement_serial 
and          (   inpolicies.inpol_replaced_by_policy_serial = 0)) /* CANCELLATION OR LAPSED */ )    

AND  1=1  


AND inpol_status IN ('A','N') 
AND (inped_year*100+inped_period)>=(".$year."*100+".$month_from.") 
AND (inped_year*100+inped_period)<=(".$year."*100+".$month_to.") 
AND clo_sort1 = '".$agent."' 
AND inity_major_category ".$klados_way." '".$klados."'
AND inity_insurance_type LIKE '".$product."'

ORDER BY  
clo_sort1 ASC ,
inpol_policy_number ASC ";
return $sql;

//AND LEFT(clo_process_status, 1) IN ('".$status."') 

}//function agent_production_per_product_periods_process_status


function agent_production_per_product_periods_per_loading($year,$month_from,$month_to,$agent,$klados,$klados_way = 'LIKE',$product = '%%',$extra_sql = '') {

$sql = "
SELECT
inpol_policy_serial
,inldg_loading_serial
,inpol_policy_period as clo_policy_period,inpol_policy_year as clo_policy_year
,inity_insurance_type
,inldg_loading_code
,inldg_long_description
,-1 * SUM((if inped_premium_debit_credit = -1 then inplg_period_premium else inplg_return_premium endif)* inped_premium_debit_credit)as clo_total_premium
,incd_ldg_rsrv_under_reinsurance
,inldg_commission_assigned
FROM
inpolicies
JOIN inpolicyloadings ON inplg_policy_serial = inpol_policy_serial
JOIN inloadings ON inldg_loading_serial = inplg_loading_serial
JOIN ininsurancetypes ON inity_insurance_type_serial = inpol_insurance_type_serial
JOIN inpolicyendorsement ON inped_policy_serial = inpol_policy_serial
LEFT OUTER JOIN inpcodes ON inldg_claim_reserve_group = incd_pcode_serial
JOIN inagents ON inag_agent_serial = inpol_agent_serial

WHERE
1=1
AND inped_year = '".$year."'
AND inped_period BETWEEN '".$month_from."' AND '".$month_to."'
AND inity_insurance_type ".$klados_way." '".$klados."'
AND inag_agent_code = '".$agent."'
AND inity_insurance_type LIKE '".$product."'
".$extra_sql."

AND inped_status = 1
GROUP BY inpol_policy_serial,clo_policy_year,clo_policy_period,inldg_loading_serial
,inldg_loading_code,inldg_long_description,inity_insurance_type,incd_ldg_rsrv_under_reinsurance,inldg_commission_assigned

ORDER BY inity_insurance_type,inpol_policy_serial,inldg_loading_serial";

return $sql;
}

}

?>