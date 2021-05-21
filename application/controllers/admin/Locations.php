<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH.'vendor/autoload.php';
class Locations extends CI_Controller {

	public function __construct(){
		
		parent::__construct();
		
		if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
		$this->mdb = mongodb;
		
	}
	
	public function index(){
		
		$this->load->view('admin/locations/allLocations');
		
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
		$appid = $this->input->post("appId");
		
		$locname = $this->input->post("locname");
		$loctype = $this->input->post("Type");
		$loccode = $this->input->post("loccode");
		
		$zip = $this->input->post("zip");
		$date = $this->input->post('import_date');
		$time = $this->input->post('time');
		$notes = $this->input->post('notes');
		$accounts = $this->input->post('accounts');
		$status = $this->input->post('status');
		
		
		$pdata = $this->input->post();
		
		/*$valRulescheck = $this->common->checkValidationrules("tbl_locations",$this->input->post(),$appid,"");
		
		if($valRulescheck){
			
			echo $valRulescheck;
			exit();
			
		}*/
		
		if($this->input->post("import_date") != ""){
			$pdata['import_date'] = date("Y-m-d",strtotime($this->input->post("import_date")));
		    $pdata['import_time'] = $time;
		}else{
			$pdata['import_date'] = "";
		    $pdata['import_time'] = "";
		}
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_locations",$this->input->post(),$appid,"");
		
		if(count($conRulescheck) > 0){
			
			foreach($conRulescheck as $con){
			
				$pdata[$con['column']] = $con['value'];
				
			}
				
		}

		$pdata['nameid'] = $pdata['locname']." - ".$pdata['loccode'];
		$pdata['cdate'] = date("m-d-Y H:i:s");
		$pdata['deleted'] = 0;
		
		$row = $this->admin->getCount("","$this->database.tbl_locations",['loccode' => $loccode],[]);
		if($row > 0){
			
			echo "Location Code Already Exists";
			exit();
			
		}
		
		/*$data = array(
			"nameid" => $nameid,
			"address" => $address,
			"city" => $city,
			"state" => $state,
			"country" => $country,
			"loccode" => $loccode,
			"zip" => $zip,
			"locname" => $locname,
			"appId" => $appid,
			"Type" => $loctype,
			"import_date" => $importdate,
			"notes"=>$notes,
			"accounts"=>$accounts,
			"deleted" => 0,
			"status" => $status,
			"cdate" => date("m-d-Y H:i:s")
		
		);*/
		
		
//		$d = $this->mongo_db->insert("tbl_locations",$data);

		foreach($pdata as $pk => $pd){
			
			if($pk != "accounts"){
			
				$pdata[$pk] = trim($pd);	
			
			}
				
		}
		
		$pdata["locid"] = $this->admin->insert_id("tbl_locations",$this->database,"locid");
		
		$d = $this->admin->mongoInsert("$this->database.tbl_locations",$pdata);
		
		if($d){
			
			/*$linvid = $this->admin->insert_id("tbl_inventory",$this->database);
			
			$this->mongo_db->switch_db($this->database);
			$locid = $this->mongo_db->get_where("tbl_locations",["locid"=>$pdata["locid"]])[0];
			
			$linvdata = [	
							"id" => $linvid,	
							"location" => $pdata['nameid'],
							"locname" => ["id"=>$locid["_id"]->{'$id'},"locname"=>$pdata["locname"],"loccode"=>$pdata["loccode"],"status"=>$pdata["status"]],
							"loccode" => $pdata['loccode'],
							"loctype" => $pdata['Type'],
							"notes" => "",
							"last_report_date" => "",
							"starting_balance" => 0,
							"issues" => 0,
							"returns" => 0,
							"transfer_ins" => 0,
							"transfer_outs" => 0,
							"adjustments" => 0,
							"ending_balance" => 0,
							"audit_date2019" => "",
							"audit_count2019" => 0,
							"item" => "",
							"appId" => $appid,
							"deleted" => 0,
							"cdate" => date("Y-m-d H:i:s"),
							"udate" => ""];
			$this->admin->mongoInsert("$this->database.tbl_inventory",$linvdata);*/
			
			echo "success";
			
		}else{
			
			echo "error";
			
		}
		
	}
	
