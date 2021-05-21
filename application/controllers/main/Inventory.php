<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH.'vendor/autoload.php';

class Inventory extends CI_Controller {

	public function __construct(){
		
		parent::__construct();
		
		if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
		$this->mdb = mongodb;
		
	}
	
	public function getToutsgraphdata(){
		
		for ($i = 0; $i <= 11; $i++) {
			$months[] = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
			$mname[] = date("M-Y", strtotime( date( 'Y-m-01' )." -$i months"));
		}
		$colors = ["#808080","#000000","#FF0000","#800000","#FFFF00","#808000","#00FF00","#008000","#00FFFF","#008080","#0000FF","#000080"];
		
		$this->mongo_db->switch_db($this->database);

		$loccode = $this->input->post("loccode");
		$item = $this->input->post("item");
		$appid = $this->input->post("appid");
		
		
		$data = [];
		foreach($months as $kk => $mon){
		
			$total = 0;
			
//			$mdate = explode("-",$mon)[1];
			
			$touts = $this->mongo_db->select(["quantity","shippmentdate"])->where(["appId"=>$appid,"flcoationcode"=>"$loccode","item.item_name"=>"$item","item.status"=>"Active","flocation.status"=>"Active","tlcoation.status"=>"Active"])->like("shippmentdate","$mon")->get("tbl_touts");
			
			if(count($touts) > 0){
		
				foreach($touts as $tout){

					$sdate = explode("-",date("Y-m",strtotime($tout['shippmentdate'])));
					$smname = date("M",strtotime($tout['shippmentdate']));
					$shdate = $sdate[0]."-".$sdate[1];

					if(in_array($shdate,$months)){
						
						$total += $tout["quantity"];
						$data[$smname] = [$smname."-".$sdate[0],$total,$colors[$kk]];

					}

				}

			}else{
					
				$data[$mname[$kk]] = [$mname[$kk],$total,$colors[$kk]];

			}
		}
		
		echo json_encode(array_values($data));
		
	}
	
	public function getToutsbarchartbymonth(){
		
		
		$this->mongo_db->switch_db($this->database);

		$loccode = $this->input->post("loccode");
		$item = $this->input->post("item");
		$appid = $this->input->post("appid");
		$month = explode("-",$this->input->post("month"))[1];
		
		$mon = $month."-".date("m",strtotime($this->input->post("month")));	
		
		
		$touts = $this->mongo_db->aggregate("tbl_touts",[
				['$match' => ["appId"=>$appid,"flcoationcode"=>"$loccode","item.item_name"=>"$item","item.status"=>"Active","flocation.status"=>"Active","tlcoation.status"=>"Active",'shippmentdate' => new MongoDB\BSON\Regex($mon,'i')]],
				['$group' => ["_id"=>'$shippmentdate',"totalQty"=>['$sum'=>'$quantity']]],
			]);
			
		$data = [];
		$data[] = ["Task","count",["role" => "style"]];
		
//			$total = 0;
			
//			$touts = $this->mongo_db->select(["quantity","shippmentdate"])->where(["appId"=>$appid,"flcoationcode"=>"$loccode","item.item_name"=>"$item","item.status"=>"Active","flocation.status"=>"Active","tlcoation.status"=>"Active"])->like("shippmentdate","$mon")->get("tbl_touts");
		
			foreach($touts as $tout){

				$smname = date("M-d",strtotime($tout['_id']));
				$colors = ["#808080","#000000","#800000","#808000","#008000","#00FFFF","#008080","#0000FF","#000080"];
				$key = array_rand($colors);
				$data[$smname] = [$smname,$tout["totalQty"],$colors[$key]];

			}
		
		
		echo json_encode(array_merge(array_values($data)));
		
	}

	
	public function index()
	{
		// $ldata = array();
		
		// $query = $this->mongo_db->get_where("tbl_auths",array("email"=>$this->session->userdata("admin_email")));
		
		// $locations = $query[0]["locations"];
		
		
		// foreach($locations as $q){
			
		
		// 	$lid = $q->LocationId;
		
		// 	$ldata[] = $this->mongo_db->get_where("tbl_locations",array("loccode"=>$lid))[0];

		// }
		
		// $data["loc"] = $ldata;
		
		// $this->load->view('user/locations/allLocations',$data);
	}

