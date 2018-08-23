<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

class send_auto_emails {
	
	public $auto_email_serial = 0;
	private $initial_query_result;
	private $data;
	private $end_of_line;
	public $messages;//stores messages of the whole process for debugging
	private $error_messages;
	private $current_serial;
	private $add_extra_mail_headers;
	private $send_all_init = 0;
	public $count_errors = 0;
	
	public function __construct($use_auto_email_serial = 0) {
		global $db;

        $this->messages['error'] = 0;
		$this->messages['error_message'] = 'None';
		if ($use_auto_email_serial == 0) {
			$this->messages[] = 'Start: Get information for all pending emails';
			$sql = "SELECT * FROM send_auto_emails WHERE sae_active = 'A' AND sae_send_result = 0 ORDER BY sae_created_datetime ASC";
		}
		else {
			$this->messages[] = 'Start: Get information for a specific email';
			$sql = "SELECT * FROM send_auto_emails WHERE sae_active = 'A' AND sae_send_auto_emails_serial = ".$use_auto_email_serial." ORDER BY sae_created_datetime ASC";
		}
		$this->end_of_line = 0;
		$this->initial_query_result = $db->query($sql);
		$this->get_next_record_data();

	}
	
	public function get_next_record_data() {
		global $db;
		if ($this->end_of_line != 1) {
			$this->data = $db->fetch_assoc($this->initial_query_result);
			//check if end of line
			if ($this->data["sae_send_auto_emails_serial"] > 0) {
				$this->current_serial = $this->data["sae_send_auto_emails_serial"];
				$this->messages[] = "Retrieve data for the record: ".$this->current_serial;
			}
			else {
				$this->messages[] = "No more records. Reached end of line";
				$this->end_of_line = 1;
			}
		}
		else {
			$this->messages[] = "No more records. Reached end of line";
		}
	}
	
	public function execute_all_emails() {
		if ($this->end_of_line != 1) {
			if ($this->send_all_init == 0) {
				$this->messages[] = "Executing all emails function initiated";
			}
			$this->send_all_init = 1;
			$this->send_email();
			$this->get_next_record_data();
			$this->execute_all_emails();	
		}
	}
	
