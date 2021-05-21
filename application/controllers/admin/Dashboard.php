<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	
	public function __construct(){
		
		parent::__construct();
		
		if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}
		
	}
	
	public function batchprocess(){
		
		$this->load->view("admin/batchprocess");
			
	}

public function index(){
		$_SESSION['appid'] = '';
		$mng = $this->admin->Mconfig();
		$apps = $this->admin->getArray($mng,["status"=>"Active","deleted"=>0],[],mongodb.".tbl_apps");
		$items = [];
		$itemsCount = [];
		foreach($apps as $value){
			$table = mongodb."_".$value->appId.".tbl_items";
			$n = $this->admin->getArray($mng,["status"=>"Active"],[],$table);
			$itemsCount[] = count($n); 
			$n['database'] = mongodb."_".$value->appId;
			$n['appName'] = $value->appname;
			array_push($items, $n);

		}

		$acts = $this->mongo_db->aggregate("tbl_tasks",[
				['$sort'=>["_id"=>-1]],
				['$limit'=>10],
				['$lookup'=>
					["from"=>'tbl_apps',
					 'localField'=>"appId",
					 'foreignField'=>'appId',
					 'as'=>'customers']
				]
			]);

		$activities = [];
		foreach($acts as $activity){
			if($activity['table'] == 'tbl_touts'){ $module = 'Transfers'; }
			if($activity['table'] == 'tbl_locations'){ $module = 'Locations'; }
			if($activity['table'] == 'tbl_inventory'){ $module = 'Location Inventory'; }
			if($activity['table'] == 'tbl_adjustment'){ $module = 'adjustments'; }
			if($activity['table'] == 'tbl_issues'){ $module = 'Shipments'; }
			if($activity['table'] == 'tbl_returns'){ $module = 'Pickups'; }
			if($activity['table'] == 'tbl_items'){ $module = 'Items'; }
			array_push($activities, ['Module'=>$module,'TaskName'=>strtoupper($activity['task_name']),'DateTime'=>$activity['next_run_date']." ".$activity['next_run_time'],'Customer'=>$activity['customers'][0]->appname]);
		}
		$data['activities'] = $activities;
		
		$data['locationsCount']= $this->getLocationscount();
		$data['itemsCount']=array_sum($itemsCount);
		$data['items']=$items;
		$this->load->view('admin/dashboard', $data);
	}

	
	public function getLocationscount(){

		$apps = $this->admin->getArray("",[],[],mongodb.".tbl_apps");		
		$locations = [];
		
		foreach($apps as $val){
			
			$table1 = mongodb."_".$val->appId;
			
			$this->mongo_db->switch_db($table1);
			$locations[] = $this->mongo_db->count("tbl_locations");
			
		}
		$this->mongo_db->switch_db(mongodb);
		return array_sum($locations);
	}
	public function updateData(){
		$mng = $this->admin->Mconfig();
		// $n = explode("#","Chep 48X40 Block Pallet#ongpool_OID001");
		$n = explode("#",$this->input->post("data"));
		$item = $n[0];
		$db = $n[1];
		$shipments=$this->admin->getArray($mng,[],['sort'=>['_id'=>-1],'limit'=>10],$db.".tbl_issues");
		$pickups=$this->admin->getArray($mng,[],['sort'=>['_id'=>-1],'limit'=>10],$db.".tbl_returns");
		$transfers=$this->admin->getArray($mng,[],['sort'=>['_id'=>-1],'limit'=>10],$db.".tbl_touts");
		$adjustments=$this->admin->getArray($mng,[],['sort'=>['_id'=>-1],'limit'=>10],$db.".tbl_adjustments");
		echo json_encode(array("Shipments"=>$shipments,"Pickups"=>$pickups,"Transfers"=>$transfers,"Adjustments"=>$adjustments,"Data"=>$n));
	}
	
	public function profile(){
		
		$this->load->view('admin/profile');
		
	}
	
	public function updateProfilepic(){
		
	  $uemail = $this->session->userdata("admin_email");	
	  $logo = $this->mongo_db->get_where("tbl_auths",array("email"=>$uemail))[0];					

	  if($_FILES['profile_pic']['size']!='0'){
			//profile pic uploading
		  		$config['upload_path']          = "uploads/users/";
                $config['allowed_types']        = 'gif|jpg|png|jpeg';
//                $config['encrypt_name']             = TRUE;
//			    $config['max_width']            = 450;
//				$config['max_height']           = 80;		
        $this->load->library('upload', $config);
		
		$dd=$this->upload->do_upload("profile_pic");
		
			if($dd){  
				$d=$this->upload->data();

				$nimage = "uploads/users/".$d['file_name'];

				unlink($logo["profile_pic"]);
			}else{
				$this->alert->pnotify("error","Please Select Valid Image Format Or Dimensions","error");
//				redirect("admin/dashboard/profile");
				
				echo 'imgerror';
			}
		}else{
			
			
			$nimage=$logo["profile_pic"];
			
		}
		
		$d = $this->mongo_db->where(array('email'=>$uemail))->set(array("profile_pic"=>$nimage))->update('tbl_auths');
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'error';
			
		}
		
	}
	
	public function changePassword(){
		
		$opass = $this->input->post("opass");
		$npass = $this->input->post("npass");
		$cpass = $this->input->post("cpass");
		
		$uemail = $this->session->userdata("admin_email");	
		$udata = $this->mongo_db->get_where("tbl_auths",array("email"=>$uemail))[0];					

		if($this->secure->decrypt($udata["password"]) != $opass){
			
			echo "oldwrong";
			exit();
			
		}
		
		if($npass != $cpass){
			
			echo "notmatched";
			exit();
			
		}
		
		$d = $this->mongo_db->where(array('email'=>$uemail))->set(array("password"=>$this->secure->encrypt($npass)))->update('tbl_auths');
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'fail';
			
		}
		
		
	}
	
	public function logout(){
		
		$this->session->sess_destroy();
		redirect("login");
		
	}
	
}
