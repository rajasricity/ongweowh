<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locations extends CI_Controller {

	public function __construct(){
		
		parent::__construct();
		
		if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}
		
	}
	
	public function index()
	{
		$ldata = array();
		
		$query = $this->mongo_db->get_where("tbl_auths",array("email"=>$this->session->userdata("admin_email")));
		
		$locations = $query[0]["locations"];
		
		
		foreach($locations as $q){
			
		
			$lid = $q->LocationId;
		
			$ldata[] = $this->mongo_db->get_where("tbl_locations",array("loccode"=>$lid))[0];

		}
		
		$data["loc"] = $ldata;
		
		$this->load->view('user/locations/allLocations',$data);
	}
	
	public function createLocation(){
		
		$this->load->view('admin/locations/createLocation');
		
	}
	
	public function editLocation($uid){
		
		$id = new MongoDB\BSON\ObjectID($uid);
		
		$data['l'] = $this->mongo_db->get_where("tbl_locations",array('_id' => $id));
		
		$this->load->view('admin/locations/createLocation',$data);
		
	}
	
	public function insertLocation(){
		
		$address = $this->input->post("address");
		$city = $this->input->post("city");
		$state = $this->input->post("state");
		$country = $this->input->post("country");
		$lat = $this->input->post("lat");
		$lon = $this->input->post("lon");
		
		$data = array(
		
			"address" => $address,
			"city" => $city,
			"state" => $state,
			"country" => $country,
			"lat" => $lat,
			"lon" => $lon,
			"deleted" => 0,
			"status" => "Active",
			"cdate" => date("m-d-Y H:i:s")
		
		);
		
		
		$d = $this->mongo_db->insert("tbl_locations",$data);
		
		if($d){
			
			echo "success";
			
		}else{
			
			echo "error";
			
		}
		
	}
	
	public function updateLocation(){
		
		$address = $this->input->post("address");
		$city = $this->input->post("city");
		$state = $this->input->post("state");
		$country = $this->input->post("country");
		$lat = $this->input->post("lat");
		$lon = $this->input->post("lon");
		$status = $this->input->post("status");
		$id = new MongoDB\BSON\ObjectID($this->input->post("id"));
		
		$data = array(
		
			"address" => $address,
			"city" => $city,
			"state" => $state,
			"country" => $country,
			"lat" => $lat,
			"lon" => $lon,
			"status" => $status
		
		);
		
		
		$d = $this->mongo_db->where(array('_id'=>$id))->set($data)->update('tbl_locations');
		
		if($d){
			
			echo "success";
			
		}else{
			
			echo "error";
			
		}
		
	}
	
	public function delLocation($id){
		
		$lid = new MongoDB\BSON\ObjectID($id);
		
		$d = $this->mongo_db->where(array('_id'=>$lid))->delete('tbl_locations');
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'error';
			
		}
		
	}
	
}
