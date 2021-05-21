<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locations extends CI_Controller {

	public function __construct(){
		
		parent::__construct();
		
		if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
	}
	
	public function index(){
		$ldata = array();
		
		$query = $this->mongo_db->get_where("tbl_auths",array("email"=>$this->session->userdata("admin_email")));
		
		$locations = $query[0]["locations"];
		
		
		foreach($locations as $q){
			
			$lid = $q->LocationId;
			$ldata[] = $this->admin->getRow("",["loccode"=>$lid],[],"$this->database.tbl_locations");

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
	
	public function ChepAdmin(){
		
		$data['database'] = $this->database;
		$this->load->view('main/locations/chepAdmin',$data);
	}

	public function AdminLocations(){
		$data["database"] = $this->database;
		$this->load->view('main/locations/AdminLocations',$data);
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

	public function getDynamictouts($id,$appid,$item,$query){
		
		$this->mongo_db->switch_db($this->database);
		 
		$out = [];
		 //Transfer Out Open
//		 if($query == 'transferout'){
		
//		print_r($_POST);
//		exit();
			 
			$draw = $this->input->post('draw');
			$rowstart = $this->input->post('start');
			$rowperpage = $this->input->post('length'); // Rows display per page
			$columnIndex = $this->input->post('order')[0]['column']; // Column index
			$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
			$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
			$searchValue = $this->input->post('search')['value']; // Search value

			$totalRecords = $this->mongo_db->where(["appId"=>$appid,"flcoationcode"=>urldecode($id),"item"=>urldecode($item)])->count("tbl_touts");

			if($searchValue != ''){

				$this->mongo_db->where_or([
					'shipperpo'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'pronum'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlcoation'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'reportdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

			}
			$empRecords = $this->mongo_db->select(["shipperpo","shippmentdate","pronum","tlcoation","quantity","reportdate"])->order_by(array("$columnName"=>"$columnSortOrder","_id"=>"desc"))->where(["appId"=>$appid,"flcoationcode"=>urldecode($id),"item"=>urldecode($item)])->limit(intval($rowperpage))->offset(intval($rowstart))->get("tbl_touts");


			foreach($empRecords as $key=>$row){
				$row["Sno"] = $key+1;
				if($row["reportdate"]){

				}else{
				 $row["reportdate"] = '';
				}
				
				array_push($out,$row);
			}
			 	
//		 }
	
		$response = array(
		  "draw" => intval($draw),
		  "iTotalRecords" => $totalRecords,
		  "iTotalDisplayRecords" => $totalRecords,
		  "aaData" => $out
		);
		
		echo json_encode($response);
		
	}
	
	
	public function getDynamicData($id,$appid,$item,$query){
		
		$this->mongo_db->switch_db($this->database);
		 
		$out = [];
		 //Transfer Out Open
		 if($query == 'transferout'){
			 
			$draw = $this->input->post('draw');
			$rowstart = $this->input->post('start');
			$rowperpage = $this->input->post('length'); // Rows display per page
			$columnIndex = $this->input->post('order')[0]['column']; // Column index
			$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
			$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
			$searchValue = $this->input->post('search')['value']; // Search value

			$totalRecords = $this->mongo_db->where(["appId"=>$appid,"flcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->count("tbl_touts");

			if($searchValue != ''){

				$this->mongo_db->where_or([
					'shipperpo'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'pronum'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlcoation.locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'reportdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

			}
			$empRecords = $this->mongo_db->select(["shipperpo","shippmentdate","pronum","tlcoation","quantity","reportdate"])->order_by(array("$columnName"=>"$columnSortOrder","_id"=>"desc"))->where(["appId"=>$appid,"flocation.status"=>"Active","item.status"=>"Active","flcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->limit(intval($rowperpage))->offset(intval($rowstart))->get("tbl_touts");


			foreach($empRecords as $key=>$row){
				$row["Sno"] = $key+1;
				$shipmentdate=$this->common->getConverteddate($row["shippmentdate"]);
				$reportdate=$this->common->getConverteddate(explode(" ",$row["reportdate"])[0]);
				
				$row["shippmentdate"] = ($row["shippmentdate"] != "") ? date("m-d-Y",strtotime($shipmentdate)) : "";
				$row["reportdate"] = ($row["reportdate"][0] != "") ? date("m-d-Y",strtotime($reportdate)) : "";
				$row["tlcoation"] = $row["tlcoation"]->locname;

				array_push($out,$row);
			}
			 
			if($searchValue != ''){

				$this->mongo_db->where_or([
					'shipperpo'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'pronum'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlcoation.locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'reportdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

				$fRecords = $this->mongo_db->select(["shipperpo","shippmentdate","pronum","tlcoation","quantity","reportdate"])->where(["appId"=>$appid,"flcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->get("tbl_touts");

				$filteredRecords = count($fRecords);
			}else{
				
				$filteredRecords = $totalRecords;
				
			}
		 }
		 //Transfer Out Close

		 //Transfer In Open
		 if($query == 'transferin'){
		 	
			$draw = $this->input->post('draw');
			$rowstart = $this->input->post('start');
			$rowperpage = $this->input->post('length'); // Rows display per page
			$columnIndex = $this->input->post('order')[0]['column']; // Column index
			$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
			$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
			$searchValue = $this->input->post('search')['value']; // Search value

			$totalRecords = $this->mongo_db->where(["appId"=>$appid,"tlocationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->count("tbl_touts");

			if($searchValue != ''){

				$this->mongo_db->where_or([
					'shipperpo'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'pronum'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlcoation.locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'reportdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

			}
			$empRecords = $this->mongo_db->select(["shipperpo","shippmentdate","pronum","tlcoation","flocation","quantity","reportdate"])->order_by(array("$columnName"=>"$columnSortOrder","_id"=>"desc"))->where(["appId"=>$appid,"tlcoation.status"=>"Active","item.status"=>"Active","tlocationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->limit(intval($rowperpage))->offset(intval($rowstart))->get("tbl_touts");


			foreach($empRecords as $key=>$row){
				$row["Sno"] = $key+1;
								
				$shipmentdate=$this->common->getConverteddate($row["shippmentdate"]);
				$reportdate=$this->common->getConverteddate(explode(" ",$row["reportdate"])[0]);
				
				$row["shippmentdate"] = ($row["shippmentdate"] != "") ? date("m-d-Y",strtotime($shipmentdate)) : "";
				$row["reportdate"] = ($row["reportdate"][0] != "") ? date("m-d-Y",strtotime($reportdate)) : "";
				$row["flocation"] = $row["flocation"]->locname;

				array_push($out,$row);
			} 
			 
			if($searchValue != ''){

				$this->mongo_db->where_or([
					'shipperpo'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'pronum'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlcoation.locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'reportdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

				$fRecords = $this->mongo_db->select(["shipperpo","shippmentdate","pronum","tlcoation","flocation","quantity","reportdate"])->where(["appId"=>$appid,"tlocationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->get("tbl_touts");

				$filteredRecords = count($fRecords);
			}else{
				
				$filteredRecords = $totalRecords;
				
			} 
			 
		 }
		 //Transfer In Close

		 //Issues Open
		 if($query == 'issues'){
			
			$draw = $this->input->post('draw');
			$rowstart = $this->input->post('start');
			$rowperpage = $this->input->post('length'); // Rows display per page
			$columnIndex = $this->input->post('order')[0]['column']; // Column index
			$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
			$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
			$searchValue = $this->input->post('search')['value']; // Search value

			$totalRecords = $this->mongo_db->where(["appId"=>$appid,"tlcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->count("tbl_issues");

			if($searchValue != ''){

				$this->mongo_db->where_or([
					'chepreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'ongreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

			}
			$empRecords = $this->mongo_db->select(["chepreference","ongreference","shippmentdate","tlocation","quantity"])->order_by(array("$columnName"=>"$columnSortOrder","_id"=>"desc"))->where(["appId"=>$appid,"tlocation.status"=>"Active","item.status"=>"Active","tlcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->limit(intval($rowperpage))->offset(intval($rowstart))->get("tbl_issues");


			foreach($empRecords as $key=>$row){
				$row["Sno"] = $key+1;
				
				$shipmentdate=$this->common->getConverteddate($row["shippmentdate"]);
				$row["shippmentdate"] = ($row["shippmentdate"] != "") ? date("m-d-Y",strtotime($shipmentdate)) : "";
				$row["tlocation"] = $row["tlocation"]->locname;

				array_push($out,$row);
			}
			 
			if($searchValue != ''){

				$this->mongo_db->where_or([
					'chepreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'ongreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

				$fRecords = $this->mongo_db->select(["chepreference","ongreference","shippmentdate","tlocation","quantity"])->where(["appId"=>$appid,"tlcoationcode"=>urldecode($id),"item"=>urldecode($item)])->get("tbl_issues");

				$filteredRecords = count($fRecords);
				
			}else{
				
				$filteredRecords = $totalRecords;
				
			} 
		 }
		 //Issues Close

		  //Returns Open
		 if($query == 'returns'){
			
			$draw = $this->input->post('draw');
			$rowstart = $this->input->post('start');
			$rowperpage = $this->input->post('length'); // Rows display per page
			$columnIndex = $this->input->post('order')[0]['column']; // Column index
			$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
			$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
			$searchValue = $this->input->post('search')['value']; // Search value

			$totalRecords = $this->mongo_db->where(["appId"=>$appid,"tlcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->count("tbl_returns");

			if($searchValue != ''){

				$this->mongo_db->where_or([
					'chepreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'ongreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

			}
			$empRecords = $this->mongo_db->select(["chepreference","ongreference","shippmentdate","tlocation","quantity"])->order_by(array("$columnName"=>"$columnSortOrder","_id"=>"desc"))->where(["appId"=>$appid,"tlocation.status"=>"Active","item.status"=>"Active","tlcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->limit(intval($rowperpage))->offset(intval($rowstart))->get("tbl_returns");


			foreach($empRecords as $key=>$row){
				$row["Sno"] = $key+1;
				$shipmentdate=$this->common->getConverteddate($row["shippmentdate"]);
				$row["shippmentdate"] = ($row["shippmentdate"] != "") ? date("m-d-Y",strtotime($shipmentdate)) : "";
				$row["tlocation"] = $row["tlocation"]->locname;
				array_push($out,$row);
			}
			 
			if($searchValue != ''){

				$this->mongo_db->where_or([
					'chepreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'ongreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

				$fRecords = $this->mongo_db->select(["chepreference","ongreference","shippmentdate","tlocation","quantity"])->where(["appId"=>$appid,"tlcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->get("tbl_returns");

				$filteredRecords = count($fRecords);
				
			}else{
				
				$filteredRecords = $totalRecords;
				
			} 
			 
		 }
		 //Returns Close

		 //Adjustments Open
		 if($query == 'adjustments'){
			 
		    $draw = $this->input->post('draw');
			$rowstart = $this->input->post('start');
			$rowperpage = $this->input->post('length'); // Rows display per page
			$columnIndex = $this->input->post('order')[0]['column']; // Column index
			$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
			$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
			$searchValue = $this->input->post('search')['value']; // Search value

			$totalRecords = $this->mongo_db->where(["appId"=>$appid,"tlcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->count("tbl_adjustments");

			if($searchValue != ''){

				$this->mongo_db->where_or([
					'chepreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'ongreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

			}
			$empRecords = $this->mongo_db->select(["chepreference","ongreference","shippmentdate","tlocation","quantity"])->order_by(array("$columnName"=>"$columnSortOrder","_id"=>"desc"))->where(["appId"=>$appid,"tlocation.status"=>"Active","item.status"=>"Active","tlcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->limit(intval($rowperpage))->offset(intval($rowstart))->get("tbl_adjustments");


			foreach($empRecords as $key=>$row){
				$row["Sno"] = $key+1;
				
				$shipmentdate=$this->common->getConverteddate($row["shippmentdate"]);
				$row["shippmentdate"] = ($row["shippmentdate"] != "") ? date("m-d-Y",strtotime($shipmentdate)) : "";
				$row["tlocation"] = $row["tlocation"]->locname;
				
				array_push($out,$row);
			}
			 
			if($searchValue != ''){

				$this->mongo_db->where_or([
					'chepreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'ongreference'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'quantity'=>new MongoDB\BSON\Regex($searchValue,'i'),
				]);

				$fRecords = $this->mongo_db->select(["chepreference","ongreference","shippmentdate","tlocation","quantity"])->where(["appId"=>$appid,"tlcoationcode"=>urldecode($id),"item.item_name"=>urldecode($item)])->get("tbl_adjustments");

				$filteredRecords = count($fRecords);
				
			}else{
				
				$filteredRecords = $totalRecords;
				
			} 
		 }
		 //Adjustments Close
		 
		$response = array(
		  "draw" => intval($draw),
		  "iTotalRecords" => $totalRecords,
		  "iTotalDisplayRecords" => $filteredRecords,
		  "aaData" => $out
		);
		
		echo json_encode($response);
	}

	public function getGraphData(){
		$mng = $this->admin->Mconfig();
		// $tlocation = "4000293461";
		$tlocation = $this->input->post('loccode');
		$mng = $this->admin->Mconfig();
		$out = [];
		$command = new MongoDB\Driver\Command([
			    'aggregate' => 'tbl_touts',
			    'pipeline' => [
			    	['$match'=>['flcoationcode'=>$tlocation]],
			        ['$group' => ['_id' => '$tlocationcode', 'tolocation'=>['$addToSet'=>'$tlcoation'], 'sum'=>['$sum'=>['$toDouble'=>'$quantity']]]],
			    ],
			    'cursor' => new stdClass,
			]);
		$cursor = $mng->executeCommand("$this->database", $command);
		$out=[];
		foreach($cursor as $value){
			array_push($out, array("Code"=>$value->_id,"Quantity"=>$value->sum,"Location"=>$value->tolocation[0]));
			//echo $value->_id." - ".$value->sum.'<br/>';
			}
			// array_push($out,$tlocation);
			echo json_encode($out);
		}
	
}