	public function location($id,$str){
		$data['id']=$id;
		
		$data['show']=$str;
		$data['database']=$this->database;
		$this->load->view('main/locations/locations',$data);
		
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
			"cdate" => date("Y-m-d H:i:s")
		
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

	public function saveshipment(){
		$mng=$this->admin->Mconfig();
		$main = [];
		$main = $this->input->post();
		
		date_default_timezone_set("US/Eastern");
		$main['cdate']=date("Y-m-d h:i:s A", time());
$main['deleted']="0";		
$count = $this->admin->getCount($mng,"$this->database.tbl_touts",["shipperpo"=>$this->input->post('shipperpo'),"item.item_name"=>$main['item']],[]);
$warning='';
		if($count > 0){
			$warning = "Shipper PO already existed";
		}

	$flocname = $this->admin->getReturn($mng,"$this->database.tbl_locations",["loccode"=>$main['flcoationcode']],[],"locname");
	$tlocname = $this->admin->getReturn($mng,"$this->database.tbl_locations",["loccode"=>$main['tlocationcode']],[],"locname");
	$main['flocation']=$flocname;
	$main['tlcoation']=$tlocname;

	$this->mongo_db->switch_db($this->database);

	$flocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$main["flocation"]])[0];
	$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$main["tlcoation"]])[0];
	$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$main["item"]])[0];

	$main["flocation"] = ["id"=>$flocdata["_id"]->{'$id'},"locname"=>$flocdata["locname"],"loccode"=>$flocdata["loccode"],"status"=>$flocdata["status"]];

	$main["tlcoation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];

	$main["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];
		
	$main['importtolocation']="";$main['importtolocationcode']="";$main['importtoaddress']="";$main['importtocity']="";$main['importtostate']="";$main['importtozip']="";$main['importtocountry']="";
	$main['reportdate']="";$main['rcvdate']="";$main['processdate']="";$main['chepprocessdate']="";$main['chepumi']="";$main['uploadedetochep']="NO";$main['reasonforhold']="";
	$main['locid']="";$main['locid_wrecid']="";$main['notes_general']="";$main['dupid']="";$main['program']="";$main['type']="";$main['jnj_id']="";
	$main['chepreference']="";
			$main['ongreference']="";
			$main['reportdate']=date("Y-m-d");
			$main['shippmentdate']=date("Y-m-d",strtotime($this->input->post("shippmentdate")));
			$main['quantity']=intval($this->input->post("quantity"));
			$main["id"] = $this->admin->insert_id("tbl_touts",$this->database);
			
			
// update location inventory		
		
		$tins = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$main['appId'],$main["tlocationcode"],"tlocationcode",$main["item"]["item_name"],$main["quantity"],"transfer_ins");
		
		$touts = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$main['appId'],$main["flcoationcode"],"flcoationcode",$main["item"]["item_name"],$main["quantity"],"transfer_outs");
		
		
