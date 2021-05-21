<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH.'vendor/autoload.php';
class Items extends CI_Controller {

	public function __construct(){
		
		parent::__construct();
		
		if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
		$this->mdb = mongodb;
		
	}
	
	
	public function insertItem(){
		
		$icode = $this->input->post("item_code");
		$iname = $this->input->post("item_name");
		$status = $this->input->post("status");
		$appId = $this->input->post("appID");
		
		$pdata = $this->input->post();
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_items",$pdata,$appId,"");
		
		if($conRulescheck){
			
			foreach($conRulescheck as $con){
			
				$pdata[$con['column']] = $con['value'];
				
			}			
		}
	
		/*$valRulescheck = $this->common->checkValidationrules("tbl_items",$pdata,$appId,"");
		
		if($valRulescheck){
			
			echo $valRulescheck;
			exit();
			
		}*/
		
		$row = $this->admin->getCount("","$this->database.tbl_items",['item_code' => $pdata["item_code"]],[]);
		
		if($row > 0){
			
			echo "Item Code Exists";
			exit();
			
		}
		
		/*$data = array(
		
			"item_code" => $icode,
			"item_name" => $iname,
			"status" => $status,
			"appId" => $appId,
			"deleted" => 0,
			"created_date" => date("M-d-y H:i:s"),
		
		);*/
		
		$pdata["deleted"] = 0;
		$pdata["created_date"] = date("M-d-y H:i:s");
		
		foreach($pdata as $pk => $pd){
			
			$pdata[$pk] = trim($pd);	
				
		}
		
		$pdata["id"] = $this->admin->insert_id("tbl_items",$this->database);
		
		$d = $this->admin->mongoInsert("$this->database.tbl_items",$pdata);
		
//		echo $d->getInsertedCount();
		
		if($d){
			
			echo "success";
			
		}else{
			
			echo "error";
			
		}
		
	}
	
	public function updateItem(){
		
		$iid = $this->input->post("iid");	
		$icode = $this->input->post("item_code");
		$iname = $this->input->post("item_name");
		$status = $this->input->post("status");
		$appId = $this->input->post("appID");
		$id = new MongoDB\BSON\ObjectID($iid);
		
		$pdata = $this->input->post();
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_items",$pdata,$appId,"");
		
		if($conRulescheck){
			
			foreach($conRulescheck as $con){
			
				$pdata[$con['column']] = $con['value'];
				
			}
						
		}
	
		/*$valRulescheck = $this->common->checkValidationrules("tbl_items",$pdata,$appId,"");
		
		if($valRulescheck){
			
			echo $valRulescheck;
			exit();
			
		}*/
		
		$row = $this->admin->getRow("",['item_code' => $pdata["item_code"]],[],"$this->database.tbl_items");
			
		if($row > 0){
			
			if($row->_id != $iid){
				
				echo "Item Code Already Exists";
				exit();
				
			}
			
		}
		
		$idata = $this->admin->getRow("",['_id' => $id],[],"$this->database.tbl_items");
		
		
		/*$data = array(
		
			"item_code" => $icode,
			"item_name" => $iname,
			"status" => $status,
			"updated_date" => date("M-d-y H:i:s"),
		
		);*/
		
		$pdata["updated_date"] = date("M-d-y H:i:s");
		
		foreach($pdata as $pk => $pd){
			
			$pdata[$pk] = trim($pd);	
				
		}
		$d = $this->admin->mongoUpdate("$this->database.tbl_items",array('_id'=>$id),$pdata,[]);
		
//		$d->getModifiedCount()
		
		if($d){
			
			if(($pdata['item_name'] != $idata->item_name) || ($pdata['item_code'] != $idata->item_code) || ($pdata['status'] != $idata->status)){
				
				$udata = ["id"=>$this->input->post("iid"),"previous_name"=>$idata->item_name,"new_name"=>$pdata['item_name'],"code"=>$pdata['item_code'],"status"=>$pdata['status'],"appId"=>$appId];
				$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);
				
			}
			
			echo "success";
			
		}else{
			
			echo "error";
			
		}
		
	}
	
	public function delItem($id){
		
		$lid = new MongoDB\BSON\ObjectID($id);
		
		$ldata = $this->admin->getRow("",['_id' => $lid],[],"$this->database.tbl_items");

		$this->mongo_db->switch_db($this->database);

// transfers		
		
		$this->mongo_db->where(["item.item_name"=>$ldata->item_name])->delete_all("tbl_touts");
		$this->mongo_db->where(["item.item_name"=>$ldata->item_name])->delete_all("tbl_touts");
		
// shipments		
		
		$this->mongo_db->where(["item.item_name"=>$ldata->item_name])->delete_all("tbl_issues");
		$this->mongo_db->where(["item.item_name"=>$ldata->item_name])->delete_all("tbl_returns");
		$this->mongo_db->where(["item.item_name"=>$ldata->item_name])->delete_all("tbl_adjustments");
		$this->mongo_db->where(["item.item_name"=>$ldata->item_name])->delete_all("tbl_inventory");	
		
		
		$d =$this->admin->mongoDelete("$this->database.tbl_items",array('_id'=>$lid),[]);	
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'error';
			
		}
		
	}
	
	public function exceluploadItems(){
		
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
			/* foreach($spreadsheet as $worksheet)
			   {*/
			   	
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = 5;
			
				$excel_fields = ["item_code","item_name","status","appId","deleted","created_date"];
				$ndata = array();	
					for($i=2; $i<=$highestRow; $i++){
						
						 	foreach ($excel_fields as $key =>$value){

								$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();

								if($new_val ==""){ $final = " "; }else{ $final = strval($new_val); }
								$data[$value] = $final;

								if($value == "deleted"){ $data["deleted"] = 0; }
								if($value == "appId"){ $data["appId"] = $appId; }
								if($value == "status"){ $data["status"] = 'Active'; }
								if($value == "created_date"){ $data["created_date"] = date("m-d-Y H:i:s"); }
								
						 	}

						$field =  ["item_code","item_name","status","appId","deleted","created_date"];

						$ff = array_combine($field,$data);	
						
						$ndata[] = $ff;	
						
					}
			
					$this->admin->mongoInsert("$this->database.tbl_items",$ndata,"bulk");
										
			}
		
			echo 'success';
	}

	
}