	public function updateLocation(){
	
		
		$locname = $this->input->post("locname");
		$loctype = $this->input->post("Type");
		$loccode = $this->input->post("loccode");
		$address = $this->input->post("address");
		$city = $this->input->post("city");
		$state = $this->input->post("state");
		$country = $this->input->post("country");
		$status = $this->input->post("status");
		$zip = $this->input->post("zip");
		$impdate = date("m-d-Y",strtotime($this->input->post("import_date")))." ".$this->input->post("time");
		$accounts = $this->input->post("accounts");
		$notes = $this->input->post("notes");
//		$loctest = $this->input->post("loctest");
		$appid = $this->input->post("appId"); 
		$id = new MongoDB\BSON\ObjectID($this->input->post("id"));
		$nameid = $locname." - ".$loccode;
		$time = $this->input->post("time"); 
		
		$pdata = $this->input->post();
		
//		$lchk = $this->admin->getRow("",['loccode' => $loccode],[],"$this->database.tbl_locations");
		

//		print_r($lchk->loccode);exit();
		
		/*if($lchk->loccode==$loccode){

			
		}else{
			
			$echk1 = $this->admin->getCount("","$this->database.tbl_locations",['loccode' => $loccode],[]);	
			if($echk1 > 0){
				echo "Location Code Already Exists";
				exit();
			}else{
				
			}
			
		}*/
		
		/*$valRulescheck = $this->common->checkValidationrules("tbl_locations",$this->input->post(),$appid,"");
		
		if($valRulescheck){
			
			echo $valRulescheck;
			exit();
			
		}*/
		
		if($this->input->post("import_date") != ""){
			$pdata['import_date'] = date("Y-m-d",strtotime($this->input->post("import_date")));
		    $pdata['import_time'] = $time;
		}else{
			$pdata['import_date'] = "";
		    $pdata['import_time'] = "";
		}
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_locations",$this->input->post(),$appid,"");
		
		if(count($conRulescheck) > 0){
			
			foreach($conRulescheck as $con){
			
				$pdata[$con['column']] = $con['value'];
				
			}
				
		}
		
		
		$pdata['nameid'] = $pdata['locname']." - ".$pdata['loccode'];
		
		$ldata = $this->admin->getRow("",["_id"=>$id],[],"$this->database.tbl_locations");

		foreach($pdata as $pk => $pd){
			
			if($pk != "accounts"){
			
				$pdata[$pk] = trim($pd);	
			
			}
				
		}
		
		if($pdata["accounts"]){
			
			$pdata["accounts"] = $accounts;
			
		}else{
			
			$pdata["accounts"] = [];
			
		}
		
		
		$d = $this->admin->mongoUpdate("$this->database.tbl_locations",array('_id'=>$id),$pdata,[]);
		
		if($d){
			
			if(($pdata['locname'] != $ldata->locname) || ($pdata['loccode'] != $ldata->loccode) || ($pdata['status'] != $ldata->status) || ($pdata['notes'] != $ldata->notes) || ($pdata['Type'] != $ldata->Type)){				
				
				$udata = ["id"=>$this->input->post("id"),"previous_name"=>$ldata->locname,"new_name"=>$pdata['locname'],"code"=>$pdata['loccode'],"status"=>$pdata['status'],"notes"=>$pdata['notes'],"loctype"=>$pdata['Type'],"appId"=>$appid];
				$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);
				
			}
			
			echo "success";
			
		}else{
			
			echo "error";
			
		}
		
	}
	
	public function delLocation($id){
		
		$lid = new MongoDB\BSON\ObjectID($id);
		
// update location info
		
		$appId = $_SESSION['appid'];
		
		$ldata = $this->admin->getRow("",["_id"=>$lid],[],"$this->database.tbl_locations");

		$this->mongo_db->switch_db($this->database);

// transfers		
		
		$this->mongo_db->where(["flcoationcode"=>$ldata->loccode])->delete_all("tbl_touts");
		$this->mongo_db->where(["tlocationcode"=>$ldata->loccode])->delete_all("tbl_touts");
		
// shipments		
		
		$this->mongo_db->where(["tlcoationcode"=>$ldata->loccode])->delete_all("tbl_issues");
		$this->mongo_db->where(["tlcoationcode"=>$ldata->loccode])->delete_all("tbl_returns");
		$this->mongo_db->where(["tlcoationcode"=>$ldata->loccode])->delete_all("tbl_adjustments");
		$this->mongo_db->where(["loccode"=>$ldata->loccode])->delete_all("tbl_inventory");
		
// update location info ends		
		
		$d = $this->admin->mongoDelete("$this->database.tbl_locations",array('_id'=>$lid),[]);
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'error';
			
		}
		
	}

	public function delColumn($id){
		
		$this->mongo_db->switch_db($this->database);
		$id= urldecode($id);
		$data = $this->mongo_db->where(array('table'=>"tbl_inventory"))->get('settings');
		// print_r($data[0]['labels']);
		$key = array_search($id,$data[0]['labels']);
		if(in_array($id, $data[0]['labels'])){
			unset($data[0]['labels'][$key]);
			$data[0]['labels'] = array_values($data[0]['labels']);
			unset($data[0]['columns'][$key]);
			$data[0]['columns'] = array_values($data[0]['columns']);
			unset($data[0]['dataType'][$key]);
			$data[0]['dataType'] = array_values($data[0]['dataType']);
			$this->mongo_db->where(array('table'=>"tbl_inventory"))->set(["columns"=>$data[0]['columns'],"labels"=>$data[0]['labels'],"dataType"=>$data[0]['dataType']])->update('settings');
			echo 'success';
		}else{
			echo "error";
		}

		// exit;
		// if($d){
			
		// 	echo 'success';
			
		// }else{
			
		// 	echo 'error';
			
		// }
		
	}

	public function excelupload(){
		
		if(isset($_FILES["ldata"]["name"])){
			
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
			
			 $appId = $this->input->post("appID");
			
			$data = [];
			$error = [];
			$batchSize = 1000;
			/* foreach($spreadsheet as $worksheet)
			   {*/
			   	
				$highestRow = $worksheet->getHighestRow();
				$hrow = $worksheet->getHighestRow();
				$highestColumn = 5;//$worksheet->getHighestColumn();
					//print_r($highestRow); exit();
			
				// $excel_fields = ["locname","loccode","address","city","state","zip","country","status","Type","import_date","deleted","appId","cdate"];
				$excel_fields = ["nameid","locname","loccode","address","city","state","zip","country","status","Type","import_date","accounts","notes","deleted","appId","cdate"];
				$ndata = array();	
			
					for($i=2; $i<=$highestRow; $i++){
						
						 	foreach ($excel_fields as $key => $value){

						 		$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();
								
						 		if($new_val ==""){
									$final = " ";
								}else{
									$final = $new_val;
								}
							   
							   $data[$value] = $final;
							   
							   if($value == "deleted"){
								   
								   $data["deleted"] = 0;
								   
							   }
							   
							   if($value == "appId"){
								   
								   $data["appId"] = $appId;
								   
							   }
							   
							   if($value == "cdate"){
								   
								   $data["cdate"] = date("m-d-Y H:i:s");
								   
							   }
							   
							   if($value == "loccode"){
								   
								   $data["loccode"] = strval($new_val);
								   
							   }
							   
							   if($value == "zip"){
								   
								   $data["zip"] = strval($new_val);
								   
							   }
								
							   if($value == "import_date"){
								   
								   $data["import_date"] = date("m-d-Y H:i:s");
								   
							   }	
								
								
						 	}
						
							$field = ["nameid","locname","loccode","address","city","state","zip","country","status","Type","import_date","accounts","notes","deleted","appId","cdate"];
					 		
						$ff = array_combine($field,$data);	
						
						$ndata[] = $ff;
						
						if($hrow > $batchSize){

							if (($i+1) >= $batchSize){

								$this->admin->mongoInsert("$this->database.tbl_locations",$ndata,"bulk");
								$ndata = [];
								$hrow = $hrow - $batchSize;

							}

						}else{

							$this->admin->mongoInsert("$this->database.tbl_locations",$ndata,"bulk");
							$ndata = [];

						}
						
					}
			
//					$this->admin->mongoInsert("$this->database.tbl_locations",$ndata,"bulk");					
			}
			
			echo 'success';
		
	}

	public function exceluploadTransfers(){
		
		if(isset($_FILES["ldata"]["name"])){
			
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
			
			 $appId = $this->input->post("appID");
			
			$data = [];
			$error = [];
			$batchSize = 1000;
			/* foreach($spreadsheet as $worksheet)
			   {*/
			   	
				$highestRow = $worksheet->getHighestRow();
				$hrow = $worksheet->getHighestRow();
				$highestColumn = 5;//$worksheet->getHighestColumn();
					//print_r($highestRow); exit();
			
				// $excel_fields = ["locname","loccode","address","city","state","zip","country","status","Type","import_date","deleted","appId","cdate"];
			$excel_fields = ["shipperpo","shippmentdate","pronum","reference","item","flocation","flcoationcode","tlcoation","tlocationcode","importtolocation","importtolocationcode","importtoaddress","importtocity","importtostate","importtozip","importtocountry","quantity","reportdate","user","rcvdate","processdate","chepprocessdate","chepumi","uploadedetochep","reasonforhold","locid","locid_wrecid","notes_general","dupid","program","type","jnj_id","transactionid","chepreference","ongreference","deleted","appId","cdate"];
				$ndata = array();

					for($i=2; $i<=$highestRow; $i++){
						
						 	foreach ($excel_fields as $key =>$value){

								$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();
								if($key == '1' || $key == '21'){
								$excel_date = $new_val; //here is that value 41621 or 41631
								$unix_date = ($excel_date - 25569) * 86400;
								$excel_date = 25569 + ($unix_date / 86400);
								$unix_date = ($excel_date - 25569) * 86400;
								$new_val = gmdate("Y-m-d", $unix_date);	
								}

								if($new_val ==""){ $final = " "; }else{ $final = strval($new_val); }
								$data[$value] = $final;

								if($value == "deleted"){ $data["deleted"] = 0; }
								if($value == "appId"){ $data["appId"] = $appId; }
								if($value == "cdate"){ $data["cdate"] = date("m-d-Y H:i:s"); }

							}

								$field =  ["shipperpo","shippmentdate","pronum","reference","item","flocation","flcoationcode","tlcoation","tlocationcode","importtolocation","importtolocationcode","importtoaddress","importtocity","importtostate","importtozip","importtocountry","quantity","reportdate","user","rcvdate","processdate","chepprocessdate","chepumi","uploadedetochep","reasonforhold","locid","locid_wrecid","notes_general","dupid","program","type","jnj_id","transactionid","chepreference","ongreference","deleted","appId","cdate"];

						$ff = array_combine($field,$data);	
						
						$ndata[] = $ff;	
						
						/*if (($i+1) >= $batchSize){
						
//							$this->admin->mongoInsert("$this->database.tbl_touts",$ndata,"bulk");
							$ndata = [];
							
						}*/
						
						if($hrow > $batchSize){

							if (($i+1) >= $batchSize){

								$this->admin->mongoInsert("$this->database.tbl_touts",$ndata,"bulk");
								$ndata = [];
								$hrow = $hrow - $batchSize;

							}

						}else{

							$this->admin->mongoInsert("$this->database.tbl_touts",$ndata,"bulk");
							$ndata = [];

						}
						
						
						
					}
			
//			echo '<pre>';
//			print_r($ndata);
			
			}
			echo 'success';
	}

	public function exceluploadIssues(){
		
		if(isset($_FILES["ldata"]["name"])){
			
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
			
			 $appId = $this->input->post("appID");
			
			$data = [];
			$error = [];
			$batchSize = 1000;
			/* foreach($spreadsheet as $worksheet)
			   {*/
			   	
				$highestRow = $worksheet->getHighestRow();
				$hrow = $worksheet->getHighestRow();
				$highestColumn = 5;//$worksheet->getHighestColumn();
					//print_r($highestRow); exit();
			
				// $excel_fields = ["locname","loccode","address","city","state","zip","country","status","Type","import_date","deleted","appId","cdate"];
$excel_fields = ["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","deleted","appId","cdate"];
				$ndata = array();	
					for($i=2; $i<=$highestRow; $i++){
						
						 	foreach ($excel_fields as $key =>$value){

$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();

if($key == '2' || $key == '7'){
$excel_date = $new_val; //here is that value 41621 or 41631
$unix_date = ($excel_date - 25569) * 86400;
$excel_date = 25569 + ($unix_date / 86400);
$unix_date = ($excel_date - 25569) * 86400;
$new_val = gmdate("Y-m-d", $unix_date);	
}
								
if($new_val ==""){ $final = " "; }else{ $final = strval($new_val); }
$data[$value] = $final;
							   
if($value == "deleted"){ $data["deleted"] = 0; }
if($value == "appId"){ $data["appId"] = $appId; }
if($value == "cdate"){ $data["cdate"] = date("m-d-Y H:i:s"); }
								
						 	}

$field =  ["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","deleted","appId","cdate"];

						$ff = array_combine($field,$data);	
						$ndata[] = $ff;
						
						/*if (($i+1) >= $batchSize){
						
							$this->admin->mongoInsert("$this->database.tbl_issues",$ndata,"bulk");
							$ndata = [];
							
						}*/
						
						if($hrow > $batchSize){

							if (($i+1) >= $batchSize){

								$this->admin->mongoInsert("$this->database.tbl_issues",$ndata,"bulk");
								$ndata = [];
								$hrow = $hrow - $batchSize;

							}

						}else{

							$this->admin->mongoInsert("$this->database.tbl_issues",$ndata,"bulk");
							$ndata = [];

						}
						
					}

//					$this->admin->mongoInsert("$this->database.tbl_issues",$ndata,"bulk");
										
			}
		
		
			echo 'success';
	}

	public function exceluploadReturns(){
		
		if(isset($_FILES["ldata"]["name"])){
			
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
			
			 $appId = $this->input->post("appID");
			
			$data = [];
			$error = [];
			$batchSize = 1000;
			/* foreach($spreadsheet as $worksheet)
			   {*/
			   	
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = 5;//$worksheet->getHighestColumn();
					//print_r($highestRow); exit();
			
				// $excel_fields = ["locname","loccode","address","city","state","zip","country","status","Type","import_date","deleted","appId","cdate"];
$excel_fields = ["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","deleted","appId","cdate"];
				$ndata = array();
			
				$hrow = $worksheet->getHighestRow();
			
					for($i=2; $i<=$highestRow; $i++){
						
						 	foreach ($excel_fields as $key =>$value){

								$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();
								if($key == '2' || $key == '7'){
								$excel_date = $new_val; //here is that value 41621 or 41631
								$unix_date = ($excel_date - 25569) * 86400;
								$excel_date = 25569 + ($unix_date / 86400);
								$unix_date = ($excel_date - 25569) * 86400;
								$new_val = gmdate("Y-m-d", $unix_date);	
								}

								if($new_val ==""){ $final = " "; }else{ $final = strval($new_val); }
								$data[$value] = $final;

								if($value == "deleted"){ $data["deleted"] = 0; }
								if($value == "appId"){ $data["appId"] = $appId; }
								if($value == "cdate"){ $data["cdate"] = date("m-d-Y H:i:s"); }

							}

								$field =  ["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","deleted","appId","cdate"];

								$ff = array_combine($field,$data);	

								$ndata[] = $ff;	
								
								if($hrow > $batchSize){

									if (($i+1) >= $batchSize){

										$this->admin->mongoInsert("$this->database.tbl_returns",$ndata,"bulk");
										$ndata = [];
										$hrow = $hrow - $batchSize;

									}
									
								}else{
									
									$this->admin->mongoInsert("$this->database.tbl_returns",$ndata,"bulk");
									$ndata = [];
									
								}
//						echo $hrow;
						
					}

//			echo '<pre>';
//			print_r($ff);
			

//					$this->mongo_db->batch_insert("tbl_returns",$ndata);
										
			}
		
		
			echo 'success';
	}

	public function exceluploadAdjustments(){
		
		if(isset($_FILES["ldata"]["name"])){
			
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
			
			 $appId = $this->input->post("appID");
			
			$data = [];
			$error = [];
			$batchSize = 1000;
			/* foreach($spreadsheet as $worksheet)
			   {*/
			   	
				$highestRow = $worksheet->getHighestRow();
				$hrow = $worksheet->getHighestRow();
				$highestColumn = 5;//$worksheet->getHighestColumn();
					//print_r($highestRow); exit();
			
				// $excel_fields = ["locname","loccode","address","city","state","zip","country","status","Type","import_date","deleted","appId","cdate"];
$excel_fields = ["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","adjdirection","umi","deleted","appId","cdate"];
				$ndata = array();	
					for($i=2; $i<=$highestRow; $i++){
						
						 	foreach ($excel_fields as $key =>$value){

$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();

if($key == '2' || $key == '7'){
$excel_date = $new_val; //here is that value 41621 or 41631
$unix_date = ($excel_date - 25569) * 86400;
$excel_date = 25569 + ($unix_date / 86400);
$unix_date = ($excel_date - 25569) * 86400;
$new_val = gmdate("Y-m-d", $unix_date);	
}

if($new_val ==""){ $final = " "; }else{ $final = strval($new_val); }
$data[$value] = $final;
							   
if($value == "deleted"){ $data["deleted"] = 0; }
if($value == "appId"){ $data["appId"] = $appId; }
if($value == "cdate"){ $data["cdate"] = date("m-d-Y H:i:s"); }
								
						 	}

$field =  ["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","adjdirection","umi","deleted","appId","cdate"];

						$ff = array_combine($field,$data);	
						
						$ndata[] = $ff;	
						
						/*if (($i+1) >= $batchSize){
						
							$this->admin->mongoInsert("$this->database.tbl_adjustments",$ndata,"bulk");
							$ndata = [];
							
						}*/
						
						if($hrow > $batchSize){

							if (($i+1) >= $batchSize){

								$this->admin->mongoInsert("$this->database.tbl_adjustments",$ndata,"bulk");
								$ndata = [];
								$hrow = $hrow - $batchSize;

							}

						}else{

							$this->admin->mongoInsert("$this->database.tbl_adjustments",$ndata,"bulk");
							$ndata = [];

						}
					}

//					$this->admin->mongoInsert("$this->database.tbl_adjustments",$ndata,"bulk");					
			}
		
		
			echo 'success';
	}

	public function exceluploadInventory(){
		
		if(isset($_FILES["ldata"]["name"])){
			
			$config['upload_path'] = 'uploads/exceldata/';
			$config['allowed_types'] = 'xlsx|xls|csv';
			$this->load->library('upload', $config);
			$this->upload->do_upload('ldata');
			
			
			$upload_data = $this->upload->data();
			$file_name = $upload_data['file_name'];

			$path = FCPATH.'uploads/exceldata/'.$file_name;
//			$path = FCPATH.'uploads/exceldata/sample.xlsx';
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			$spreadsheet = $reader->load($path)->getSheet(0);
			$worksheet = $spreadsheet;
			
			$appId = $this->input->post("appID");
			
			$data = [];
			$error = [];
			$batchSize = 1000;
			/* foreach($spreadsheet as $worksheet)
			   {*/
			   	
				$highestRow = $worksheet->getHighestRow();
				$hrow = $worksheet->getHighestRow();
				$highestColumn = 5;//$worksheet->getHighestColumn();
					//print_r($highestRow); exit();
			
				// $excel_fields = ["locname","loccode","address","city","state","zip","country","status","Type","import_date","deleted","appId","cdate"];
				$excel_fields = ["location","locname","loccode","loctype","item","current_contacts","notes","audit","last_report_date","starting_balance","issues","returns","transfer_ins","transfer_outs","adjustments","ending_balance","hold_transfer_ins","hold_transfer_outs","total_transfer_ins","total_transfer_outs","2017_audit_count","2017_audit_date","2018_audit_count","2018_audit_date","variance","location_test","short_text","2019_audit_count","deleted","appId","cdate"];
				$ndata = array();	
					for($i=2; $i<=$highestRow; $i++){
						
						 	foreach ($excel_fields as $key =>$value){

								$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();

								if($key == '8' || $key == '21' || $key == '23'){
									
									if($new_val != ''){
										$excel_date = $new_val; //here is that value 41621 or 41631
										$unix_date = ($excel_date - 25569) * 86400;
										$excel_date = 25569 + ($unix_date / 86400);
										$unix_date = ($excel_date - 25569) * 86400;
										$new_val = gmdate("Y-m-d", $unix_date);		
									}

								}

								if($new_val ==""){ $final = " "; }else{ $final = strval($new_val); }
								$data[$value] = $final;

								if($value == "deleted"){ $data["deleted"] = 0; }
								if($value == "appId"){ $data["appId"] = $appId; }
								if($value == "cdate"){ $data["cdate"] = date("m-d-Y H:i:s"); }
								
						 	}

						$field =  ["location","locname","loccode","loctype","item","current_contacts","notes","audit","last_report_date","starting_balance","issues","returns","transfer_ins","transfer_outs","adjustments","ending_balance","hold_transfer_ins","hold_transfer_outs","total_transfer_ins","total_transfer_outs","2017_audit_count","2017_audit_date","2018_audit_count","2018_audit_date","variance","location_test","short_text","2019_audit_count","deleted","appId","cdate"];

						$ff = array_combine($field,$data);	
						
						$ndata[] = $ff;	
						
						/*if (($i+1) >= $batchSize){
						
							$this->admin->mongoInsert("$this->database.tbl_inventory",$ndata,"bulk");
							$ndata = [];
							
						}*/
						
						if($hrow > $batchSize){

							if (($i+1) >= $batchSize){

								$this->admin->mongoInsert("$this->database.tbl_inventory",$ndata,"bulk");
								$ndata = [];
								$hrow = $hrow - $batchSize;

							}

						}else{

							$this->admin->mongoInsert("$this->database.tbl_inventory",$ndata,"bulk");
							$ndata = [];

						}
					}

//					$this->admin->mongoInsert("$this->database.tbl_inventory",$ndata,"bulk");	
										
			}
			echo 'success';
	}

	public function addTransfers(){
		
		$main = $this->input->post();
		
		$data = [];
		
		foreach($main as $k => $m){
				$data[$k] = $m;
		}

		$mng = $this->admin->Mconfig();
		$data['flcoationcode'] = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$data["flocation"]],[],"loccode");
		$data['tlocationcode'] = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$data["tlcoation"]],[],"loccode");
		
		$data["quantity"] = intval($this->input->post("quantity"));
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_touts",$data,$_SESSION['appid'],"");
		
		if(count($conRulescheck) > 0){
			
			foreach($conRulescheck as $con){
			
				$data[$con['column']] = $con['value'];
				
			}
				
		}
		/*$valRulescheck = $this->common->checkValidationrules("tbl_touts",$main,$_SESSION['appid'],"");
		
		if($valRulescheck){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>$valRulescheck));
			exit();
			
		}*/
		
		$this->mongo_db->switch_db($this->database);
		
		$flocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$data["flocation"]])[0];
		$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$data["tlcoation"]])[0];
		$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$data["item"]])[0];
		
		$data["flocation"] = ["id"=>$flocdata["_id"]->{'$id'},"locname"=>$flocdata["locname"],"loccode"=>$flocdata["loccode"],"status"=>$flocdata["status"]];
		
		$data["tlcoation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
		
		$data["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];
		
		$count = $this->admin->getCount("","$this->database.tbl_touts",["shipperpo"=>$data['shipperpo']],[]);
		$warMsg='';
		if($count > 0){
			$warMsg="Shipper PO already existed";
			// echo json_encode(array("Status"=>"Wrong","Message"=>"Shipper PO already existed"));
			// exit();
		}
		
		if($data['chepumi'] != ''){
			$ucount = $this->admin->getCount("","$this->database.tbl_touts",["chepumi"=>$data['chepumi']],[]);
		if($ucount > 0){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>"UMI already existed"));
			exit();
		}
		}
		
		
		foreach($data as $pk => $pd){
			if($pk != "quantity" && $pk != "flocation" && $pk != "tlcoation" && $pk != "item"){
				$data[$pk] = trim($pd);	
			}	
		}
		
		$data["id"] = $this->admin->insert_id("tbl_touts",$this->database);
		
		
