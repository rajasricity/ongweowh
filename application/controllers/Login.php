<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'libraries/sendgrid/sendgrid-php.php');

class Login extends CI_Controller {

	public function __construct(){
		
		parent::__construct();
		
		if($this->session->userdata("admin_email")){
			
			redirect("admin/dashboard");
			
		}
		
	}
	
	
	public function index()
	{
		$this->load->view('admin/login');
	}
	
	public function do_login(){
		
		$uname = $this->input->post("uname");
		$password = $this->input->post("password");
		
		$row = $this->mongo_db->get_where("tbl_auths",array('email' => new MongoDB\BSON\Regex($uname,'i'),"status"=>"Active","deleted"=>0));
		
//		echo $row[0]["_id"]->{'$id'};
		
		if(count($row) > 0){
			
			if($this->secure->decrypt($row[0]["password"]) != $password){
				echo json_encode(array("status"=>"Please check login credentials."));
				exit();
			}
			
			$appChk = $this->mongo_db->get_where("tbl_apps",["appId"=>$row[0]['appid']])[0];
			
			if($appChk["deleted"] == 1 || $appChk["status"] == "Inactive"){
				
				echo json_encode(array("status"=>"Your Account Is Blocked Please Contact Administrator."));
				exit();
				
			}
			
			$this->session->set_userdata(array("admin_email"=>$row[0]['email'],"role"=>$row[0]['role'],"appId"=>$row[0]['appid']));
			echo json_encode(array("status"=>"Success","role"=>$row[0]['role']));
				
		}else{
			
			echo json_encode(array("status"=>"Please check login credentials."));
			
		}
		
	}
	
	public function forgotpassword(){
		
		$email = $this->input->post("uname");
		$eChk = $this->mongo_db->get_where("tbl_auths",array('email'=>new MongoDB\BSON\Regex($email,'i'),"status"=>"Active","deleted"=>0));
		$otp = random_string("numeric",6);

		if(count($eChk) > 0){
			/*
			<table style="width:100%;border:3px solid #363636;border-bottom:0px;">
								<tr style="background-color: #002F47;">
									<td>
										<center><img src="'.base_url().'assets/logo/home.png" width="40%"/></center>
									</td>
								</tr>


							</table>
							<br>
						*/
		
			$msg = '<html>
						<head>

						</head>
						<body>
							
							<p>
								Dear User,<br><br>
								
								A request has been received to change the password for your account. Please find below code for change password.<br><br>
								
								Code:'.$otp.'<br><br>
								
								If you did not initiate this request, please contact us immediately at support@Ongweoweh.com.<br><br>
								
								Thank you,<br>
								Ongweoweh Team
								
							</p>
						</body>
					</html>';
			
			$subject = "Forgot OTP Verification";
			
			$otpChk = $this->mongo_db->get_where("tbl_otp",array('email'=>$email));
			
			if(count($otpChk) > 0){
				
				$this->mongo_db->where(array('email'=>$email))->set(array("otp"=>$otp))->update('tbl_otp');
				
			}else{
				
				$this->mongo_db->insert("tbl_otp",array('email'=>$email,"otp"=>$otp));
				
			}
			
			$this->admin->send_email($subject,$email,$msg);
			
			echo "success";
			
		}else{
			
			echo 'User not registered with us';
			
		}
		
	}
	
	public function checkOtp(){
		
		$email = $this->input->post("email");
		$otp = $this->input->post("otp");
		
		$otpChk = $this->mongo_db->get_where("tbl_otp",array('email'=>$email,"otp"=>$otp));
		
		if(count($otpChk) > 0){
		
			$this->mongo_db->where(array('email'=>$email))->delete('tbl_otp');
			echo "success";
			
		}else{
			
			echo 'Incorrect OTP';
			
		}
		
	}
	
	public function resetPassword(){
		
		$email = $this->input->post("email");
		$pass = $this->input->post("pass");
		$cpass = $this->input->post("cpass");
		
		if($pass != $cpass){
			
			echo "password not matched";
			exit();
			
		}
		
		$d = $this->mongo_db->where(array('email'=>$email))->set(array("password"=>$this->secure->encrypt($pass)))->update('tbl_auths');
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'fail';
			
		}
		
	}

	// public function mytest(){
		
	// 	$password=$this->secure->encrypt('Ampcus$2020#');
	// 	echo $password;
	// }
}
