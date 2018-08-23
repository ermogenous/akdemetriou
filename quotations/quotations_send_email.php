<?php
/**
 * Created by PhpStorm.
 * User: micac
 * Date: 29/5/2018
 * Time: 9:32 ΠΜ
 */
include("../include/main.php");
include('quotations_class.php');
$db = new Main();

//first create the pdf file
if ($_POST["action"] == 'send_mail') {

        //generate the pdf
        include('quotations_print_html.php');

        $html = getQuotationHtml($_POST["lid"], true);
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

        //filename
        //$filename = 'pdfFiles/' . $qt->getQuotationUserID() . '/' . date('YmdHis-') . $_POST["lid"] . '.pdf';
        $filename = 'pdfFiles/' . $_POST["lid"] . '-' . date('YmdHis') . '.pdf';

        //echo $filename;
        //$handle = fopen($filename, 'w');
        //fclose($handle);

        $mpdf->WriteHTML($html);
        $mpdf->Output($filename, \Mpdf\Output\Destination::FILE);

        if ($_POST['fld_email_name'] == ''){
            $_POST['fld_email_name'] = $_POST['fld_email'];
        }

        //create the email
        $emailData['active'] = 'A';
        $emailData['type'] = 'quotation';
        $emailData['created_datetime'] = date('Y-m-d G:i:s');
        $emailData['send_result'] = '0';
        $emailData['primary_serial'] = $_POST["lid"];
        $emailData['primary_label'] = 'Quotation_ID';
        $emailData['email_to'] = $_POST['fld_email'];
        $emailData['email_to_name'] = $_POST['fld_email_name'];
        $emailData['email_from'] = $main["no-reply-email"];
        $emailData['email_from_name'] = $main["no-reply-email-name"];
        $emailData['email_subject'] = $main['admin_title'].' - Quotation';
        $emailData['email_reply_to'] = $main["no-reply-email"];
        $emailData['email_reply_to_name'] = $main["no-reply-email-name"];
        $emailData['email_body'] = 'Please find attached our quotation';
        $emailData['attachment_file'] = 'quotations/'.$filename;
        $mailID = $db->db_tool_insert_row('send_auto_emails',$emailData,'',1,'sae_');

        //send the email
        include('../send_auto_emails/send_auto_emails_class.php');
        $email = new send_auto_emails($mailID);
        //echo "Sending Email";
        $email->send_email();
        header("Location: quotations.php?alert-success=Email Send");
        exit();
}

if ($_GET['lid'] == '') {
    header("Location: quotations.php");
    exit();
}
$qt = new quotations($_GET["lid"]);
if ($qt->verifyAccess($db)) {
    //access granted
    $quotationData = $qt->getQuotationData();
}
else {
    header("Location: quotations.php");
    exit();
}

$db->show_header();
?>

    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-3 hidden-xs hidden-sm"></div>
            <div class="col-lg-6 col-md-6 col-xs-12 col-sm-12">
                <form name="groups" method="post" action="" onSubmit="" class="justify-content-center">

                    <div class="alert alert-dark text-center"><b>Send Email for Quotation <?php echo $quotationData['quotations_id'];?></b>
                    </div>

                    <div class="form-group row">
                        <label for="fld_email" class="col-sm-4 col-form-label">Email*</label>
                        <div class="col-sm-8">
                            <input name="fld_email" type="email" id="fld_email"
                                   class="form-control"
                                   value=""
                                   required/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="fld_email_name" class="col-sm-4 col-form-label">Name</label>
                        <div class="col-sm-8">
                            <input name="fld_email_name" type="text" id="fld_email_name"
                                   class="form-control"
                                   value=""/>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="" class="col-sm-4 col-form-label"></label>
                        <div class="col-sm-8">
                            <input name="action" type="hidden" id="action"
                                   value="send_mail">
                            <input name="lid" type="hidden" id="lid" value="<?php echo $_GET["lid"]; ?>">
                            <input type="submit" name="Submit" value=" Send Email " class="btn btn-secondary">
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-3 col-md-3 hidden-xs hidden-sm"></div>
        </div>
    </div>

<?php

$db->show_footer();