	public function send_email() {

		global $db,$main;
		if ($this->data["sae_send_result"] != 0) {
			$this->messages[] = 'This email has already been sent. SKIP';
			$this->messages["error"] = 1;
			$this->messages["error_message"] = 'This email has already been sent. SKIP';
			return;
		}
		if (!$this->data["sae_send_auto_emails_serial"] > 0) {
			$this->messages[] = 'No data found';
			$this->messages["error"] = 1;
			$this->messages["error_message"] = 'No data found';

			return;
		}

		//require_once $main["local_url"].'/include/libraries/phpMailer/PHPMailerAutoload.php';
		$mail = new PHPMailer(true);
		//fix emails
		$this->fix_email_lists();


		$this->messages[] = "Sending email of serial: ".$this->data["sae_send_auto_emails_serial"];

		//$mail->SMTPDebug = 3;//true echos all steps           // Enable verbose debug output
		//$mail->CharSet = "UTF-8";
		//$mail->isSMTP();                                        // Set mailer to use SMTP
		//$mail->Host = '192.168.1.254';       // Specify main and backup SMTP servers
		//$mail->SMTPAuth = false;                                // Enable SMTP authentication
		//$mail->Username = 'user@example.com';                 // SMTP username
		//$mail->Password = 'secret';                           // SMTP password
		//$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		//$mail->Port = 587;            
		$mail->isHTML(true);
		$mail->setFrom($this->data["sae_email_from"], $this->data["sae_email_from_name"]);
		//$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
		//loop into all the recipients to add them one by one
		$to = explode(",",$this->data["sae_email_to"]);
		$to_name = explode(",",$this->data["sae_email_to_name"]);

        foreach($to as $name=>$value) {
			if ($mail->addAddress($value,$to_name[$name])){
				$this->messages[] = "Mail - ".$value." added succesfully";
            }
			else {
				$this->messages[] = "Error adding Mail - ".$value;
				$this->error_messages .= "\nError adding Mail - ".$value;
            }
		}

        if ($mail->addReplyTo($this->data["sae_email_reply_to"], $this->data["sae_email_reply_to_name"])) {
            $this->messages[] = "Reply to Mail - ".$this->data["sae_email_reply_to"]." added succesfully";
        }
		else {
            $this->messages[] = "Error adding Reply to Mail - ".$this->data["sae_email_reply_to"];
			$this->error_messages .= "\nError adding  Reply to Mail - ".$this->data["sae_email_reply_to"];
        }

		//loop into all the cc to add them one by one
		if ($this->data["sae_email_cc"] != '') {
			$cc = explode(",",$this->data["sae_email_cc"]);
			foreach($cc as $value) {
				if ($mail->addCC($value)) {
					$this->messages[] = "CC Mail - ".$value." added succesfully";
				}
				else {
					$this->messages[] = "Error adding CC Mail - ".$value;
					$this->error_messages .= "\nError adding CC Mail - ".$value;
				}
			}
		}
		//loop into all the cc to add them one by one
		if ($this->data["sae_email_bcc"] != '') {
			$bcc = explode(",",$this->data["sae_email_bcc"]);
			foreach($bcc as $value) {
				if ($mail->addBCC($value)) {
					$this->messages[] = "BCC Mail - ".$value." added succesfully";
				}
				else {
					$this->messages[] = "Error adding BCC Mail - ".$value;
					$this->error_messages .= "\nError BCC adding Mail - ".$value;
				}
			}
		}
		$mail->addAttachment($main["local_url"].'/'.$this->data['sae_attachment_file']);         // Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
		$mail->isHTML(true);                                  // Set email format to HTML
		
		$mail->Subject = $this->data["sae_email_subject"];
		
		$mail->msgHTML($this->data["sae_email_body"]);
		//$mail->Body    = $this->data["sae_email_body"];
		//$mail->Body    = mb_convert_encoding($this->data["sae_email_body"], "ISO-8859-1", mb_detect_encoding($this->data["sae_email_body"], "UTF-8, ISO-8859-1, ISO-8859-15", true));
		
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
		
		if(!$mail->send()) {
			$this->messages[] = "Error sending email: Error message: ".$mail->ErrorInfo;
			$this->update_email_result(-1,'Message could not be sent. '.$mail->ErrorInfo);
			$this->count_errors++;
		} else {
			$this->update_email_result(1,'Message has been sent '.$mail->ErrorInfo);
			$this->messages[] = "Email Send success";
		}		
	}
	
	public function update_email_result($result,$result_description) {
		global $db;
		$sql = "UPDATE send_auto_emails SET
		sae_send_result = ".$result.",
		sae_send_result_description = '".addslashes($result_description)." ".addslashes($this->error_messages)."',
		sae_send_datetime = '".date("Y-m-d G:i:s")."'
		WHERE 
		sae_send_auto_emails_serial = ".$this->data["sae_send_auto_emails_serial"];
		$db->query($sql);
	}
	
	public function fix_email_lists() {
		//fixes the email_to, email_from, reply_to, cc, bcc and updates also the database.
		//replace the ; to , 
		$this->data["sae_email_to"] = str_replace(";",",",$this->data["sae_email_to"]);
		$this->data["sae_email_from"] = str_replace(";",",",$this->data["sae_email_from"]);
		$this->data["sae_email_reply_to"] = str_replace(";",",",$this->data["sae_email_reply_to"]);
		$this->data["sae_email_cc"] = str_replace(";",",",$this->data["sae_email_cc"]);
		$this->data["sae_email_bcc"] = str_replace(";",",",$this->data["sae_email_bcc"]);
		
		//remove any spaces found
		$this->data["sae_email_to"] = str_replace(" ","",$this->data["sae_email_to"]);
		$this->data["sae_email_from"] = str_replace(" ","",$this->data["sae_email_from"]);
		$this->data["sae_email_reply_to"] = str_replace(" ","",$this->data["sae_email_reply_to"]);
		$this->data["sae_email_cc"] = str_replace(" ","",$this->data["sae_email_cc"]);
		$this->data["sae_email_bcc"] = str_replace(" ","",$this->data["sae_email_bcc"]);

	}

}

?>