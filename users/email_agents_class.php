<?

class email_to_agents{
	
	var $email = '';
	var $email_cc = '';
	var $email_bcc = '';
	
	var $issuing_email = '';
	var $issuing_cc = '';
	var $issuing_bcc = '';
	
	var $user_email = '';
	var $user_email_cc = '';
	var $user_email_bcc = '';
	
	
	public function email_to_agents($user_ID) {
		global $db;
		
		$sql = "SELECT * FROM users WHERE usr_users_ID = ".$user_ID;
		$user_data = $db->query_fetch($sql);
		
		if ($user_data["usr_email"] != "") {
			$this->email .= $user_data["usr_email"];
			$this->user_email .= $user_data["usr_email"];
		}
		if ($user_data["usr_email2"] != "") {
			if ($user_data["usr_email2"] != "") {
				$this->email .= ",".$user_data["usr_email2"];
				$this->user_email .= ",".$user_data["usr_email2"];
			}
		}
		if ($user_data["usr_emailcc"] != "") {
			$this->email_cc .= $user_data["usr_emailcc"];	
			$this->user_email_cc .= $user_data["usr_emailcc"];	
		}
		if ($user_data["usr_emailbcc"] != "") {
			$this->email_bcc .= $user_data["usr_emailbcc"];
			$this->user_email_bcc .= $user_data["usr_emailbcc"];
		}
		
		//get the emails from the issuing office
		if ($user_data["usr_issuing_office_serial"] != 0) {
			$sql = "SELECT * FROM users WHERE usr_users_ID = ".$user_data["usr_issuing_office_serial"];
			$data = $db->query_fetch($sql);
			
			if ($data["usr_email"] != "") {
				if ($data["usr_email"] != "") {
					$this->email .= ",".$data["usr_email"];
					$this->issuing_email = $data["usr_email"];
				}
			}
			if ($data["usr_email2"] != "") {
				if ($data["usr_email2"] != "") {
					$this->email .= ",".$data["usr_email2"];
					$this->issuing_email .= ",".$data["usr_email2"];
				}
			}
			if ($data["usr_emailcc"] != "") {
				if ($data["usr_emailcc"] != "") {
					$this->email_cc .= ",".$data["usr_emailcc"];
					$this->issuing_cc = $data["usr_emailcc"];
				}
			}
			if ($data["usr_emailbcc"] != "") {
				if ($data["usr_emailbcc"] != "") {
					$this->email_bcc .= ",".$data["usr_emailbcc"];
					$this->issuing_bcc = $data["usr_emailbcc"];
				}
			}			
		}//get emails from issuing office
		
		//clear the first comma if exists.
		
		$this->email = $this->clear_first_comma($this->email);
		$this->email_cc = $this->clear_first_comma($this->email_cc);
		$this->email_bcc = $this->clear_first_comma($this->email_bcc);
		
		$this->issuing_email = $this->clear_first_comma($this->issuing_email);
		$this->issuing_cc = $this->clear_first_comma($this->issuing_cc);
		$this->issuing_bcc = $this->clear_first_comma($this->issuing_bcc);

		$this->user_email = $this->clear_first_comma($this->user_email);
		$this->user_email_cc = $this->clear_first_comma($this->user_email_cc);
		$this->user_email_bcc = $this->clear_first_comma($this->user_email_bcc);

		
		
	}//functions init

	function clear_first_comma($text) {
		if (substr($text,0,1) == ',') {
			return substr($text,1);	
		}
		else {
			return $text;	
		}
	}
	
}//class




?>