//		echo $tins." ".$touts;
//		exit();
// end update location inventory
			
			
			$bulk = new MongoDB\Driver\BulkWrite;
			$bulk->insert($main);
			$mng->executeBulkWrite("$this->database.tbl_touts", $bulk);
			echo json_encode(array("Status"=>"Success","Message"=>"Successfully Inserted","Warning"=>$warning));
		
		
	}


	public function requestLocation(){
		$mng=$this->admin->Mconfig();
		$main = [];
		$main = $this->input->post();
		date_default_timezone_set("US/Eastern");
		$main['Created_Date']=date("Y-m-d h:i:s A", time());
		$main['Status']="Pending";
		$main['Updated_Date']="";
		
		$d = $this->admin->mongoInsert("$this->database.location_requests",$main,"");
		
//		echo '<pre>';
//		print_r($d);
//		exit();
		
		echo json_encode(array("Status"=>"Success","Message"=>"Your request has been submitted successfully."));
	}

	public function addLocation(){
		$mng=$this->admin->Mconfig();
		$main = [];
		$main = $this->input->post();
		date_default_timezone_set("US/Eastern");
		$main['Created_Date']=date("Y-m-d h:i:s A", time());
		$main['Status']="Pending";
		$main['Updated_Date']="";
		
		$this->admin->mongoInsert("$this->database.location_submits",$main,"");
		
		echo json_encode(array("Status"=>"Success","Message"=>"Your location request has been submitted successfully."));
	}

	public function getInventoryChepAdmin($item){
		$appid= $this->session->userdata("appid");

		$this->mongo_db->switch_db($this->database);
	
		$draw = $this->input->post('draw');
		$row = $this->input->post('start');
		$rowperpage = $this->input->post('length'); // Rows display per page
		$columnIndex = $this->input->post('order')[0]['column']; // Column index
		$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
		$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
		$searchValue = $this->input->post('search')['value']; // Search value
		
		$totalRecords = $this->mongo_db->where(["appId"=>$appid,"last_report_date"=>['$ne'=>" "],"item"=>urldecode($item)])->count("tbl_inventory");

		if($searchValue != ''){
		
			$this->mongo_db->where_or([
					'location'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'last_report_date'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'issues'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'returns'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'transfer_ins'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'transfer_outs'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'adjustments'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'ending_balance'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'audit_count2019'=>new MongoDB\BSON\Regex($searchValue,'i'),
			]);
			
		}
		$empRecords = $this->mongo_db->where(["appId"=>$appid,"last_report_date"=>['$ne'=>" "],"item"=>urldecode($item)])->order_by(array("$columnName"=>"$columnSortOrder","_id"=>"desc"))->limit(intval($rowperpage))->offset(intval($row))->get("tbl_inventory");
		
		$out = [];
		
		foreach($empRecords as $key=>$row){
			$row["location"] = '<a href="'.base_url().'main/inventory/location/'.$row["loccode"].'/off/'.urldecode($item).'">'.$row["location"].'</a>';
			array_push($out,$row);
		}
		
		$response = array(
		  "draw" => intval($draw),
		  "iTotalRecords" =>  count($out),
		  "iTotalDisplayRecords" =>  count($out),
		  "aaData" => $out
		);

		echo json_encode($response);
	}

	public function getInventoryChepAdminConsolidated(){
		$appid= $this->session->userdata("appid");
		$mng = $this->admin->Mconfig();
		$out = [];
		$rows = $this->admin->getRows($mng,["appId"=>$appid,"last_report_date"=>['$ne'=>" "]],['sort'=>['_id'=>-1]],"$this->database.tbl_inventory");
		$issues = [];
		$returns = [];
		$tins = [];
		$touts = [];
		$adjustments = [];
		$ebal = [];
		$acount2017=[];
		$acount2018 = [];
		$acount2019=[];
		$varieance = [];

		foreach($rows as $key=>$row){
			array_push($issues, $row->issues);
			array_push($returns, $row->returns);
			array_push($tins, $row->transfer_ins);
			array_push($touts, $row->transfer_outs);
			array_push($adjustments, $row->adjustments);
			array_push($ebal, $row->ending_balance);
//			array_push($acount2017, $row->{'audit_count2017'});
//			array_push($acount2018, $row->{'audit_count2018'});
			array_push($acount2019, $row->{'audit_count2019'});
			array_push($varieance, $row->variance);
		}
		array_push($out, array("issues"=>array_sum($issues),"returns"=>array_sum($returns),"tins"=>array_sum($tins),"touts"=>array_sum($touts),"adjustments"=>array_sum($adjustments),"ebal"=>array_sum($ebal),"acount2017"=>array_sum($acount2017),"acount2018"=>array_sum($acount2018),"acount2019"=>array_sum($acount2019),"varieance"=>array_sum($varieance)));
		
		echo json_encode($out);
	}
	
	public function importTouts(){
		
		$user = $this->admin->getRow("",['email'=>$this->session->userdata('admin_email')],[],"$this->mdb.tbl_auths");
		
		$username = $user->uname;
		$floc = [];
		$tloc = [];
		foreach($user->locations as $key=>$location){
		  if($location->Type == 'from'){
			array_push($floc, $location);
		  }else{
			array_push($tloc, $location);
		  }
		}
		
		$fromlocations = [];
		$floccode = [];
		foreach($floc as $fl){
			
			$fromlocations[] = $fl->LocationName;
			$floccode[] = $fl->loccode;
			
		}
		
		$tolocations = [];
		$toloccode = [];
		foreach($tloc as $tl){
			
			$tolocations[] = $tl->LocationName;
			$toloccode[] = $tl->loccode;
			
		}
		
		if(isset($_FILES["ldata"]["name"])){
			
			$database = $this->database;
			$this->mongo_db->switch_db($database);
			
			$config['upload_path'] = 'uploads/exceldata/';
			$config['allowed_types'] = 'xlsx|xls|csv';
			$this->load->library('upload', $config);
			$this->upload->do_upload('ldata');
			
			
			$upload_data = $this->upload->data();
			$file_name = $upload_data['file_name'];

			 $path = FCPATH.'uploads/exceldata/'.$file_name;
//			 $path = FCPATH.'uploads/exceldata/sample.xlsx';
			 $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			 $spreadsheet = $reader->load($path)->getSheet(0);
			 $worksheet = $spreadsheet;
			
			 $appId = $this->input->post("appId");
			 $user = $this->input->post("user");
			
			$data = [];
			$error = [];
			$warnings = [];
			$batchSize = 1000;
			/* foreach($spreadsheet as $worksheet)
			   {*/
			   	
				$highestRow = $worksheet->getHighestRow();
				$hrow = $worksheet->getHighestRow();
				$highestColumn = 5;//$worksheet->getHighestColumn();
					//print_r($highestRow); exit();
			
				// $excel_fields = ["locname","loccode","address","city","state","zip","country","status","Type","import_date","deleted","appId","cdate"];
				$excel_fields = ["shipperpo","shippmentdate","pronum","reference","item","flocation","flcoationcode","tlcoation","tlocationcode","quantity","user","deleted","appId","cdate",'reportdate','uploadedetochep'];
				$ndata = array();	
			
				$aid = 0;
				$autoid = explode("_",$this->admin->insert_id("tbl_touts",$this->database))[1];
			    $starting_id = $this->admin->insert_id("tbl_touts",$this->database);
			
				for($i=2; $i<=$highestRow; $i++){

						foreach ($excel_fields as $key => $value){

							$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();

							if($new_val ==""){
								$final = " ";
							}else{
								$final = strval($new_val);
							}

						   $data[$value] = $final;
							

						
						   /*if($value == "shippmentdate"){
							   
							   	$excel_date = $new_val; //here is that value 41621 or 41631
								$unix_date = ($excel_date - 25569) * 86400;
								$excel_date = 25569 + ($unix_date / 86400);
								$unix_date = ($excel_date - 25569) * 86400;
								$new_val = gmdate("Y-m-d", $unix_date);	

							    $data["shippmentdate"] = $new_val;

						   }*/	

						   if($value == "deleted"){

							   $data["deleted"] = 0;

						   }

						   if($value == "appId"){

							   $data["appId"] = $appId;

						   }
						   if($value == "user"){

							   $data["user"] = $user;

						   }

						   if($value == "cdate"){

							   $data["cdate"] = date("Y-m-d H:i:s");

						   }
							
						   if($value == "reportdate"){

							   $data["reportdate"] = date("Y-m-d");

						   }
							if($value == "uploadedetochep"){

							   $data["uploadedetochep"] = "No";

						   }
							
						}
					
					if(strpos($data["shippmentdate"],"-")){
						$mp=explode("-",$data["shippmentdate"]);
						$ndate = $mp[1]."-".$mp[0]."-".$mp[2];
						$sdate = $mp[2]."-".$mp[0]."-".$mp[1];
						$cdate = date("Y-m-d");
					}else if(strpos($data["shippmentdate"],"/")){
						$mp=explode("/",$data["shippmentdate"]);
						$ndate = $mp[1]."-".$mp[0]."-".$mp[2];
						$sdate = $mp[2]."-".$mp[0]."-".$mp[1];
						$cdate = date("Y-m-d");
					}
						$vd=date("Y", strtotime($ndate));
						
					$spo = $data['shipperpo'];
					
					if($vd < 2015){
						array_push($error,array("Msg"=>"Year must be above 2015","Error"=>$data["shippmentdate"]));
					}elseif(strtotime($sdate) > strtotime($cdate)){
						array_push($error,array("Msg"=>"shipment date cannot be a future date for shipper PO $spo  ","Error"=>$data["shippmentdate"]));
					}
					if(($this->mongo_db->where(['shipperpo'=>$data["shipperpo"]])->count("tbl_touts"))>0){
						array_push($warnings,array("Msg"=>"Shipper PO ".$data["shipperpo"]." exists for From Location ".$data["flocation"]."","Error"=>$data["shipperpo"]));	
					}
					if(($this->mongo_db->where(['item_name'=>$data["item"]])->count("tbl_items")) == 0){
						array_push($error,array("Msg"=>"Unknown Item Found","Error"=>$data["item"]));	
					}
					
			// location validation starts					
			 		
					// if(in_array($data['flocation'],$fromlocations)){
						
					// }else{
						
					// 	array_push($error,array("Msg"=>"From Location for shipper PO - $spo cannot be blank (or) Invalid location. Please enter a valid From location","Error"=>""));	
						
					// }
					
					// if(in_array($data['tlcoation'],$tolocations)){
						
					// }else{
						
					// 	array_push($error,array("Msg"=>"To Location for shipper PO - $spo cannot be blank (or) Invalid location. Please enter a valid To location","Error"=>""));	
						
					// }
					
			// location validation ends
					
			// loccode validation starts
					
					if(in_array($data['flcoationcode'],$floccode)){
						
					}else{
						
						array_push($error,array("Msg"=>"From Location code for shipper PO - $spo cannot be blank (or) Invalid location code. Please enter a valid From location code","Error"=>""));	
						
					}
					
					if(in_array($data['tlocationcode'],$toloccode)){
						
					}else{
						
						array_push($error,array("Msg"=>"To Location code for shipper PO - $spo cannot be blank (or) Invalid location code. Please enter a valid To location code","Error"=>""));	
						
					}
					
			// loccode validation ends
					
			// Quantity validation
					
					if($data['quantity'] <= 0){
						
						array_push($error,array("Msg"=>"Quantity must be greater than 0","Error"=>$data['quantity']));
						
					}
					
			// Quantity validation
					
					if(strlen($data['shipperpo']) > 13){
						
						array_push($error,array("Msg"=>"Shipper PO $spo, Character count must be 13  or less, please use the Reference #3 field for the remainder if character count exceeds 13","Error"=>$data['shipperpo']));
						
					}		
					
					$field = ["shipperpo","shippmentdate","pronum","reference","item","flocation","flcoationcode","tlcoation","tlocationcode","quantity","user","deleted","appId","cdate",'reportdate','uploadedetochep'];
					
					$ff = array_combine($field,$data);
					
					$ff['shippmentdate'] = $this->common->getConverteddate($ff['shippmentdate']);
						
					
					$prefix = $this->admin->getPrefix("tbl_touts");
					if($aid == 0){
					
						$ff["id"] = $prefix.$autoid;

					}else{
						
						$ff["id"] = $prefix.(intval($autoid++) + 1);
						
					}
					$ff["flag"] = "excel";
					
					$flocdata = $this->admin->getRow("",["loccode"=>$ff["flcoationcode"]],[],"$this->database.tbl_locations");
					$tlocdata = $this->admin->getRow("",["loccode"=>$ff["tlocationcode"]],[],"$this->database.tbl_locations");
					$itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");

					$ff["flocation"] = ["id"=>(string) $flocdata->_id,"locname"=>$flocdata->locname,"loccode"=>$flocdata->loccode,"status"=>$flocdata->status,'flag'=>'excel'];

					$ff["tlcoation"] = ["id"=>(string) $tlocdata->_id,"locname"=>$tlocdata->locname,"loccode"=>$tlocdata->loccode,"status"=>$tlocdata->status,'flag'=>'excel'];

					$ff["item"] = ["id"=>(string) $itemdata->_id,"item_name"=>$itemdata->item_name,"status"=>$itemdata->status];
					$ff['quantity'] = ($ff['quantity'] != "") ? intval($ff['quantity']) : intval(); 
					
					
					/*$tins = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$appId,$ff["tlocationcode"],"tlocationcode",$ff["item"]->item_name,$ff["quantity"],"transfer_ins");

					$touts = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$appId,$ff["flcoationcode"],"flcoationcode",$ff["item"]->item_name,$ff["quantity"],"transfer_outs");*/
					

//					echo $tins." ".$touts;
					
					$ndata[] = $ff;
					
					$aid++;
	
				}
			
				$ending_id = $ff["id"];
			
//				print_r($ndata);
//				exit();
			
				if((count($error)>0 || count($warnings)>0) && ($this->input->post("usubmit") != "Upload")){
						
					echo json_encode(array("Status"=>"Dups","Message"=>$error,"WarCount"=>count($warnings),"WarMsg"=>$warnings));	

				}else{
					
					$this->admin->mongoInsert("$this->database.tbl_touts",$ndata,"bulk");
					
					$start = $this->admin->getRow("",["id"=>$starting_id],[],"$this->database.tbl_touts");	 
					$end = $this->admin->getRow("",["id"=>$ending_id],[],"$this->database.tbl_touts");	 

					 $this->admin->mongoInsert("ongpool.tbl_import_data",["appId"=>$appId,"table"=>"tbl_touts","start"=>(string) $start->_id,"end"=>(string) $end->_id,"records"=>count($ndata),"status"=>"Queue","imported_user"=>$username],"");
					echo json_encode(array("Status"=>"success","Message"=>""));

				}

			
			
//					$this->admin->mongoInsert("$this->database.tbl_locations",$ndata,"bulk");					
			}	
	}
}
