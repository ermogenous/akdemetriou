<?

function policies_based_on_drivers_ages($policy_starting_date,$policy_expiry_date,$year) {

return "SELECT POLICY_NUMBER, 
LIST(MIN_POLICY_SERIAL), /* Drivers Existing ONLY ON ONE OF THE TWO PHASES (OR Additional Drivers on the 2nd phase) */
            SUM(POLICY_PHASES_COUNT) / COUNT() AS NO_OF_PHASES,
            COUNT(DISTINCT DRIVER_SERIAL) AS TOTAL_DRIVERS,
            LIST(DISTINCT DRIVER_ID +'('+  CAST(MIN_DRIVER_AGE AS CHAR) +')') AS DRIVERS_LIST,
            COUNT(IF MIN_DRIVER_AGE < 25 THEN DRIVER_SERIAL ELSE NULL ENDIF) as DRIVERS_UNDER_25,

            LIST(DISTINCT   CAST(MIN_DRIVER_AGE AS CHAR)) AS all_ages

FROM (SELECT inpol_policy_number AS POLICY_NUMBER,
                                    MIN(inpol_policy_serial) as MIN_POLICY_SERIAL,
                                    Count(DISTINCT inpol_policy_serial) AS POLICY_PHASES_COUNT,
                                    indr_driver_serial AS DRIVER_SERIAL,
                                    indr_identity AS DRIVER_ID,
                                    MIN(fn_evaluate_age(indr_birth_date, inpol_period_starting_date)) as MIN_DRIVER_AGE
                        FROM inpolicies 
                        JOIN inpolicyendorsement ON inpol_policy_serial = inped_policy_serial AND inpol_last_endorsement_serial = inped_endorsement_serial
                        JOIN ininsurancetypes ON inpol_insurance_type_serial = inity_insurance_type_serial
                        LEFT OUTER JOIN inpolicydrivers ON inpol_policy_serial = inpdr_policy_serial
                        LEFT OUTER JOIN indrivers ON indr_driver_serial = inpdr_driver_serial
                        WHERE COALESCE(inpol_cancellation_date, inpol_expiry_date) >= '".$policy_starting_date."' //AND '2010-03-31'
                        AND inpol_starting_date <= '".$policy_expiry_date."'
                        AND inped_year >= ".($year-1)." AND inped_status = '1' /* Posted */
                        AND inity_insurance_form = 'M' AND inity_insurance_type <> '19TC'
                        GROUP BY inpol_policy_number, indr_driver_serial, indr_identity
                        ORDER BY inpol_policy_number, indr_identity) AS DYNAMIC_TABLE
GROUP BY POLICY_NUMBER//, MIN_POLICY_SERIAL
ORDER BY POLICY_NUMBER;";

}

function policies_based_on_drivers_experience($policy_starting_date,$policy_expiry_date,$year) {

return "SELECT POLICY_NUMBER, 
LIST(MIN_POLICY_SERIAL), /* Drivers Existing ONLY ON ONE OF THE TWO PHASES (OR Additional Drivers on the 2nd phase) */
            SUM(POLICY_PHASES_COUNT) / COUNT() AS NO_OF_PHASES,
            COUNT(DISTINCT DRIVER_SERIAL) AS TOTAL_DRIVERS,
            LIST(DISTINCT DRIVER_ID +'('+  CAST(MIN_DRIVER_EXP AS CHAR) +')') AS DRIVERS_LIST,
            COUNT(IF MIN_DRIVER_EXP < 25 THEN DRIVER_SERIAL ELSE NULL ENDIF) as DRIVERS_UNDER_25,

            LIST(DISTINCT   CAST(MIN_DRIVER_EXP AS CHAR)) AS all_exp

FROM (SELECT inpol_policy_number AS POLICY_NUMBER,
                                    MIN(inpol_policy_serial) as MIN_POLICY_SERIAL,
                                    Count(DISTINCT inpol_policy_serial) AS POLICY_PHASES_COUNT,
                                    indr_driver_serial AS DRIVER_SERIAL,
                                    indr_identity AS DRIVER_ID,
                                    MIN(fn_evaluate_age(indr_permit_date, inpol_period_starting_date)) as MIN_DRIVER_EXP
                        FROM inpolicies 
                        JOIN inpolicyendorsement ON inpol_policy_serial = inped_policy_serial AND inpol_last_endorsement_serial = inped_endorsement_serial
                        JOIN ininsurancetypes ON inpol_insurance_type_serial = inity_insurance_type_serial
                        LEFT OUTER JOIN inpolicydrivers ON inpol_policy_serial = inpdr_policy_serial
                        LEFT OUTER JOIN indrivers ON indr_driver_serial = inpdr_driver_serial
                        WHERE COALESCE(inpol_cancellation_date, inpol_expiry_date) >= '".$policy_starting_date."' //AND '2010-03-31'
                        AND inpol_starting_date <= '".$policy_expiry_date."'
                        AND inped_year >= ".($year-1)." AND inped_status = '1' /* Posted */
                        AND inity_insurance_form = 'M' AND inity_insurance_type <> '19TC'
                        GROUP BY inpol_policy_number, indr_driver_serial, indr_identity
                        ORDER BY inpol_policy_number, indr_identity) AS DYNAMIC_TABLE
GROUP BY POLICY_NUMBER//, MIN_POLICY_SERIAL
ORDER BY POLICY_NUMBER;";

}