// update location inventory		
		
		$tins = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$tlocdata["loccode"],"tlocationcode",$itemdata["item_name"],$data["quantity"],"transfer_ins",false);
		
		$touts = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$flocdata["loccode"],"flcoationcode",$itemdata["item_name"],$data["quantity"],"transfer_outs",false);
		
		
//		echo $tins." ".$touts;
//		exit();
// end update location inventory
		
		
		
		$d = $this->admin->mongoInsert("$this->database.tbl_touts",$data);
		echo json_encode(array("Status"=>"Success","Message"=>"Trasfer successfully added.","Warning"=>$warMsg));
	}

	public function addShipment(){
		
		$chRef = $this->input->post("chepreference");
		$ongRef = $this->input->post("ongreference");
		
		/*$valRulescheck = $this->common->checkValidationrules("tbl_issues",$this->input->post(),$_SESSION['appid'],"");
		
		if($valRulescheck){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>$valRulescheck));
			exit();
			
		}*/
		
		$main = $this->input->post();
		
		$main['quantity'] = intval($this->input->post('quantity'));
		$mng = $this->admin->Mconfig();
		
		$main['tlcoationcode'] = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$main["tlocation"]],[],"loccode");
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_issues",$main,$_SESSION['appid'],"");
		
		if(count($conRulescheck) > 0){
			
			foreach($conRulescheck as $con){
			
				$main[$con['column']] = $con['value'];
				
			}
				
		}
		
		$chkOngref = $this->admin->getCount("","$this->database.tbl_issues",["ongreference"=>$main['ongreference']],[]);
		$chkChepref = $this->admin->getCount("","$this->database.tbl_issues",["chepreference"=>$main['chepreference']],[]);
		
		
		if($chkChepref > 0 && ($chRef != "")){
			
			echo json_encode(array("Status"=>"Error","Message"=>"Vendor Reference Already Added."));
			exit();
			
		}
		
		if($chkOngref > 0 && ($ongRef != "")){
			
			echo json_encode(array("Status"=>"Error","Message"=>"Ongweoweh Reference Already Added."));
			exit();
			
		}
		
		if($main['umi'] != ''){
				$ucount = $this->admin->getCount("","$this->database.tbl_issues",["umi"=>$main['umi']],[]);
				if($ucount > 0){
					echo json_encode(array("Status"=>"Wrong","Message"=>"UMI already existed"));
					exit();
				}
		}
		

		foreach($main as $pk => $pd){
			if($pk != "quantity"){
				$main[$pk] = trim($pd);	
			}	
		}
		
		$this->mongo_db->switch_db($this->database);
		
		$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$main["tlocation"]])[0];
		$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$main["item"]])[0];
		
		
		$main["tlocation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
		$main["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];
		
		
		$main["id"] = $this->admin->insert_id("tbl_issues",$this->database);
		
		$d = $this->admin->mongoInsert("$this->database.tbl_issues",$main,"");

        // update location inventory		
		
		$tins = $this->common->updateLocationinventorycount($this->database,"tbl_issues",$_SESSION['appid'],$tlocdata["loccode"],"tlcoationcode",$itemdata["item_name"],$main['quantity'],"issues",false);		

        // end update location inventory

		echo json_encode(array("Status"=>"Success","Message"=>"Shipment successfully added."));
		
	}

	public function addPickup(){
		
		$chRef = $this->input->post("chepreference");
		$ongRef = $this->input->post("ongreference");
		
		
		$main = $this->input->post();
		
		/*$valRulescheck = $this->common->checkValidationrules("tbl_returns",$this->input->post(),$_SESSION['appid'],"");
		
		if($valRulescheck){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>$valRulescheck));
			exit();
			
		}*/
		
		$main['quantity'] = intval($this->input->post('quantity'));

		$mng = $this->admin->Mconfig();
		$main['tlcoationcode'] = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$main["tlocation"]],[],"loccode");
				
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_returns",$main,$_SESSION['appid'],"");

		if(count($conRulescheck) > 0){
			
			foreach($conRulescheck as $con){
			
				$main[$con['column']] = $con['value'];
				
			}
				
		}

		foreach($main as $pk => $pd){
			if($pk != "quantity"){
				$main[$pk] = trim($pd);	
			}	
		}
		$main["id"] = $this->admin->insert_id("tbl_returns",$this->database);
		
		$this->mongo_db->switch_db($this->database);
		
		$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$main["tlocation"]])[0];
		$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$main["item"]])[0];
		
		
		
		$main["tlocation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
		$main["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];

		
		$chkOngref = $this->admin->getCount("","$this->database.tbl_returns",["ongreference"=>$main['ongreference']],[]);
		$chkChepref = $this->admin->getCount("","$this->database.tbl_returns",["chepreference"=>$main['chepreference']],[]);

		if($chkChepref > 0 && ($chRef != "")){
			
			echo json_encode(array("Status"=>"Error","Message"=>"Vendor Reference Already Added."));
			exit();
			
		}
		
		if($chkOngref > 0 && ($ongRef != "")){
			
			echo json_encode(array("Status"=>"Error","Message"=>"Ongweoweh Reference Already Added."));
			exit();
			
		}
		
		if($main['umi'] != ''){
			$ucount = $this->admin->getCount("","$this->database.tbl_returns",["umi"=>$main['umi']],[]);
		if($ucount > 0){
			echo json_encode(array("Status"=>"Wrong","Message"=>"UMI already existed"));
			exit();
		}
		}
		
		
		
		$d = $this->admin->mongoInsert("$this->database.tbl_returns",$main,""); 
		// update location inventory		
		
		$tins = $this->common->updateLocationinventorycount($this->database,"tbl_returns",$_SESSION['appid'],$tlocdata["loccode"],"tlcoationcode",$itemdata["item_name"],$main['quantity'],"returns",false);		

        // end update location inventory
		echo json_encode(array("Status"=>"Success","Message"=>"Pickup successfully added."));
		
	}

	public function addAdjustment(){
		
		$chRef = $this->input->post("chepreference");
		$ongRef = $this->input->post("ongreference");
		
		
		/*$valRulescheck = $this->common->checkValidationrules("tbl_adjustments",$this->input->post(),$_SESSION['appid'],"");
		
		if($valRulescheck){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>$valRulescheck));
			exit();
			
		}*/
				
		$main = $this->input->post();
		
		$main["quantity"] = intval($this->input->post("quantity"));

		$mng = $this->admin->Mconfig();
		
		$main['tlcoationcode'] = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$main["tlocation"]],[],"loccode");
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_adjustments",$this->input->post(),$_SESSION['appid'],"");
		
		if(count($conRulescheck) > 0){
			
			foreach($conRulescheck as $con){
			
				$main[$con['column']] = $con['value'];
				
			}
				
		}

		foreach($main as $pk => $pd){
			if($pk != "quantity"){
				$main[$pk] = trim($pd);	
			}	
		}
		
		$main["id"] = $this->admin->insert_id("tbl_adjustments",$this->database);		
		
		$chkOngref = $this->admin->getCount("","$this->database.tbl_adjustments",["ongreference"=>$main['ongreference']],[]);
		$chkChepref = $this->admin->getCount("","$this->database.tbl_adjustments",["chepreference"=>$main['chepreference']],[]);

		if($chkChepref > 0 && ($chRef != "")){
			
			echo json_encode(array("Status"=>"Error","Message"=>"Vendor Reference Already Added."));
			exit();
			
		}
		
		if($chkOngref > 0 && ($ongRef != "")){
			
			echo json_encode(array("Status"=>"Error","Message"=>"Ongweoweh Reference Already Added."));
			exit();
			
		}
		
		if($main['umi'] != ''){
			$ucount = $this->admin->getCount("","$this->database.tbl_adjustments",["umi"=>$main['umi']],[]);
			if($ucount > 0){
				echo json_encode(array("Status"=>"Wrong","Message"=>"UMI already existed"));
				exit();
			}
		}
		
		
		$this->mongo_db->switch_db($this->database);
		
		$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$main["tlocation"]])[0];
		$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$main["item"]])[0];
		
		
		$main["tlocation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
		$main["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];

		
		$d = $this->admin->mongoInsert("$this->database.tbl_adjustments",$main,""); 
		// update location inventory		
		
		$tins = $this->common->updateLocationinventorycount($this->database,"tbl_adjustments",$_SESSION['appid'],$tlocdata["loccode"],"tlcoationcode",$itemdata["item_name"],$main['quantity'],"adjustments",false);		

        // end update location inventory
		echo json_encode(array("Status"=>"Success","Message"=>"Adjustment successfully added."));
		
	}
	
	
// delete bulk data	
	
	public function deleteBulkdata(){
		
		$this->mongo_db->switch_db($this->database);
		
		$locations = $this->input->post("locations");
		$table = $this->input->post("table");
		
		foreach($locations as $loc){
			
			$exdata = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($loc)])[0];
			
			if($table == "tbl_locations"){
						
				$udata = ["id"=>$exdata["_id"]->{'$id'},"previous_name"=>$exdata['locname'],"new_name"=>$exdata['locname'],"code"=>$exdata['loccode'],"status"=>"Inactive","notes"=>$exdata['notes'],"loctype"=>$exdata['Type'],"appId"=>$_SESSION['appid']];
				$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);

			}

			if($table == "tbl_items"){

				$udata = ["id"=>$exdata["_id"]->{'$id'},"previous_name"=>$exdata["item_name"],"new_name"=>$exdata['item_name'],"code"=>$exdata['item_code'],"status"=>"Inactive","appId"=>$_SESSION['appid']];
				$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);

			}
			
			
			$column = [];
		
			if($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){

				$column[] = "tlcoationcode";

			}elseif($table == "tbl_touts"){

				$column[] = "flcoationcode";
				$column[] = "tlocationcode";

			}

			foreach($column as $col){

				$oldinvdata = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$exdata[$col],"item.item_name"=>$exdata["item"]->item_name])[0];

				$dval['starting_balance'] = intval($oldinvdata["starting_balance"]);
				$dval['issues'] = intval($oldinvdata["issues"]);
				$dval['returns'] = intval($oldinvdata["returns"]);
				$dval['transfer_ins'] = intval($oldinvdata["transfer_ins"]);
				$dval['transfer_outs'] = intval($oldinvdata["transfer_outs"]);
				$dval['adjustments'] = intval($oldinvdata["adjustments"]);

				if($table == "tbl_touts"){

					if($col == "tlocationcode"){

						$dval['transfer_ins'] = intval($oldinvdata["transfer_ins"]) - intval($exdata["quantity"]);

					}else{

						$dval['transfer_outs'] = intval($oldinvdata["transfer_outs"]) - intval($exdata["quantity"]);

					}

				}elseif($table == "tbl_issues"){

					$dval['issues'] = intval($oldinvdata["issues"]) - intval($exdata["quantity"]);

				}elseif($table == "tbl_returns"){

					$dval['returns'] = intval($oldinvdata["returns"]) - intval($exdata["quantity"]);

				}elseif($table == "tbl_adjustments"){

					$dval['adjustments'] = intval($oldinvdata["adjustments"]) - intval($exdata["quantity"]);

				}

				$ending_balance = ($dval['starting_balance'] + $dval['issues'] + $dval['returns'] + $dval['transfer_ins'] - $dval['transfer_outs'] + $dval['adjustments']); 

				$dval['ending_balance'] = intval($ending_balance);

				$this->mongo_db->where(["loccode"=>$exdata[$col],"item.item_name"=>$exdata["item"]->item_name])->set($dval)->update("tbl_inventory");

			}
			
			
				if($table == "tbl_inventory"){
				
					$issues = $this->mongo_db->aggregate("tbl_issues",[
						['$match' => ["item.item_name"=>$exdata["item"]->item_name,"tlcoationcode"=>$exdata["loccode"],"flag"=>"uexcel"]],
					]);

			// pickups		

					$pickups = $this->mongo_db->aggregate("tbl_returns",[
						['$match' => ["item.item_name"=>$exdata["item"]->item_name,"tlcoationcode"=>$exdata["loccode"],"flag"=>"uexcel"]],
					]);

			// adjustments		

					$adjustments = $this->mongo_db->aggregate("tbl_adjustments",[
						['$match' => ["item.item_name"=>$exdata["item"]->item_name,"tlcoationcode"=>$exdata["loccode"],"flag"=>"uexcel"]],
					]);

			// Transfers Ins		

					$transferins = $this->mongo_db->aggregate("tbl_touts",[
						['$match' => ["item.item_name"=>$exdata["item"]->item_name,"tlocationcode"=>$exdata["loccode"],"flag"=>"uexcel"]],
					]);

			// Transfers Outs		

					$transferouts = $this->mongo_db->aggregate("tbl_touts",[
						['$match' => ["item.item_name"=>$exdata["item"]->item_name,"flcoationcode"=>$exdata["loccode"],"flag"=>"uexcel"]],
					]);		

			// end query


			// update issues count		
			//
					if($issues){

						foreach($issues as $iss){

							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($iss["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_issues");		

						}

					}

			// update pickups count		

					if($pickups){

						foreach($pickups as $pkk){

							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($pkk["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_returns");		

						}

					}

			// update adjustments count		

					if($adjustments){

						foreach($adjustments as $adj){

							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($adj["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_adjustments");		

						}

					}		

			// update transfer ins count		

					if($transferins){

						foreach($transferins as $tin){

							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($tin["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_touts");		

						}

					}

					if($transferouts){

						foreach($transferouts as $tout){

							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($tout["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_touts");		

						}

					}
	
				
			}
			
			$d = $this->mongo_db->where(array("_id"=>new MongoDB\BSON\ObjectID($loc)))->delete($table);
		
		}
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'fail';
			
		}
	}
	
	public function updateLocbulkrecords(){
		
		$columns = $this->input->post("columns");
		$values = $this->input->post("value");
		$time = $this->input->post("value1");
		$targets = $this->input->post("targets");
		$table = $this->input->post("table");
		
		$fields = [];
		
		foreach($columns as $ccol){
			
			$fields[] = explode("-",$ccol)[0];
			
		}
		
		if(count($columns) > 0){
			
			$impkey = 0;
			$data = [];

	// data binding to columns
			
			foreach($columns as $key => $col){
				
				$colname = explode("-",$col)[0];
				$datatype = explode("-",$col)[1];
				
				if($colname == "quantity"){
					
					if($values[$key] == 0){
						
						echo 'Please enter postive or negative integer values in Quantity';
						exit();
						
					}	
					
				}
				
				if($colname == "import_date" || $colname == "reportdate"){
					
					$data[$colname] = date("Y-m-d",strtotime($values[$key]));
					
					if($time[$impkey] != "" && $colname == "import_date"){
						$data['import_time'] = $time[$impkey];
					}else{
						$data['import_time'] = "";
					}

					if($time[$impkey] != "" && $colname == "reportdate"){
						$data['reportdate_time'] = $time[$impkey];
					}else{
						$data['reportdate_time'] = "";
					}
					
				}elseif($colname == "starting_balance" || $colname == "issues" || $colname == "returns" || $colname == "transfer_ins" || $colname == "transfer_outs" || $colname == "adjustments" || $colname == "ending_balance"){					
					$data[$colname] = intval($values[$key]);
				}elseif($datatype == "date"){
					
					$data[$colname] = date("Y-m-d",strtotime($values[$key]));
					
				}elseif(($colname == "quantity" && $table == "tbl_touts") || ($colname == "quantity" && $table == "tbl_issues") || ($colname == "quantity" && $table == "tbl_returns") || ($colname == "quantity" && $table == "tbl_adjustments")){
					
				    $v1 = intval($values[$key]);
					$data[$colname] = $v1;	
					
				}else{
					
					$data[$colname] = $values[$key];
					
				}
				
			}
			
			$this->mongo_db->switch_db($this->database);
						
			
			$postdata = [];
			
			foreach($targets as $tk => $loc){
				
				$postdata[$tk] = $this->mongo_db->get_where($table,array('_id'=>new MongoDB\BSON\ObjectID($loc)))[0];
				
				
				$fromlocation = isset($postdata[$tk]["flocation"]) ? $postdata[$tk]["flocation"] : "";
				$tolocation = isset($postdata[$tk]["tlcoation"]) ? $postdata[$tk]["tlcoation"] : "";
				$spatolocation = isset($postdata[$tk]["tlocation"]) ? $postdata[$tk]["tlocation"] : "";
				$item = isset($postdata[$tk]["item"]) ? $postdata[$tk]["item"] : "";
				$invlocname = ($postdata[$tk]['locname'] && $table == "tbl_inventory") ? $postdata[$tk]['locname'] : "";
				
				
				if($fromlocation){

					$postdata[$tk]["flocation"] = $postdata[$tk]["flocation"]->locname;

				}
				if($tolocation){

					$postdata[$tk]["tlcoation"] = $postdata[$tk]["tlcoation"]->locname;

				}
				if($spatolocation){

					$postdata[$tk]["tlocation"] = $postdata[$tk]["tlocation"]->locname;

				}
				if($item){

					$postdata[$tk]["item"] = $postdata[$tk]["item"]->item_name;

				}
				
				if($invlocname){

					$postdata[$tk]["locname"] = $postdata[$tk]["locname"]->locname;

				}
				
				
//				$postdata[$loc] = $tdata;
				
				$fielddata = array_combine($fields,$values);
				
//				print_r($tdata);
				
//				$postdata[$loc] = array_replace($tdata,$fielddata);
				
				foreach($fielddata as $key => $value){
					
					if($colname == "starting_balance" || $colname == "issues" || $colname == "returns" || $colname == "transfer_ins" || $colname == "transfer_outs" || $colname == "adjustments" || $colname == "ending_balance" || $colname == "quantity"){
						
						$postdata[$tk][$key] = ($value != "") ? intval($value) : intval();
						
					}else{
					
						$postdata[$tk][$key] = $value;
						
					}
				}
//				print_r($postdata[$tk]);
				
				
//				$conRulescheck = $this->conditions_model->checkConditionrules($table,$conditionsdata,$_SESSION['appid'],"");

				/*if(count($conRulescheck) > 0){

					foreach($conRulescheck as $ck => $con){

						$postdata[$tk][$con['column']] = $con['value'];

					}

				}*/
			
			}
			
//			print_r($postdata);
//			exit();
			
	// updating column values
			
			
			foreach($postdata as $key => $val){
				
//				echo $key;
				
				$tlocdata = [];
				
				unset($val["_id"]);
				unset($val["id"]);
				
				$crulechk = $this->admin->getCount("","$this->mdb.tbl_conditional_rules",["appId"=>$_SESSION['appid'],"table"=>$table],[]);
				
				if($crulechk > 0){
				
					$conRulescheck = $this->conditions_model->checkConditionrules($table,$val,$_SESSION['appid'],"");

					if(count($conRulescheck) > 0){

						foreach($conRulescheck as $ck => $con){

							$val[$con['column']] = $con['value'];

						}

					}
					
				}
				
				$fromlocation = isset($val["flocation"]) ? $val["flocation"] : "";
				$tolocation = isset($val["tlcoation"]) ? $val["tlcoation"] : "";
				$spatolocation = isset($val["tlocation"]) ? $val["tlocation"] : "";
				$item = isset($val["item"]) ? $val["item"] : "";
				$invlocname = ($val['locname'] && $table == "tbl_inventory") ? $val['locname'] : "";
				
				if($fromlocation){
					
					$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$val["flocation"]])[0];
					$val["flocation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
					$val["flcoationcode"] = $tlocdata["loccode"];
					
				}
				if($tolocation){
					
					$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$val["tlcoation"]])[0];
					$val["tlcoation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
					$val["tlocationcode"] = $tlocdata["loccode"];
					
				}
				if($spatolocation){
					
					$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$val["tlocation"]])[0];
					
//					print_r($tlocdata);
					$val["tlocation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
					$val["tlcoationcode"] = $tlocdata["loccode"];

					
				}
				if($item){

					$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$val["item"]])[0];
					$val["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];
					
				}
				if($invlocname){

					$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$val["locname"]])[0];					
					$val["locname"] = ["id"=>$tlocdata['_id']->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
					$val["location"] = $tlocdata['locname']." - ".$tlocdata['loccode'];
					$val["loccode"] = $tlocdata["loccode"];
					$val["loctype"] = $tlocdata["Type"];
//					$val["notes"] = $tlocdata["notes"];
	
				}
				
