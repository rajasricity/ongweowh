<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'libraries/sendgrid/sendgrid-php.php');
class Users extends CI_Controller {

	public function __construct(){
		
		parent::__construct();
		
		if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
	}
	
	public function index()
	{
		$this->load->view('admin/users/allUsers');
	}
	
	public function createUser(){
		
		$this->load->view('admin/users/createUser');
		
	}
	
	public function editUser($uid){
		
		$id = new MongoDB\BSON\ObjectID($uid);
		
		$data['u'] = $this->mongo_db->get_where("tbl_auths",array('_id' => $id));
		
		$this->load->view('admin/users/createUser',$data);
		
	}
	
	public function insertUser(){
		
		$name = $this->input->post("uname");
		$email = $this->input->post("email");
		$password = $this->input->post("password");
		$role = $this->input->post("role");
		$appid = $this->input->post("appid");
		$id = $this->admin->insert_id("tbl_auths");
		
		if($appid){
			
			$row = $this->mongo_db->get_where("tbl_auths",array('email' => $email,"appid"=>$appid));
			
		}else{
			
			$row = $this->mongo_db->get_where("tbl_auths",array('email' => $email));
			
		}
		
		if(count($row) > 0){
			
			echo "Email Already Exists";
			exit();
			
		}
		
		$data = array(
		
			"id" => $id,
			"uname" => $name,
			"email" => $email,
			"password" => $this->secure->encrypt($password),
			"role" => $role,
			"deleted" => 0,
			"status" => "Active",
			"appid" => $appid,
			"created_date" => date("Y-m-d H:i:s"),
			"profile_pic" => ""
		
		);
		
		
		$d = $this->mongo_db->insert("tbl_auths",$data);
		
		if($d){
			
			$msg = '<html>
			<head>

			</head>
				<body>
					<table style="width:100%;border:3px solid #363636;border-bottom:0px;">
						<tr style="background-color: #002F47;">
							<td>
							<center><img src="'.base_url().'assets/logo/home.png" width="40%"/></center>
							</td>
						</tr>


					</table>
					<br>
					<table style="width:100%;border:3px solid #363636;border-top:0px;">
					<thead>
					 <tr>
						<th>Username</th>
						<th>Password</th>
					 </tr>	
					</thead>
					<tbody>
						<tr style="background-color: #D4D4D4">
						 <td style="text-align:center">'.$email.'</td>
						 <td style="text-align:center">'.$password.'</td>
						</tr>
					</tbody>
					</table>

				</body>
			</html>';
			
			$subject = 'Ongweoweh Login Credentials';
			
			// $this->admin->send_email($subject,$email,$msg);
			echo "success";
			
		}else{
			
			echo "error";
			
		}
		
	}
		
	public function updateUser(){
		
		
		$name = $this->input->post("uname");
		$email = $this->input->post("email");
		//$password = $this->input->post("password");
		$role = $this->input->post("role");
		$status = $this->input->post("status");
		$id = new MongoDB\BSON\ObjectID($this->input->post("id"));
		$appid = $this->input->post("appid");
		
		if($appid){
		
			$row = $this->mongo_db->get_where("tbl_auths",array('email' => $email,"_id"=>$id,"appid"=>$appid));
			
		}else{
			
			$row = $this->mongo_db->get_where("tbl_auths",array('email' => $email,"_id"=>$id));
			
		}
		
		if($row[0]["email"]==$email){

			
		}else{
			
			$echk1 = $this->mongo_db->get_where("tbl_auths",array("email"=>$email));	
			if(count($echk1)> 0){
				echo "Email Already Exists";
				exit();
			}else{
				
			}
			
		}
		
		$data = array(
		
			"uname" => $name,
			"email" => $email,
			"role" => $role,
			"status" => $status,
			"updated_date" => date("Y-m-d H:i:s")
		
		);
		
		
		$d = $this->mongo_db->where(array('_id'=>$id))->set($data)->update('tbl_auths');
		
		if($d){
			/*
			$msg = '<html>
			<head>

			</head>
				<body>
					<table style="width:100%;border:3px solid #363636;border-bottom:0px;">
						<tr style="background-color: #002F47;">
							<td>
							<center><img src="'.base_url().'assets/logo/home.png" width="40%"/></center>
							</td>
						</tr>


					</table>
					<br>
					<table style="width:100%;border:3px solid #363636;border-top:0px;">
					<thead>
					 <tr>
						<th>Username</th>
						<th>Password</th>
					 </tr>	
					</thead>
					<tbody>
						<tr style="background-color: #D4D4D4">
						 <td style="text-align:center">'.$email.'</td>
						 <td style="text-align:center">'.$password.'</td>
						</tr>
					</tbody>
					</table>

				</body>
			</html>';
			
			$subject = 'Ongweoweh Login Credentials';
			
			$this->admin->send_email($subject,$email,$msg);
			*/
			echo "success";
			
		}else{
			
			echo "error";
			
		}
		
	}

	public function addLocation(){

		// print_r($_POST);

		$locations = $this->input->post("loc");
		$type = $this->input->post("type");
		
		$loc = [];
		
		$uid = $this->input->post("uid");
		$id = new MongoDB\BSON\ObjectID($uid);
		
		foreach($locations as $l){

			
			$lid = new MongoDB\BSON\ObjectID($l);
			$ld = $this->admin->getRow("",["_id"=>$lid],[],"$this->database.tbl_locations");
			
			$ndata = [];
			
			$ndata["Date"] = date("M-d-Y H:i:s");
			$ndata["LocationId"] = strval($lid);
			$ndata["loccode"] = strval($ld->loccode);
			$ndata["LocationName"] = $ld->locname;
			$ndata["Type"] = $type;
			$ndata["status"] = $ld->status;
			
			$loc[] = $ndata;
			
			$d = $this->mongo_db->where(array('_id'=>$id))->push("locations",$ndata)->update('tbl_auths');

			// print_r($d);
			
		}
		
		
		
//		$exLoc = $this->mongo_db->get_where("tbl_auths",array('_id'=>$id));
//		
//		$eLoc = ($exLoc[0]["locations"] != "") ? $exLoc[0]["locations"] : [];
//		
//		$data = array_merge($eLoc,$loc);
		
//		$d = $this->mongo_db->where(array('_id'=>$id))->push("locations",$loc)->update('tbl_auths');
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'error';
			
		}
		
	}
	
	public function delUser($id){
		
		$lid = new MongoDB\BSON\ObjectID($id);
		
		$d = $this->mongo_db->where(array('_id'=>$lid))->delete('tbl_auths');
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'error';
			
		}
		
	}
	
	public function test(){
		
		$db = $this->mongo_db->command('tbl_locations',array("locname"=>"ravi"));
		print_r($db);
	}

	
	
}