function claims_create_temp_table($as_at_date,$from_year,$from_period,$open_date_from,$open_date_to,$select_temp_sql,$agent='',$insurance_type='') {
	
	if ($agent != "") {
		$agent_sql = "AND inag_agent_code = '".$agent."'";
	}
	if ($insurance_type != "") {
		$insurance_type_sql = "AND inity_insurance_type = '".$insurance_type."'";	
	}
	
$sql = "SELECT  
inag_agent_code,
inity_major_category ,
inity_insurance_type,
inag_long_description,
( Select incd_long_description From inpcodes where incd_record_type = '01' And incd_record_code = inity_major_category ) as clo_desc2,
inclm_claim_serial ,
inclm_claim_number ,
DATE('".$from_year."/".$from_period."/01') as clo_from_date,
DATE('".$as_at_date."') as clo_as_at_date,
(SELECT inpr_financial_period FROM inpparam WHERE UPPER(inpr_module_code) = 'IN') as clo_financial_period,
(SELECT inpr_financial_year FROM inpparam WHERE UPPER(inpr_module_code) = 'IN') as clo_financial_year,
inclm_process_status ,
inclm_open_date ,
IF clo_process_status IN ('C', 'W') THEN YEAR(MAX(incvsdt_operation_date)) ELSE 0 ENDIF AS clo_closed_year,           
IF clo_process_status IN ('C', 'W') THEN MONTH(MAX(incvsdt_operation_date)) ELSE 0 ENDIF AS clo_closed_period,           
SUM(IF incvsdt_line_type = 'E' AND incvsdt_line_sub_type = 'C6' THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF) as clo_estimated_reserve_as_at_date,           
SUM(IF incvsdt_line_type = 'E' AND incvsdt_line_sub_type = 'C5' THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF) as clo_estimated_recoveries_as_at_date,           
SUM(IF incvsdt_line_type = 'S' AND incvsdt_line_sub_type = 'C6' THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF) as clo_paid_as_at_date,           
SUM(IF incvsdt_line_type = 'S' AND incvsdt_line_sub_type = 'C5' THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF) as clo_recoveries_as_at_date,           
SUM(IF incvsdt_line_type = 'E' AND incvsdt_line_sub_type = 'C6' AND incvsdt_reset_on_recovery = 'N' THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF) as clo_reserve_pay_recovery_exp,           
SUM(IF incvsdt_line_type = 'S' AND incvsdt_line_sub_type = 'C6' AND incvsdt_reset_on_recovery = 'N' THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF) as clo_payments_recovery_exp,           
SUM(IF incvsdt_line_type = 'E' AND incvsdt_line_sub_type = 'C6' AND incvsdt_operation_date < clo_from_date THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF) as clo_bf_reserves,           
SUM(IF incvsdt_line_type = 'E' AND incvsdt_line_sub_type = 'C5' AND incvsdt_operation_date < clo_from_date THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF) as clo_bf_est_recoveries,           
SUM(IF incvsdt_line_type = 'S' AND incvsdt_line_sub_type = 'C6' AND incvsdt_operation_date < clo_from_date THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF) as clo_bf_payments,           
SUM(IF incvsdt_line_type = 'S' AND incvsdt_line_sub_type = 'C5' AND incvsdt_operation_date < clo_from_date THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF) as clo_bf_recoveries,           
SUM(IF incvsdt_line_type = 'E' AND incvsdt_line_sub_type = 'C6' AND incvsdt_initial_re_estimation = 'I' AND (incvsdt_operation_date BETWEEN clo_from_date AND clo_as_at_date) THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF)  as clo_initial_res_for_payments,           
SUM(IF incvsdt_line_type = 'E' AND incvsdt_line_sub_type = 'C6' AND incvsdt_initial_re_estimation = 'R' AND (incvsdt_operation_date BETWEEN clo_from_date AND clo_as_at_date) THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF)  as clo_reest_res_for_payments,           
SUM(IF incvsdt_line_type = 'E' AND incvsdt_line_sub_type = 'C5' AND (incvsdt_operation_date BETWEEN clo_from_date AND clo_as_at_date) THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF)  as clo_est_recoveries_period,           
SUM(IF incvsdt_line_type = 'S' AND incvsdt_line_sub_type = 'C6' AND (incvsdt_operation_date BETWEEN clo_from_date AND clo_as_at_date) THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF)  as clo_period_payments,           
SUM(IF incvsdt_line_type = 'S' AND incvsdt_line_sub_type = 'C5' AND (incvsdt_operation_date BETWEEN clo_from_date AND clo_as_at_date) THEN incvsdt_debit_credit * incvsdt_value ELSE 0 ENDIF)  as clo_period_recoveries,           
CASE WHEN inclm_process_status = 'I' THEN 'I' WHEN clo_estimated_reserve_as_at_date = 0 THEN IF COUNT(IF incvsdt_line_sub_type <> '' THEN 1 ELSE NULL ENDIF) = 0 THEN 'P' ELSE 'W' ENDIF WHEN clo_estimated_reserve_as_at_date - clo_paid_as_at_date = 0 AND clo_estimated_recoveries_as_at_date - clo_recoveries_as_at_date = 0 THEN 'C' WHEN (clo_estimated_reserve_as_at_date - clo_reserve_pay_recovery_exp) - (clo_paid_as_at_date - clo_payments_recovery_exp) = 0 AND clo_estimated_recoveries_as_at_date - clo_recoveries_as_at_date <> 0 THEN 'R' ELSE 'O' END as clo_process_status,    

//totals
clo_period_payments as clo_total_payments,
(clo_bf_reserves + clo_initial_res_for_payments + clo_reest_res_for_payments - clo_paid_as_at_date)as clo_total_os_reserve,
clo_period_recoveries as clo_total_paid_recoveries,
(clo_bf_est_recoveries + clo_est_recoveries_period - clo_recoveries_as_at_date)as clo_total_os_recoveries

into #temp

FROM inclaims ,           
inpolicies ,           
inclients ,           
ininsurancetypes ,           
inagents ,           
ingeneralagents ,           
inclaims_asat_date     

WHERE 
( inpol_policy_serial = inclm_policy_serial ) 
and          ( inclients.incl_client_serial = inpol_client_serial ) 
and          ( inpol_insurance_type_serial = ininsurancetypes.inity_insurance_type_serial ) 
and          ( inag_agent_serial = inpol_agent_serial ) 
and          ( inpol_general_agent_serial = ingeneralagents.inga_agent_serial ) 
and          ( inclm_claim_serial = incvsdt_claim_serial ) 
and          ((inclm_process_status <> 'I' /* Exclude Initial */) 
And          (inclm_status <> 'D' /* Exclude Deleted */) 
and          (inclm_open_date <= clo_as_at_date)) 
And          ( incvsdt_operation_date <= clo_as_at_date )  
AND  1=1  
AND inclm_status in ('O','A','D') 
AND inclm_open_date >='".$open_date_from."' 
AND inclm_open_date <='".$open_date_to."' 
".$agent_sql."
".$insurance_type_sql."

GROUP BY inclm_claim_serial ,           
inclm_claim_number ,           
inclm_process_status ,           
inclm_open_date   , 
inag_agent_code, 
inag_long_description, 
inity_major_category, 
inity_insurance_type,
clo_desc2,
inclm_open_date 
HAVING  1=1  
AND clo_process_status in ('P','O','W','R','C') 
AND (((clo_closed_year * 100) + clo_closed_period) >= (YEAR('".$from_year."/".$from_period."/01') * 100) + MONTH('".$from_year."/".$from_period."/01') OR clo_closed_year = 0) 

ORDER BY  inag_agent_code ASC, inity_major_category ASC,inclm_open_date ASC

".$select_temp_sql;	
	
return $sql;	

}

