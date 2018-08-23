<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 5/7/2018
 * Time: 1:18 ΜΜ
 */

class approvals
{

    private $quotationID;
    private $appResult;

    public $approvalData;
    public $quotationData;
    public $noApproval = false;
    public $status;
    public $processStatus;

    function __construct($quotationID)
    {

        global $db;

        $sql = 'SELECT * FROM quotations WHERE quotations_id = '.$quotationID;
        $this->quotationData = $db->query_fetch($sql);

        $this->quotationID = $quotationID;
        //get info of the last approval
        $sql = 'SELECT * FROM  quotation_approvals WHERE oqa_quotation_ID = '.$quotationID.' ORDER BY oqa_quotation_approvals_ID DESC';
        $this->appResult = $db->query($sql);
        $this->approvalData = $db->fetch_assoc($this->appResult);
        if ($this->approvalData['oqa_quotation_approvals_ID'] == ''){
            $this->noApproval = true;
        }
        $this->status = $this->approvalData['oqa_status'];
        $this->processStatus = $this->approvalData['oqa_process_status'];
    }

    function nextApproval() {
        global $db;
        if ($this->noApproval == false) {
            $this->approvalData = $db->fetch_assoc($this->appResult);
            if ($this->approvalData['oqa_quotation_approvals_ID'] == '') {
                $this->noApproval = true;
            }
        }
    }

    function create($message, $toUserID, $toGroupID){
        global $db;


        //prepare data for db
        $data['quotation_ID'] = $this->quotationID;
        $data['status'] = 'A';
        $data['process_status'] = 'O';
        $data['from_user_ID'] = $db->user_data["usr_users_ID"];
        $data['to_user_ID'] = $toUserID;
        $data['group_ID'] = $toGroupID;
        $data['message'] = $message;
        $data['send_date_time'] = date('Y-m-d G:i:s');

        $db->db_tool_insert_row('quotation_approvals', $data, '', 0, 'oqa_');
        return true;
    }

    function reject($message){
        global $db;

        //prepare data for db
        $data['process_status'] = 'R';
        $data['reply_message'] = $message;
        $data['reply_date_time'] = date('Y-m-d G:i:s');

        $db->db_tool_update_row('quotation_approvals', $data,
            "`oqa_quotation_approvals_ID` = " . $this->approvalData['oqa_quotation_approvals_ID'],
            $this->approvalData['oqa_quotation_approvals_ID'],
            '',
            'execute',
            'oqa_');


    }

    function approve($message){
        global $db;

        //prepare data for db
        $data['process_status'] = 'A';
        $data['reply_message'] = $message;
        $data['reply_date_time'] = date('Y-m-d G:i:s');

        $db->db_tool_update_row('quotation_approvals', $data,
            "`oqa_quotation_approvals_ID` = " . $this->approvalData['oqa_quotation_approvals_ID'],
            $this->approvalData['oqa_quotation_approvals_ID'],
            '',
            'execute',
            'oqa_');

    }

    function delete() {
        global $db;
        //prepare data for db
        $data['status'] = 'D';

        $db->db_tool_update_row('quotation_approvals', $data,
            "`oqa_quotation_approvals_ID` = " . $this->approvalData['oqa_quotation_approvals_ID'],
            $this->approvalData['oqa_quotation_approvals_ID'],
            '',
            'execute',
            'oqa_');
    }

    function re_evaluate($message){
        global $db;

        //prepare data for db
        $data['process_status'] = 'V';
        $data['reply_message'] = $message;
        $data['reply_date_time'] = date('Y-m-d G:i:s');

        $db->db_tool_update_row('quotation_approvals', $data,
            "`oqa_quotation_approvals_ID` = " . $this->approvalData['oqa_quotation_approvals_ID'],
            $this->approvalData['oqa_quotation_approvals_ID'],
            '',
            'execute',
            'oqa_');

    }

    function getProcessStatusLabel() {
        return approvalsGetProcessStatusLabel($this->approvalData['oqa_process_status']);
    }

    function getStatusLabel() {
        return approvalGetStatusLabel($this->approvalData['oqa_status']);
    }
//returns false if the quotation is ok for modify
//returns true if the quotation should be locked
    function lockQuotation(){
        if ($this->quotationData['individual_group'] == 'I') {
            return false;
        }
        if ($this->noApproval == true) {
            return false;
        }
        //echo $this->status." ".$this->processStatus;
        if ($this->status == 'D'){
            return false;
        }
        if ($this->status == 'A' || $this->status == 'C'){
            if ($this->processStatus == 'V') {
                return false;
            }
            else {
                return true;
            }
        }
        else {
            return true;
        }
    }


}

function approvalsGetProcessStatusLabel($pStatus) {
    if ($pStatus == 'O') {
        return 'Outstanding';
    }
    else if ($pStatus == 'R') {
        return 'Rejected';
    }
    else if ($pStatus == 'A') {
        return 'Approved';
    }
    else if ($pStatus == 'V') {
        return 'Re-evaluate';
    }
}

function approvalGetStatusLabel($status){
    if ($status == 'A'){
        return 'Active';
    }
    else if ($status == 'C'){
        return 'Completed';
    }
    else if ($status == 'D'){
        return 'Deleted';
    }
    else if ($status == 'R'){
        return 'Replaced';
    }


}