//				print_r($val);	
				
//				echo $postdata[$key]["_id"]->{'$id'};
//				print_r($val);
				
//				foreach($targets as $loc){
					
					$sbalance = 0;
					
					$exdata = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'})])[0];

//					print_r($val);
					if($table == "tbl_touts"){
						
						$this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'}))->set($val)->update($table);
						
						$tins = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$val["tlocationcode"],"tlocationcode",$val["item"]["item_name"],$val["quantity"],"transfer_ins",$exdata);

						$touts = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$val["flcoationcode"],"flcoationcode",$val["item"]["item_name"],$val["quantity"],"transfer_outs",$exdata);
						
//						echo $tins." ".$touts;
//						exit();

					}elseif($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){

						if($table == "tbl_adjustments"){

							$lcol = "adjustments";

						}elseif($table == "tbl_issues"){

							$lcol = "issues";

						}elseif($table == "tbl_returns"){

							$lcol = "returns";

						}
						
						$this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'}))->set($val)->update($table);

						$ff = $this->common->updateLocationinventorycount($this->database,$table,$_SESSION['appid'],$val["tlcoationcode"],"tlcoationcode",$val["item"]["item_name"],$val["quantity"],$lcol,$exdata);

					}elseif($table == "tbl_inventory"){
						
						$loccode = $val["locname"]["loccode"];
						$item = $val["item"]["item_name"];

						if($exdata["locname"]->locname != $val["locname"]["locname"]){

							$ltdata = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$loccode,"item.item_name"=>$item])[0];
							
							$sbalance = ($ltdata["starting_balance"] + $val['starting_balance']);
							$issues = ($this->common->getInventorycount($this->database,"tbl_issues",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$returns = ($this->common->getInventorycount($this->database,"tbl_returns",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$transfer_ins = ($this->common->getInventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"tlocationcode",$item));
							$transfer_outs = ($this->common->getInventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"flcoationcode",$item));
							$adjustments = ($this->common->getInventorycount($this->database,"tbl_adjustments",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$ending_balance = ($sbalance+$issues+$returns+$transfer_ins-$transfer_outs+$adjustments);

							$val['starting_balance'] = intval($sbalance);
							$val['issues'] = intval($issues);
							$val['returns'] = intval($returns);
							$val['transfer_ins'] = intval($transfer_ins);
							$val['transfer_outs'] = intval($transfer_outs);
							$val['adjustments'] = intval($adjustments);
							$val['ending_balance'] = intval($ending_balance);	
							
//							echo $sbalance;
							
							$this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($ltdata['_id']->{'$id'}))->set($val)->update($table);
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'})])->delete("tbl_inventory");
							
//							print_r($val);

						}else{
							
							$sbalance = $val['starting_balance'];
							$issues = ($this->common->getInventorycount($this->database,"tbl_issues",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$returns = ($this->common->getInventorycount($this->database,"tbl_returns",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$transfer_ins = ($this->common->getInventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"tlocationcode",$item));
							$transfer_outs = ($this->common->getInventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"flcoationcode",$item));
							$adjustments = ($this->common->getInventorycount($this->database,"tbl_adjustments",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$ending_balance = ($sbalance+$issues+$returns+$transfer_ins-$transfer_outs+$adjustments);

							$val['starting_balance'] = intval($sbalance);
							$val['issues'] = intval($issues);
							$val['returns'] = intval($returns);
							$val['transfer_ins'] = intval($transfer_ins);
							$val['transfer_outs'] = intval($transfer_outs);
							$val['adjustments'] = intval($adjustments);
							$val['ending_balance'] = intval($ending_balance);	
							
							$this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'}))->set($val)->update($table);
							
						}
					}else{
						
						$this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'}))->set($val)->update($table);
						
					}
					
					
					if($table == "tbl_locations"){
						
						if(($val['locname'] != $exdata['locname']) || ($val['loccode'] != $exdata["loccode"]) || ($val['status'] != $exdata["status"]) || ($val['notes'] != $exdata["notes"]) || ($val['Type'] != $exdata["Type"])){				
				
							$udata = ["id"=>$exdata["_id"]->{'$id'},"previous_name"=>$exdata['locname'],"new_name"=>$val['locname'],"code"=>$val['loccode'],"status"=>$val['status'],"notes"=>$val['notes'],"loctype"=>$val['Type'],"appId"=>$_SESSION['appid']];
							$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);

						}
						
					}
					
					if($table == "tbl_items"){
						
						if(($val['item_name'] != $exdata['item_name']) || ($val['item_code'] != $exdata['item_code']) || ($val['status'] != $exdata['status'])){
				
							$udata = ["id"=>$exdata["_id"]->{'$id'},"previous_name"=>$exdata["item_name"],"new_name"=>$val['item_name'],"code"=>$val['item_code'],"status"=>$val['status'],"appId"=>$_SESSION['appid']];
							$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);

						}
						
					}
					
//					print_r($val);
//					echo $touts."111 ".$tins;

					
			// end update location inventory					

				
				
//				}
			}
			
			echo 'success';
			
		}else{
			
			echo 'please select atleast one column';
			
		}
		
		
	}

	
}