function get_policy_premium_commissions($policy_serial,$extra_where='',$extra_from='',$extra_order='') {
	if ($policy_serial != "") {
		$policy_serial_sql = "AND inpol_policy_serial = ".$policy_serial;	
	}else {
		$policy_serial_sql = "";
	}
$sql = "
SELECT  
inpol_policy_serial,
inpol_status,
inpol_process_status,
IF inped_endorsement_serial = inpol_last_cancellation_endorsement_serial THEN IF inpol_status = 'A' AND inpol_replaced_by_policy_serial = 0 AND inpol_cancellation_date IS NOT NULL THEN 'C' ELSE 'L' ENDIF ELSE inpol_process_status ENDIF as clo_process_status,           
((inped_fees * inped_premium_debit_credit) * -1) + IF clo_process_status = 'E' THEN COALESCE((SELECT ((a.inped_fees * a.inped_premium_debit_credit) * -1) FROM inpolicyendorsement a, inpolicies b WHERE a.inped_policy_serial = b.inpol_policy_serial AND a.inped_endorsement_serial = b.inpol_last_cancellation_endorsement_serial AND b.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial), 0) ELSE 0 ENDIF as clo_fees,           
((inped_stamps * inped_premium_debit_credit) * -1) + IF clo_process_status = 'E' THEN COALESCE((SELECT ((a.inped_stamps * a.inped_premium_debit_credit) * -1) FROM inpolicyendorsement a, inpolicies b WHERE a.inped_policy_serial = b.inpol_policy_serial AND a.inped_endorsement_serial = b.inpol_last_cancellation_endorsement_serial AND b.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial), 0) ELSE 0 ENDIF as clo_stamps,           
((inped_x_premium * inped_premium_debit_credit) * -1) + IF clo_process_status = 'E' THEN COALESCE((SELECT ((a.inped_x_premium * a.inped_premium_debit_credit) * -1) FROM inpolicyendorsement a, inpolicies b WHERE a.inped_policy_serial = b.inpol_policy_serial AND a.inped_endorsement_serial = b.inpol_last_cancellation_endorsement_serial AND b.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial), 0) ELSE 0 ENDIF as clo_x_premium,           
COALESCE((SELECT LIST(DISTINCT intrh_document_number) FROM intransactiondetails, intransactionheaders WHERE intrd_policy_serial = inpol_policy_serial AND intrd_endorsement_serial = IF clo_process_status = 'C' THEN inpol_last_cancellation_endorsement_serial ELSE IF clo_process_status <> 'L' THEN inpol_last_endorsement_serial ELSE 0 ENDIF ENDIF AND intrd_trh_auto_serial = intrh_auto_serial AND intrd_related_type IN ('A', 'C')), '') As clo_document_number,
-1 * IF clo_process_status = 'E'  AND inpol_status IN ('O', 'C') THEN COALESCE((SELECT SUM(inplg_return_premium) FROM inpolicies oldpol JOIN inpolicyloadings ON oldpol.inpol_policy_serial = inplg_policy_serial JOIN inloadings ON inplg_loading_serial = inldg_loading_serial JOIN ininsurancetypes ity ON ity.inity_insurance_type_serial = oldpol.inpol_insurance_type_serial WHERE oldpol.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial AND inldg_loading_type <> 'X' AND ((inldg_loading_type = oldpol.inpol_cover) OR (oldpol.inpol_cover = 'B' AND ity.inity_act_in_fire_theft = 'N') OR (oldpol.inpol_cover = 'C' AND ity.inity_act_in_comprehensive = 'N'))), 0) ELSE 0 ENDIF as clo_refund_outstanding_endorsement,           
((inped_premium * inped_premium_debit_credit) * -1) + IF clo_process_status = 'E' THEN IF inpol_status IN ('C','O') THEN clo_refund_outstanding_endorsement ELSE COALESCE((SELECT ((a.inped_premium * a.inped_premium_debit_credit) * -1) FROM inpolicyendorsement a, inpolicies b WHERE a.inped_policy_serial = b.inpol_policy_serial AND a.inped_endorsement_serial = b.inpol_last_cancellation_endorsement_serial AND b.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial), 0) ENDIF ELSE 0 ENDIF as clo_premium,
((inped_mif * inped_premium_debit_credit) * -1) + IF clo_process_status = 'E' THEN IF inpol_status IN ('C','O') THEN ROUND(clo_refund_outstanding_mif_pr_endorsement * CASE clo_replacing_policy_pstatus WHEN 'N' THEN inity_mif_new WHEN 'R' THEN inity_mif_renewal ELSE inity_mif_endorsment END / 100, 2) ELSE COALESCE((SELECT ((a.inped_mif * a.inped_premium_debit_credit) * -1) FROM inpolicyendorsement a, inpolicies b WHERE a.inped_policy_serial = b.inpol_policy_serial AND a.inped_endorsement_serial = b.inpol_last_cancellation_endorsement_serial AND b.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial), 0) ENDIF ELSE 0 ENDIF as clo_mif,           
IF clo_process_status = 'E' THEN (SELECT oldpol.inpol_process_status FROM inpolicies oldpol WHERE oldpol.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial) ELSE '' ENDIF as clo_replacing_policy_pstatus,           
-1 * IF clo_process_status = 'E'  AND inpol_status IN ('O', 'C') THEN COALESCE((SELECT SUM(inplg_return_premium) FROM inpolicies oldpol JOIN inpolicyloadings ON oldpol.inpol_policy_serial = inplg_policy_serial JOIN inloadings ON inplg_loading_serial = inldg_loading_serial JOIN ininsurancetypes ity ON ity.inity_insurance_type_serial = oldpol.inpol_insurance_type_serial WHERE oldpol.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial AND inldg_loading_type <> 'X' AND inldg_mif_applied <> 'N' AND ((inldg_loading_type = oldpol.inpol_cover) OR (oldpol.inpol_cover = 'B' AND ity.inity_act_in_fire_theft = 'N') OR (oldpol.inpol_cover = 'C' AND ity.inity_act_in_comprehensive = 'N'))), 0) ELSE 0 ENDIF as clo_refund_outstanding_mif_pr_endorsement,           
COALESCE((SELECT SUM(intrd_value * intrd_debit_credit)  * -1 FROM intransactiondetails WHERE intrd_policy_serial = inpol_policy_serial AND (intrd_endorsement_serial = IF clo_process_status = 'C' THEN inpol_last_cancellation_endorsement_serial ELSE IF clo_process_status <> 'L' THEN inpol_last_endorsement_serial ELSE 0 ENDIF ENDIF) AND COALESCE(intrd_claim_serial, 0) = 0 AND intrd_transaction_type IN ('C0', 'C1','C2','C3','C4','C5','CF') AND intrd_status <> '9' /*Excl.Deleted*/ AND intrd_owner = 'O' ), 0) as clo_commission_charge,           
IF clo_process_status = 'E' THEN COALESCE((SELECT SUM(intrd_value * intrd_debit_credit) FROM intransactiondetails, inpolicies a WHERE a.inpol_policy_serial = inpolicies.inpol_replacing_policy_serial AND intrd_policy_serial = a.inpol_policy_serial AND intrd_endorsement_serial = a.inpol_last_cancellation_endorsement_serial AND COALESCE(intrd_claim_serial, 0) = 0 AND intrd_transaction_type IN ('C0', 'C1','C2','C3','C4','C5','CF') AND intrd_status <> '9' /*Excl.Deleted*/ AND intrd_owner = 'O'), 0) ELSE 0 ENDIF as clo_commission_reduction,           
clo_commission_charge - clo_commission_reduction as clo_commission_net,           
COALESCE((SELECT FIRST intrd_related_serial FROM intransactiondetails WHERE intrd_policy_serial = inpol_policy_serial AND (intrd_endorsement_serial = IF clo_process_status = 'C' THEN inpol_last_cancellation_endorsement_serial ELSE inpol_last_endorsement_serial ENDIF) AND COALESCE(intrd_claim_serial, 0) = 0 AND intrd_status <> '9' /*Excl.Deleted*/ AND intrd_transaction_type IN ('C0', 'C1','C2','C3','C4','C5','CF') AND intrd_related_type = 'A'), 0) as clo_commission_agent,
IF clo_commission_agent <> inpol_agent_serial THEN (SELECT a.inag_agent_code FROM inagents a WHERE a.inag_agent_serial = clo_commission_agent) ELSE '' ENDIF as clo_commission_agent_code
".$extra_from."
FROM 
intemplates RIGHT OUTER JOIN inpolicies ON intemplates.intmpl_template_serial = inpolicies.inpol_template_serial ,           
inpolicyendorsement ,           
ininsurancetypes

WHERE 
ininsurancetypes.inity_insurance_type_serial = inpolicies.inpol_insurance_type_serial
and ( inpolicies.inpol_policy_serial = inpolicyendorsement.inped_policy_serial ) 
and ((inpolicyendorsement.inped_endorsement_serial = inpolicies.inpol_last_endorsement_serial) 
or  (inpolicyendorsement.inped_endorsement_serial = inpolicies.inpol_last_cancellation_endorsement_serial 
and (inpolicies.inpol_replaced_by_policy_serial = 0)) /* CANCELLATION OR LAPSED */ )    

AND  1=1  
AND LEFT(clo_process_status, 1) IN ('N','R','E','D','C','L') 
AND inpol_status IN ('A','N','O','C') 
//AND (inped_year*100+inped_period)>=(2012*100+3) 
//AND (inped_year*100+inped_period)<=(2012*100+3) 

".$policy_serial_sql."
".$extra_where."
ORDER BY inpol_policy_number ASC 

".$extra_order;	
	return $sql;
}

?>