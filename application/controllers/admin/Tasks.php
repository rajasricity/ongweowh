<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Tasks extends CI_Controller {

	public function __construct(){
		
		parent::__construct();
		
		/*if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}*/
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
		$this->mdb = mongodb;
		
	}
	
	public function test(){
		
		/*$this->mongo_db->switch_db($this->database);
		$d = $this->mongo_db->insert("tbl_tasks_history",["test"=>"132"]);
		
		echo '<pre>';
		print_r($d);
		
		echo $d["_id"]->{'$id'};*/
		
		
		$columns = [ 
        "shipperpo", 
        "shippmentdate", 
        "pronum", 
        "reference", 
        "item", 
        "flocation", 
        "flcoationcode", 
        "tlcoation", 
        "tlocationcode", 
        "quantity", 
        "reportdate", 
        "user", 
        "processdate", 
        "chepprocessdate", 
        "chepumi", 
        "uploadedetochep", 
        "reasonforhold", 
        "transactionid"
    ];
    $labels = [ 
        "Shipper PO", 
        "Shipment Date", 
        "Pro Number", 
        "Reference", 
        "Item", 
        "From Location", 
        "From Location Code", 
        "To Location", 
        "To Location Code", 
        "Quantity", 
        "Report Date", 
        "User", 
        "Process Date", 
        "Customer Process Date", 
        "UMI", 
        "Uploaded To Customer", 
        "Reason For Hold", 
        "Transaction ID"
    ];
		
		
		foreach($labels as $k => $l){
			
			if(($columns[$k] != "transactionid") && ($columns[$k] != "tlocationcode")){
				
				echo $l."<br>";
				
			}
			
			
		}
		
		
	}

	public function runTask(){
		
		$this->load->view("admin/runTask");
		
	}
	
	public function updateInventoryrecords(){
		
		$this->load->view("admin/updateInventoryrecords");
		
	}
	
	public function getAlltasks($table){
		
		$i = 0;	
		$tasks = $this->admin->getArray("",["table"=>$table,"appId"=>$_SESSION['appid']],[],"$this->mdb.tbl_tasks");
		
		$out = [];
		$id = 0;
		foreach($tasks as $key=>$ta){
			
			$ndata = [];
			
			$ndata['sno'] = ++$i;
			$ndata["task_name"] = $ta->task_name;
			$ndata["schedule_type"] = $ta->schedule_type;
			$ndata["status"] = $ta->status;
			$ndata["next_run_date"] = $ta->next_run_date." ".$ta->next_run_time;
			$ndata["actions"] = '<a href="javascript:void(0)" class="editTask" data-toggle="modal" data-target="#editTask" tid="'.$ta->_id.'"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$ta->_id.'" onClick="deleteTask(this.id)"><i class="fa fa-trash" style="color: red"></i></a>';
			
			$out[] = $ndata;
			
		}
		$results = ["sEcho" => 1,"iTotalRecords" => count($out),"iTotalDisplayRecords" => count($out),"aaData" => $out ];
		echo json_encode($results);
		
	}		 
	
	public function getColumns(){
		
		$column = $this->input->post("column");
		$table = $this->input->post("table");
		$ref = $this->input->post("ref");
		$condref = $this->input->post("condref");
		$invref = $this->input->post("invref");
		$toutref = $this->input->post("toutref");
		
		if($ref){
			
			$rname = $ref;
			
		}else{
			
			$rname = "";
			
		}
		
		$lcolumns = $this->admin->getRow("",["table"=>$table],[],$this->admin->getAppdb().".settings");
	
		$columns = ''; 
		
		foreach($lcolumns->labels as $key => $labels){	
			
			if($table == "tbl_inventory"){	
				
				if(($lcolumns->columns[$key] != "location") && ($lcolumns->columns[$key] != "loccode") && ($lcolumns->columns[$key] != "loctype") && ($lcolumns->columns[$key] != "issues") && ($lcolumns->columns[$key] != "returns") && ($lcolumns->columns[$key] != "transfer_ins") && ($lcolumns->columns[$key] != "transfer_outs") && ($lcolumns->columns[$key] != "adjustments") && ($lcolumns->columns[$key] != $invref)){

					$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';

				}
				
			}elseif($table == "tbl_touts"){
				
//				if($lcolumns->columns[$key] != "tlocationcode" && $lcolumns->columns[$key] != "flcoationcode"){

					if($toutref == "dshipperpo"){
						
						if(($lcolumns->columns[$key] != "chepumi" && $lcolumns->columns[$key] != "tlocationcode" && $lcolumns->columns[$key] != "flcoationcode")){
							
							$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';
							
						}
						
					}else{
						$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';
					}
//				}
				
			}elseif(($table == "tbl_returns") || ($table == "tbl_issues") || ($table == "tbl_adjustments")){
				
				if($lcolumns->columns[$key] != "tlcoationcode"){

					if($toutref == "dshipperpo"){
						
						if(($lcolumns->columns[$key] != "umi")){
							
							$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';
							
						}
						
					}else{
						$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';
					}

				}
				
			}else{
				
				$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';
				
			}
		}
		
		$operators = $this->common->getConditionbydatatype("text",$table,$column);
		$oper = '';
		
		foreach($operators as $op){
			
			$oper .= '<option value="'.$op.'">'.$op.'</option>';
			
		}
		
		$locations = "";
		
		if($table == "tbl_inventory"){
			
			$this->mongo_db->switch_db($this->database);
			$locs = $this->mongo_db->get_where("tbl_locations",["status"=>"Active"]);
			
			$locations .= '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$rname.'[]" required>';

			foreach($locs as $loc){

				$locations .= '<option value="'.$loc['locname'].'">'.$loc['locname'].'</option>';

			}

			$locations .= '</select>';
			
			
		}
		
		$slocations = "";
		
		if($table == "tbl_inventory"){
			
			$this->mongo_db->switch_db($this->database);
			$locs = $this->mongo_db->get_where("tbl_locations",["status"=>"Active"]);
			
			$slocations .= '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue[]" required>';
			
			foreach($locs as $loc){

				$slocations .= '<option value="'.$loc['locname'].'">'.$loc['locname'].'</option>';

			}
			
			$slocations .= '</select>'; 
			
		}
		
		if($column){
			
			$conditionalsetvalueinput = $this->conditions_model->getSetvaluefield($column,$rname,$table,"");
			
		}else{
			
			$conditionalsetvalueinput = "";
			
		}
		
		echo json_encode(array("columns"=>$columns,"operators"=>$oper,"locations"=>$locations,"slocations"=>$slocations,"csetvalue"=>$conditionalsetvalueinput));
		
	}
	
	public function getDatatypeconditions(){
		
		$this->mongo_db->switch_db($this->database);
		
		$column = explode("-",$this->input->post("column"))[0];
		$datatype = explode("-",$this->input->post("column"))[1];
		
		$onchangeColref = $this->input->post("onchangeColref");
		$rCount = $this->input->post("rCount");
		
		if($rCount){
			
			$rRef = $rCount;
			
		}else{
			
			$rRef = "";
			
		}
		
		if($onchangeColref){
			
			$onchg = $onchangeColref;
			
		}else{
			
			$onchg = "";
			
		}
		
		if($this->input->post("uopid")){
			
			$opt = $this->input->post("uopid");
			
		}else{
			
			$opt = "";
			
		}
		
			
		$table = $this->input->post("table");
		
		
		if($column == "location" || $column == "flocation" || $column == "tlcoation" || $column == "tlocation" || ($column == "locname" && $table != "tbl_locations")){
			
			$locations = $this->mongo_db->where(["status"=>'Active'])->get("tbl_locations");
			
			$locnames = "";
			
			$locnames = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$rRef.'[]" required>';			
			
			foreach($locations as $loc){

				$locnames .= '<option value="'.$loc['locname'].'">'.$loc['locname'].'</option>';

			}
			$locnames .= '</select>'; 
			
		}elseif($column == "status"){
			
			$status = '<select class="form-control" name="cond_value'.$rRef.'[]" required=""><option value="Active">Active</option><option value="Inactive">Inactive</option></select>';
			
		}elseif($column == "Type"){
			
			$loctype = '<select class="form-control" name="cond_value'.$rRef.'[]" required><option value="External">External</option><option value="Internal">Internal</option></select>';
			
		}elseif($column == "import_date" || $column == "shippmentdate" || $column == "reportdate" || $column == "processdate" || $column == "chepprocessdate" || $column == "last_report_date" || $column == "audit_date2019"){
			
			$import_date = '<input type="date" class="form-control" name="cond_value'.$rRef.'[]" value="'.date("Y-m-d").'">';
			
		}elseif($column == "adjdirection"){
			
			$accounts = '<select class="form-control" name="cond_value'.$rRef.'[]" required>';
			
			$accounts .= '<option value="IN">IN</option><option value="OUT">OUT</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "accounts" || $column == "user"){
			
			$accounts = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$rRef.'[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active',"appid"=>$_SESSION['appid']],[],"$this->mdb.tbl_auths");
			
			 foreach($users as $u){
				 
				 $accounts .= '<option value="'.$u->uname.'">'.$u->uname.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "item"){
			
			$accounts = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$rRef.'[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active'],[],"$this->database.tbl_items");
			
			 foreach($users as $u){
				 
				 $accounts .= '<option value="'.$u->item_name.'">'.$u->item_name.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "uploadedetochep"){
			
			$accounts = '<select class="form-control" name="cond_value'.$rRef.'[]" required>';
			
			$accounts .= '<option value="Yes">Yes</option><option value="Hold">Hold</option><option value="From Customer">From Customer</option><option value="No">No</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "loctype"){
			
			$accounts = '<select class="form-control" name="cond_value'.$rRef.'[]" required>';
			
			$accounts .= '<option value="External">External</option><option value="Internal">Internal</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "reasonforhold"){
			
			$accounts = '<select class="form-control" name="cond_value'.$rRef.'[]" required>';
			
			$accounts .= '<option value="Reversed in Customer">Reversed in Customer</option><option value="Suspended During Customer Upload">Suspended During Customer Upload</option><option value="Rejected During Customer Upload">Rejected During Customer Upload</option><option value="Error During Customer Upload">Error During Customer Upload</option><option value="Need Customer ID">Need Customer ID</option><option value="Duplicate Transaction">Duplicate Transaction</option><option value="International Shipment">International Shipment</option><option value="Data Error on Submission to">Data Error on Submission to</option>';
			
			$accounts .= '</select>';
			
		}else{
			
			if(($table == "tbl_inventory" && $column == "starting_balance")){
			
				$import_date = '<input type="text" name="cond_value'.$rRef.'[]" class="form-control" pattern="[0-9]+" title="Only Zero & Positive Numbers are allowed" required>';
			
			}elseif(($table == "tbl_transfers" && $column == "quantity")){
			
				$import_date = '<input type="number" name="cond_value'.$rRef.'[]" class="form-control" pattern="^[1-9]" min="1" required>';
			
			}elseif($column == "issues" || $column == "returns" || $column == "transfer_ins" || $column == "transfer_outs" || $column == "adjustments" || $column == "ending_balance" || $column == "audit_count2019" || $column == "quantity"){
				
				$import_date = '<input type="number" name="cond_value'.$rRef.'[]" class="form-control" pattern="^[0-9]" required>';
				
			}else{
				
				$import_date = '<input type="text" name="cond_value'.$rRef.'[]" class="form-control">';
				
			}
		}
		
		$operators = $this->common->getConditionbydatatype($datatype,$table,$column);
		
		$oper = '<select name="condition'.$rRef.'[]" class="form-control '.$onchg.'" rCount="'.$rRef.'" opid="'.$opt.'">';
		
		foreach($operators as $op){
			
			$oper .= '<option value="'.$op.'">'.$op.'</option>';
			
		}
		
		$oper .= '</select>';
		
		$fields = array("locnames"=>$locnames,"status"=>$status,"location_type"=>$loctype,"import_date"=>$import_date,"accounts"=>$accounts,"common"=>$common);
		
		echo json_encode(["fields"=>$fields,"operators"=>$oper]);
		
	}

	public function getFields(){
		
		$this->mongo_db->switch_db($this->database);
		
		$column = explode("-",$this->input->post("column"))[0];
		$datatype = explode("-",$this->input->post("column"))[1];
		$table = $this->input->post("table");
		
		if($column == "location" || $column == "flocation" || $column == "tlcoation" || $column == "tlocation" && $table != "tbl_locations"){
			
			$locations = $this->mongo_db->where(["status"=>'Active'])->get('tbl_locations');
			
			$locnames = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue[]" required>';			
			
			foreach($locations as $loc){

				$locnames .= '<option value="'.$loc['locname'].'">'.$loc['locname'].'</option>';

			}
			$locnames .= '</select>'; 
			
		}elseif($column == "status"){
			
			$status = '<select class="form-control" name="ssetvalue[]" required=""><option value="Active">Active</option><option value="Inactive">Inactive</option></select>';
			
		}elseif($column == "Type"){
			
			$loctype = '<select class="form-control" name="ssetvalue[]" required><option value="External">External</option><option value="Internal">Internal</option></select>';
			
		}elseif($column == "loctype"){
			
			$loctype = '<select class="form-control" name="ssetvalue[]" required><option value="External">External</option><option value="Internal">Internal</option></select>';
			
		}elseif($column == "import_date" || $column == "shippmentdate" || $column == "reportdate" || $column == "processdate" || $column == "chepprocessdate" || $column == "last_report_date" || $column == "audit_date2019"){
			
			$import_date = '<input type="date" class="form-control" name="ssetvalue[]">';
			
		}elseif($column == "accounts" || $column == "user"){
			
			$accounts = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active',"appid"=>$_SESSION['appid']],[],"$this->mdb.tbl_auths");
			
			 foreach($users as $u){
				 
				 $accounts .= '<option value="'.$u->uname.'">'.$u->uname.'</option>';
				 
			 }
			
			$accounts .= '</select>';
		}elseif($column == "item"){
			
			$accounts = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active'],[],"$this->database.tbl_items");
			
			 foreach($users as $u){
				 
				 $accounts .= '<option value="'.$u->item_name.'">'.$u->item_name.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "uploadedetochep"){
			
			$accounts = '<select class="form-control" name="ssetvalue[]" required>';
			
			$accounts .= '<option value="Yes">Yes</option><option value="Hold">Hold</option><option value="From Customer">From Customer</option><option value="No">No</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "adjdirection"){
			
			$accounts = '<select class="form-control" name="ssetvalue[]" required>';
			
			$accounts .= '<option value="IN">IN</option><option value="OUT">OUT</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "reasonforhold"){
			
			$accounts = '<select class="form-control" name="ssetvalue[]" required>';
			
			$accounts .= '<option value="Reversed in Customer">Reversed in Customer</option><option value="Suspended During Customer Upload">Suspended During Customer Upload</option><option value="Rejected During Customer Upload">Rejected During Customer Upload</option><option value="Error During Customer Upload">Error During Customer Upload</option><option value="Need Customer ID">Need Customer ID</option><option value="Duplicate Transaction">Duplicate Transaction</option><option value="International Shipment">International Shipment</option><option value="Data Error on Submission to">Data Error on Submission to</option>';
			
			$accounts .= '</select>';
			
		}else{
			
			if(($table == "tbl_inventory" && $column == "starting_balance")){
			
				$import_date = '<input type="number" name="ssetvalue[]" class="form-control" required>';
			
			}elseif(($table == "tbl_transfers" && $column == "quantity")){
			
				$import_date = '<input type="number" name="ssetvalue[]" class="form-control" pattern="^[1-9]" min="1" required>';
			
			}elseif($column == "issues" || $column == "returns" || $column == "transfer_ins" || $column == "transfer_outs" || $column == "adjustments" || $column == "ending_balance" || $column == "audit_count2019" || $column == "quantity"){
				
				$import_date = '<input type="number" name="ssetvalue[]" class="form-control" pattern="^[0-9]" required>';
				
			}else{
				
				$import_date = '<input type="text" name="ssetvalue[]" class="form-control">';
				
			}
		}
		
		
		$fields = array("locnames"=>$locnames,"status"=>$status,"location_type"=>$loctype,"import_date"=>$import_date,"accounts"=>$accounts);
		
		echo json_encode(["fields"=>$fields]);
		
	}
	
	public function getsetvalfields(){
		
		$table = $this->input->post("table");
		
		$lcolumns = $this->admin->getRow("",["table"=>$table],[],$this->admin->getAppdb().".settings");
	
		$columns = '<select name="ssetvalue[]" class="form-control">'; 
		foreach($lcolumns->labels as $key => $labels){													
			
			if($table == "tbl_inventory"){	
				
				if(($lcolumns->columns[$key] != "location") && ($lcolumns->columns[$key] != "loccode") && ($lcolumns->columns[$key] != "loctype") && ($lcolumns->columns[$key] != "issues") && ($lcolumns->columns[$key] != "returns") && ($lcolumns->columns[$key] != "transfer_ins") && ($lcolumns->columns[$key] != "transfer_outs") && ($lcolumns->columns[$key] != "adjustments") && ($lcolumns->columns[$key] != "ending_balance")){

					$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';

				}
				
			}elseif($table == "tbl_touts"){
				
				if($lcolumns->columns[$key] != "tlocationcode"){

					$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';

				}
					
			}elseif(($table == "tbl_returns") || ($table == "tbl_issues") || ($table == "tbl_adjustments")){
				
				if($lcolumns->columns[$key] != "tlcoationcode"){

					$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';

				}
				
			}else{
				
				$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';
				
			}

		}
		$columns .= '</select>';
		
		echo json_encode(array("columns"=>$columns));
		
	}

	public function createTask(){
		
//		$this->mongo_db->switch_db($this->database);
		
		$data = $this->input->post();
		$data["appId"] = $_SESSION['appid'];
		$data["id"] = $this->admin->insert_id("tbl_tasks");
		
		$d = $this->mongo_db->insert("tbl_tasks",$data);
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'fail';
			
		}
	}
	
	public function editTask(){
		
//		$this->mongo_db->switch_db($this->database);
		
		$tid = $this->input->post_get("tid");
		$table = $this->input->post_get("table");
//		$tid = "";
		$tdata = $this->mongo_db->get_where("tbl_tasks",array("_id"=>new MongoDB\BSON\ObjectID($tid)))[0];
		
		$this->mongo_db->switch_db($this->database);
		$lcolumns = $this->mongo_db->get_where("settings",["table"=>$table])[0];
		
		$cond_column = '';
		
		if(count($tdata['cond_column']) > 0){
			
			$opid = 1;
			
			foreach($tdata['cond_column'] as $key1 => $cc){
				
				$dataType = explode("-",$cc);
				$refopid = "updateOperatorId$key1";
				
				$coldata = $this->common->getUpdatedFieldsOperators($dataType[0],$dataType[1],$table,$tdata['condition'][$key1],$tdata['cond_value'][$key1],$refopid,"","updateonchangeCondition");
				
				$fields = $coldata['fields'];
				$operators = $coldata['operators'];
				
				$remove = ($key1 == 0) ? 'elremoveWhencondition' : 'etlremove_button';
				
				$cond_column .= '<div class="row removeWhenCondition'.$key1.'"><div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px; width:100px;">
										<label>When</label>
									</div>

									<div class="col-md-10">
										<div class="row">

											<div class="col-md-4">
												<div class="form-group">

													<select name="cond_column[]" class="form-control elgetColumn" id="'.$refopid.'" uopid="updChnop'.$opid.'" rid="egetwhenRef'.$key1.'">'; 

													   foreach($lcolumns['labels'] as $key => $labels){
														   $lsel = (($lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key]) == $cc) ? 'selected' : '';															   
															   if($table == "tbl_inventory"){	
			
															   		if(($lcolumns['columns'][$key] != "location") && ($lcolumns['columns'][$key] != "loccode") && ($lcolumns['columns'][$key] != "loctype") && ($lcolumns['columns'][$key] != "issues") && ($lcolumns['columns'][$key] != "returns") && ($lcolumns['columns'][$key] != "transfer_ins") && ($lcolumns['columns'][$key] != "transfer_outs") && ($lcolumns['columns'][$key] != "adjustments")){

																	   $cond_column .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';
																	}
																   
															   }elseif($table == "tbl_touts"){
				
//																	if($lcolumns['columns'][$key] != "tlocationcode"){

																	   $cond_column .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

//																	}

																}elseif(($table == "tbl_returns") || ($table == "tbl_issues") || ($table == "tbl_adjustments")){

																	if($lcolumns['columns'][$key] != "tlcoationcode"){

																	   $cond_column .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

																	}

																}else{
															   
//															   $lsel = (($lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key]) == $cc) ? 'selected' : '';

															   $cond_column .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';
															   
														   }

													   }

												$cond_column .=	'</select>

												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group eopegetwhenRef'.$key1.'">'.$operators.'</div>
											</div>

											<div class="col-md-3">
												<div class="form-group egetwhenRef'.$key1.' '.$refopid.' updChnop'.$opid.'">';
												
												if($tdata['condition'][$key1] == "is during the previous" || $tdata['condition'][$key1] == "is during the next" || $tdata['condition'][$key1] == "is before the previous" || $tdata['condition'][$key1] == "is after the next"){
													
													$cond_column .= '<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days[]" class="form-control">';
													
													for($i=1; $i<=31; $i++){
														
														$selcond_days = ($tdata['cond_days'][$key1] == $i) ? 'selected' : '';
														$cond_column .= '<option value="'.$i.'" '.$selcond_days	.'>'.$i.'</option>';
														
													}
													
													$dayssel = ($tdata['cond_value'][$key1] == 'days') ? 'selected' : '';
													$weeksel = ($tdata['cond_value'][$key1] == 'weeks') ? 'selected' : '';
													$monsel = ($tdata['cond_value'][$key1] == 'months') ? 'selected' : '';
													$yearsel = ($tdata['cond_value'][$key1] == 'years') ? 'selected' : '';
													$ryearsel = ($tdata['cond_value'][$key1] == 'rolling years') ? 'selected' : '';
													
													$cond_column .= '</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value[]" class="form-control"><option value="days" '.$dayssel.'>days</option><option value="weeks" '.$weeksel.'>weeks</option><option value="months" '.$monsel.'>months</option><option value="years" '.$yearsel.'>years</option><option value="rolling years" '.$ryearsel.'>rolling years</option></select></div></div>';
													
												}elseif($tdata['condition'][$key1] == "is during the current"){
													
													$cond_column .= '<select name="cond_value[]" class="form-control">';
													
													$weeksel = ($tdata['cond_value'][$key1] == 'week') ? 'selected' : '';
													$monsel = ($tdata['cond_value'][$key1] == 'month') ? 'selected' : '';
													$quartersel = ($tdata['cond_value'][$key1] == 'quarter') ? 'selected' : '';
													$ryearsel = ($tdata['cond_value'][$key1] == 'year') ? 'selected' : '';
													
													$cond_column .= '<option value="week" '.$weeksel.'>week</option><option value="month" '.$monsel.'>month</option><option value="quarter" '.$quartersel.'>quarter</option><option value="year" '.$ryearsel.'>year</option></select>';
													
												}elseif($tdata['condition'][$key1] == "is today" || $tdata['condition'][$key1] == "is any" || $tdata['condition'][$key1] == "is today or before" || $tdata['condition'][$key1] == "is today or after" || $tdata['condition'][$key1] == "is before today" || $tdata['condition'][$key1] == "is after today" || $tdata['condition'][$key1] == "is before current time" || $tdata['condition'][$key1] == "is after current time"){
													
													$cond_column .= '<input type="hidden" name="cond_value[]" class="form-control" value="'.$tdata['cond_value'][$key1].'">';
													
												}elseif($tdata['condition'][$key1] == "is blank" || $tdata['condition'][$key1] == "is not blank"){
													
													$cond_column .= '<input type="hidden" name="cond_value[]" class="form-control" value="'.$tdata['cond_value'][$key1].'">';
													
												}else{
													
													$cond_column .= $fields;
													
												}	
				
												$cond_column .= '</div>
											</div>

											<div class="col-md-2" align="right">
												<i class="fa fa-plus-circle eaddTaskbind" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>

												<i class="fa fa-times-circle '.$remove.'" lid="removeWhenCondition'.$key1.'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>
											</div>

										</div>	
									</div>	
								</div>
								
								<div class="clearfix"></div>';
				
				++$opid;
				
			}
			
		}else{
			
			$cond_column = '<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>When</label></div><div class="col-md-4" style="margin-top: 5px;"><p>Every Record. <a href="javascript:void(0)" class="elwhenCondition"><strong style="font-size: 18px">add criteria</strong></a></p></div>';
			
		} 
		
		$scond_col = '';
		
		if(count($tdata['scond_column']) > 0){
			
			foreach($tdata['scond_column'] as $key2 => $cc1){
				
				$colVal = explode("-",$cc1);
				
				
				$selcustCondition = ($tdata['ssetcondition'][$key2] == "to a custom value") ? 'selected' : '';
				$selfieldCondition = ($tdata['ssetcondition'][$key2] == "to a field value") ? 'selected' : '';
				
				if($tdata['ssetcondition'][$key2] == "to a field value"){
					
					$setFields = $this->common->getsetvalfields($table,$tdata['ssetvalue'][$key2])['columns'];
					
				}else{
					
					$setFields = $this->common->getUpdatedFields($colVal[0],$table,$tdata['ssetvalue'][$key2]);
					
				}
			
				$scond_col .= '<div class="row removeSetCondition'.$key2.'">

									<div class="col-lg-2" align="right" style="margin-top: 5px;font-size: 18px;">
										<label>Values</label>
									</div>

									<div class="col-lg-10">
										<div class="row">

											<div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div>

											<div class="col-md-3">
												<div class="form-group">

													<select name="scond_column[]" id="egetConditionalst'.$key2.'" class="form-control escond_val" wuid="egetSetfield'.$key2.'">';

														   foreach($lcolumns['labels'] as $key => $labels){
															   
															   $lsel = (($lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key]) == $cc1) ? 'selected' : '';															   
															   if($table == "tbl_inventory"){	
			
															   		if(($lcolumns['columns'][$key] != "location") && ($lcolumns['columns'][$key] != "loccode") && ($lcolumns['columns'][$key] != "loctype") && ($lcolumns['columns'][$key] != "issues") && ($lcolumns['columns'][$key] != "returns") && ($lcolumns['columns'][$key] != "transfer_ins") && ($lcolumns['columns'][$key] != "transfer_outs") && ($lcolumns['columns'][$key] != "adjustments") && ($lcolumns['columns'][$key] != "ending_balance")){

																	   $scond_col .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';
																	}
																   
															   }elseif($table == "tbl_touts"){
				
																	if($lcolumns['columns'][$key] != "tlocationcode" && $lcolumns['columns'][$key] != "flcoationcode" && $lcolumns['columns'][$key] != "chepumi"){

																	   $scond_col .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

																	}
																   
																}elseif(($table == "tbl_returns") || ($table == "tbl_issues") || ($table == "tbl_adjustments")){

																	if($lcolumns['columns'][$key] != "tlcoationcode" && $lcolumns['columns'][$key] != "umi"){

																	   $scond_col .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

																	}

																
															   
															   }else{

//																   $lsel = (($lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key]) == $cc1) ? 'selected' : '';

																   $scond_col .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key] .'" '.$lsel.'>'.$labels.'</option>';															   

															   }
															   
														   }

												$scond_col .= '</select>

												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group">

													<select name="ssetcondition[]" class="form-control egetLocucolumns" id="egetSetfield'.$key2.'" uid="egetConditionalst'.$key2.'">

														<option value="to a custom value" '.$selcustCondition.'>To a custom value</option>
														<option value="to a field value" '.$selfieldCondition.'>To a field value</option>

													</select>

												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group egetConditionalst'.$key2.' egetSetfield'.$key2.'">'.$setFields.'</div>
											</div>

											<div class="col-md-2" align="right">';
											if($key2 == 0){}else{		
						
												$scond_col .= '<i class="fa fa-times-circle elremoveSetcondition" lid="removeSetCondition'.$key2.'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;&nbsp;';

											}
											
										$scond_col .= '<i class="fa fa-plus-circle eaddtask_set" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>
										
										 </div>

										</div>	
									</div>

								</div>
								<div class="clearfix"></div>';
			}
			
		}else{
			
			$scond_col .= '<div class="row">

									<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px">
										<label>Values</label>
									</div>

									<div class="col-md-10">
										<div class="row">

											<div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div>

											<div class="col-md-3">
												<div class="form-group">

													<select name="scond_column[]" id="escond_val" class="form-control" wuid="egetSetfield">';

													   foreach($lcolumns->labels as $key => $labels){	
														   
														   $scond_col .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'">'.$labels.'</option>';

													   }

												$scond_col .= '</select>

												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group">

													<select name="ssetcondition[]" class="form-control egetLocucolumns" uid="egetConditionalst">

														<option value="to a custom value">To a custom value</option>
														<option value="to a field value">To a field value</option>

													</select>

												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group egetConditionalst egetSetfield">

													<input type="text" name="ssetvalue[]" class="form-control">

												</div>
											</div>

											<div class="col-md-2" align="right">
												<i class="fa fa-plus-circle eaddtask_set" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i>
											</div>

										</div>	
									</div>

								</div>';
			
		}
		
		
		$thistory = $this->admin->getArray("",["task_id"=>$tid],[],"$this->mdb.tbl_tasks_history");
		
		$tbody = "";
		if(count($thistory) > 0){
			
			foreach($thistory as $th){
				if($th->ended_at != ""){
				$tbody .= '<tr>
							  <td>'.$th->started_at.'</td>	
							  <td>'.$th->ended_at.'</td>	
							  <td>'.$th->status.'</td>	
							  <td>'.$th->records_processed.'</td>	
						   </tr>';
				}
			}
			
		}
		
		
		echo json_encode(["task_name"=>$tdata['task_name'],"status"=>$tdata['status'],"schedule_type"=>$tdata['schedule_type'],"next_run_date"=>$tdata['next_run_date'],"next_run_time"=>$tdata['next_run_time'],"action"=>$tdata['action'],"cond_column"=>$tdata['cond_column'],"cond_data"=>$cond_column,"scond_column"=>$tdata['scond_column'],"scond_data"=>$scond_col,"ssetvalue"=>$tdata['ssetvalue'],"updatedWhencount"=>count($tdata['cond_column']),"updatedValuescount"=>count($tdata['scond_column']),"thistory"=>$tbody]);
		
	}
	
	public function updateTask(){
		
//		$this->mongo_db->switch_db($this->database);
		
		$data = $this->input->post();
		
		$tid = new MongoDB\BSON\ObjectID($this->input->post("task_id"));
		
//		unset($data['task_id']);
		
		$d = $this->mongo_db->where(array('_id'=>$tid))->set($data)->update('tbl_tasks');
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'fail';
			
		}
	}
	
	public function executeQuery(){
			
		$tid = new MongoDB\BSON\ObjectID($this->input->post("tid"));
		$table = $this->input->post("table");
		
		$tdata = $this->mongo_db->get_where("tbl_tasks",["_id"=>$tid,"appId"=>$_SESSION['appid']])[0];
		
		$this->mongo_db->switch_db($this->database);
		
		$cdate = date("Y-m-d H:ia");
		
//		if($tdata['status'] == "on"){
			
			// Insert Task History	
				
				$hdata = array("task_id"=>$this->input->post("tid"),"started_at"=>date("Y-m-d H:i:s"),"status"=>"Finished (Run Manually)");
				$his = $this->admin->mongoInsert("$this->mdb.tbl_tasks_history",$hdata,"");
				
				$hd = $this->admin->getRow("",[],["sort"=>["_id"=>-1],"limit"=>1],"$this->mdb.tbl_tasks_history");
				$lastid =  $hd->_id;
				
				$hid = $lastid;
			
//				$wheres = $this->where($tdata["cond_column"],$tdata);
//				$ucount = $this->mongo_db->count($table);
				
			
			// where	

			//	set

				$sets = ($tdata["scond_column"]);


				$update = [];			
			
				foreach($sets as $key => $sdata){

					$sset = $tdata["ssetcondition"][$key];

					if($sset == "to a field value"){
						
//						$this->where($tdata["cond_column"],$tdata);
//						$value = $this->mongo_db->select([$sval])->get($table)[0];
						
						$this->where($tdata["cond_column"],$tdata,$table);
						$finaldata = $this->mongo_db->get($table); 

//						$this->where($tdata["cond_column"],$tdata);
//						$this->mongo_db->set([explode("-",$sdata)[0]=>$value[$sval]])->update_all($table);
						
						$finalIds = $this->getFinalupdateids($finaldata,$tdata["condition"],$tdata);
						$sval = explode("-",$tdata["ssetvalue"][$key])[0];
						
						foreach($finalIds as $ids){
							
							$setvalue = $this->mongo_db->select([$sval])->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($ids)])[0];
							
							if(explode("-",$sdata)[1] == "date"){
								
								$conDate = $this->common->getYmddate($setvalue[$sval]);
								$update[$ids][explode("-",$sdata)[0]] = date("Y-m-d",strtotime($conDate));
								
							}elseif(explode("-",$sdata)[1] == "number"){
							
								$update[$ids][explode("-",$sdata)[0]] = ($setvalue[$sval] != "") ? intval($setvalue[$sval]) : intval();
								
							}else{
								
								$update[$ids][explode("-",$sdata)[0]] = $setvalue[$sval];
								
							}
	
						}

					}else{
						
						$this->where($tdata["cond_column"],$tdata,$table);
						$finaldata = $this->mongo_db->get($table); 
						
						$finalIds = $this->getFinalupdateids($finaldata,$tdata["condition"],$tdata);
						$sval = $tdata["ssetvalue"][$key];
						
//						print_r($finalIds);
//						exit();
						
						foreach($finalIds as $ids){
							
							if(explode("-",$sdata)[1] == "date"){
							
								$update[$ids][explode("-",$sdata)[0]] = date("Y-m-d",strtotime($sval));
								
							}elseif(explode("-",$sdata)[1] == "number"){
								
								$update[$ids][explode("-",$sdata)[0]] = ($sval != "") ? intval($sval) : intval();
								
							}else{
								
								$update[$ids][explode("-",$sdata)[0]] = $sval;
								
							}
						}
						
					}

				}
		
//				print_r($update);
//				exit();
			
				if(count($update) > 0){
					
					$postdata = [];
					$tk = 0;
					
//					print_r($update);
//					exit();
					
					foreach($update as $crid => $value){
						
						$this->mongo_db->switch_db($this->database);
						$postdata[] = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($crid)])[0];
						
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
						
						foreach($value as $tkey => $val){
					
							if($tkey == "starting_balance" || $tkey == "issues" || $tkey == "returns" || $tkey == "transfer_ins" || $tkey == "transfer_outs" || $tkey == "adjustments" || $tkey == "ending_balance" || ($tkey == "quantity" && $table == "tbl_touts") || ($tkey == "quantity" && $table == "tbl_issues") || ($tkey == "quantity" && $table == "tbl_returns") || ($tkey == "quantity" && $table == "tbl_adjustments")){

								$postdata[$tk][$tkey] = ($val != "") ? intval($val) : intval();

							}else{

								$postdata[$tk][$tkey] = $val;

							}
						}
						
						/*$this->mongo_db->switch_db($this->mdb);
						$conRulescheck = $this->conditions_model->checkConditionrules($table,$postdata[$ik],$_SESSION['appid'],"");

						if(count($conRulescheck) > 0){

							foreach($conRulescheck as $ck => $con){
								
								$postdata[$ik][$con['column']] = $con['value'];

							}
							
						}*/
						
//						print_r($conRulescheck);
						
						$tk++;
												
					}
					
//					print_r($postdata);
//					exit();
					
					$this->mongo_db->switch_db($this->database);

					$crulechk = $this->admin->getCount("","$this->mdb.tbl_conditional_rules",["appId"=>$_SESSION['appid'],"table"=>$table],[]);
					
					foreach($postdata as $key => $val){
						
						unset($val["_id"]);
						unset($val["id"]);
						

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
//							$val["notes"] = $tlocdata["notes"];

						}
						
//						print_r($val);
						
						$exdata = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'})])[0];

//						print_r($exdata);
						if($table == "tbl_touts"){
							
							$d = $this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'}))->set($val)->update($table);	

							$tins = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$val["tlocationcode"],"tlocationcode",$val["item"]["item_name"],$val["quantity"],"transfer_ins",$exdata);

							$touts = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$val["flcoationcode"],"flcoationcode",$val["item"]["item_name"],$val["quantity"],"transfer_outs",$exdata);
							
						}elseif($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){
							
							if($table == "tbl_adjustments"){
								
								$lcol = "adjustments";
								
							}elseif($table == "tbl_issues"){
								
								$lcol = "issues";
								
							}elseif($table == "tbl_returns"){
								
								$lcol = "returns";
								
							}
							
							$d = $this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'}))->set($val)->update($table);								
							
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
							
							$d = $this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'}))->set($val)->update($table);								
							
						}
						
//							echo $touts." ".$tins;
						
				// end update location inventory					

						$ulocdata = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'})])[0];
					
						if($table == "tbl_locations"){

							if(($ulocdata['locname'] != $exdata['locname']) || ($ulocdata['loccode'] != $exdata["loccode"]) || ($ulocdata['status'] != $exdata["status"]) || ($ulocdata['notes'] != $exdata["notes"]) || ($ulocdata['Type'] != $exdata["Type"])){				

								$udata = ["id"=>$exdata["_id"]->{'$id'},"previous_name"=>$exdata['locname'],"new_name"=>$ulocdata['locname'],"code"=>$ulocdata['loccode'],"status"=>$ulocdata['status'],"notes"=>$ulocdata['notes'],"loctype"=>$ulocdata['Type'],"appId"=>$_SESSION['appid']];
								$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);

							}

						}

						if($table == "tbl_items"){

							if(($ulocdata['item_name'] != $exdata['item_name']) || ($ulocdata['item_code'] != $exdata['item_code']) || ($ulocdata['status'] != $exdata['status'])){

								$udata = ["id"=>$exdata["_id"]->{'$id'},"previous_name"=>$exdata["item_name"],"new_name"=>$ulocdata['item_name'],"code"=>$ulocdata['item_code'],"status"=>$ulocdata['status'],"appId"=>$_SESSION['appid']];
								$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);

							}

						}
						
						
					}
				}
		
			
				if($tdata['schedule_type'] == "daily"){
					
					$plus = "+1 day";
					
				}elseif($tdata['schedule_type'] == "weekly"){
					
					$plus = "+7 days";
					
				}elseif($tdata['schedule_type'] == "monthly"){
					
					$plus = "+1 month";
					
				}
				
				$ndate = date("Y-m-d",strtotime($plus,strtotime($tdata['next_run_date'])));				
				$this->admin->mongoUpdate("$this->mdb.tbl_tasks",["_id"=>new MongoDB\BSON\ObjectID($tid)],["next_run_date"=>$ndate],[]);
				
				$hdata = array("ended_at"=>date("Y-m-d H:i:s"),"records_processed"=>count($finalIds));
				$this->admin->mongoUpdate("$this->mdb.tbl_tasks_history",["_id"=>new MongoDB\BSON\ObjectID($hid)],$hdata,[]);
			
				echo 'success';
		
//		}else{
//			
//			echo 'error';
//			
//		}
		
	}
	
	public function getFinalupdateids($fdata,$conditions,$tdata){
		
		$chkcondarray = ["is during the previous","is during the next","is before the previous","is after the next","is before","is after","is today or before","is today or after","is before today","is after today","is before current time","is after current time"];
		
		$uids = [];
		$wids = [];
		
		foreach($conditions as $cc => $con){

			$column = explode("-",$tdata["cond_column"][$cc])[0];
			$datatype = explode("-",$tdata["cond_column"][$cc])[1];
			
			if($con == "is during the current"){
				
				$sldates = $this->getDays($tdata["cond_value"][$cc]);
				$start = $sldates["astart"];
				$end = $sldates["aend"];
				
				$column = explode("-",$tdata["cond_column"][$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getYmddate($exDate);
					
					if(strtotime($date) >= strtotime($start) && (strtotime($date) <= strtotime($end))){
						
						$uids[] = $fd["_id"]->{'$id'};	
						
					}
					
				}
				
			}elseif($con == "is during the previous"){

				$dates = $this->getDayscount($tdata["cond_value"][$cc],"minus",$tdata["cond_days"][$cc]);
				$start = $dates["astart"];
				$end = $dates["aend"];

				$column = explode("-",$tdata["cond_column"][$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getYmddate($exDate);
					
					if(strtotime($date) >= strtotime($start) && (strtotime($date) <= strtotime($end))){
						
						$uids[] = $fd["_id"]->{'$id'};	
						
					}
					
				}
				
			}elseif($con == "is before the previous"){

				$dates = $this->getDayscount($tdata["cond_value"][$cc],"minus",$tdata["cond_days"][$cc]);
				$start = $dates["astart"];
				$end = $dates["aend"];

				$column = explode("-",$tdata["cond_column"][$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getYmddate($exDate);
					$beforeDate = date('Y-m-d', strtotime('-1 day', strtotime($date)));
					
					if((strtotime($date) < strtotime($start))){
						
						$uids[] = $fd["_id"]->{'$id'};	
						
					}
					
				}
				
			}elseif($con == "is during the next" || $con == "is after the next"){
				
				$dates = $this->getDayscount($tdata["cond_value"][$cc],"plus",$tdata["cond_days"][$cc]);
				$start = $dates["astart"];
				$end = $dates["aend"];

				$column = explode("-",$tdata["cond_column"][$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getYmddate($exDate);
					
					if(strtotime($date) >= strtotime($start) && (strtotime($date) <= strtotime($end))){
						
						$uids[] = $fd["_id"]->{'$id'};	
						
					}
					
				}
				
			}elseif($con == "is before" || $con == "is after"){

				$cdate = date("Y-m-d",strtotime($tdata["cond_value"][$cc]));

				if($con == "is before"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) < strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}	

				}elseif($con == "is after"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) > strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
				}
			}elseif($con == "is today or before" || $con == "is today or after" || $con == "is before today" || $con == "is after today" || $con == "is after current time" || $con == "is before current time"){

				$cdate = date("Y-m-d");

				if($con == "is today or before" || $con == "is before current time"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) <= strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}	

				}elseif($con == "is before today"){
					
					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) < strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
					
				}elseif($con == "is today or after" || $con == "is after current time"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) >= strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}

				}elseif($con == "is after today"){
					
					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) > strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
					
				}

			}elseif($con == "higher than"){
				
				if($datatype == "number"){
				
					foreach($fdata as $fd){

						if(intval($fd[$column]) > intval($tdata["cond_value"][$cc])){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
					
				}else{
					
					foreach($fdata as $fd){

						if($fd[$column] > $tdata["cond_value"][$cc]){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
					
				}
				
			}elseif($con == "lower than"){
				
				if($datatype == "number"){
				
					foreach($fdata as $fd){

						if(intval($fd[$column]) < intval($tdata["cond_value"][$cc])){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
					
				}else{
					
					foreach($fdata as $fd){

						if($fd[$column] < $tdata["cond_value"][$cc]){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
					
				}
				
			}elseif($con == "is blank"){
				
				foreach($fdata as $fd){
					
					if($fd[$column] == "" || $fd[$column] == " "){

						$uids[] = $fd["_id"]->{'$id'};	

					}

				}
				
			}else{
				
				foreach($fdata as $fd){
	
					$wids[] = $fd["_id"]->{'$id'};	
					
				}
	
				
			}
			
		}
		
		$whenids = array_unique($wids);
		$dateids = array_unique($uids);
		
		if((count($whenids) > 0) && (count($dateids) > 0)){
			
			return array_intersect($whenids,$dateids);
			
		}elseif((count($whenids) > 0)){
			
			return $whenids;
			
		}elseif(count($dateids) > 0){
			
			return $dateids;
			
		}
		
		return [];
		
	}
	
	public function where($wheres,$tdata,$table){
		
		$where = [];
		foreach($wheres as $kk => $wh){

			$set = explode("-",$wh);
			$column = explode("-",$wh)[0];

	// where condition

			if($tdata["condition"][$kk] == "contains"){
				
				if($column == "tlocation" || ($column == "locname" && $table == "tbl_inventory") || $column == "flocation" || $column == "tlcoation"){
					
					$this->mongo_db->like("$column.locname",$tdata["cond_value"][$kk]);
					
				}elseif($column == "item"){
					
					$this->mongo_db->like("$column.item_name",$tdata["cond_value"][$kk]);
					
				}else{
				
					$this->mongo_db->like($set[0],$tdata["cond_value"][$kk]);

				}
					
			}elseif($tdata["condition"][$kk] == "is"){
				
				if($set[1] == "date"){
					
					$this->mongo_db->like($set[0],date("Y-m-d",strtotime($tdata["cond_value"][$kk])));
					
				}elseif($set[1] == "number"){
					
					$this->mongo_db->where($set[0],intval($tdata["cond_value"][$kk]));					
					
				}elseif($column == "tlocation" || ($column == "locname" && $table == "tbl_inventory") || $column == "flocation" || $column == "tlcoation"){
					
					$this->mongo_db->where("$column.locname",$tdata["cond_value"][$kk]);
					
				}elseif($column == "item"){
					
					$this->mongo_db->where("$column.item_name",$tdata["cond_value"][$kk]);
					
				}else{
				
					$this->mongo_db->where($set[0],$tdata["cond_value"][$kk]);
				
				}
				
			}elseif($tdata["condition"][$kk] == "does not contain"){

				if($column == "tlocation" || ($column == "locname" && $table == "tbl_inventory") || $column == "flocation" || $column == "tlcoation"){
					
					$this->mongo_db->where_ne("$column.locname",$tdata["cond_value"][$kk]);
					
				}elseif($column == "item"){
					
					$this->mongo_db->where_ne("$column.item_name",$tdata["cond_value"][$kk]);
					
				}else{
				
					$this->mongo_db->where_not_in($set[0],[new MongoDB\BSON\Regex($tdata["cond_value"][$kk],'i')]);

				}
				
			}elseif($tdata["condition"][$kk] == "is not"){
				
				if($set[1] == "date"){
					
					$this->mongo_db->where_not_in($set[0],[new MongoDB\BSON\Regex(date("Y-m-d",strtotime($tdata["cond_value"][$kk])),'i')]);
					
				}elseif($set[1] == "number"){
					
					$this->mongo_db->where_ne($set[0],intval($tdata["cond_value"][$kk]));					
					
				}elseif($column == "tlocation" || ($column == "locname" && $table == "tbl_inventory") || $column == "flocation" || $column == "tlcoation"){
					
					$this->mongo_db->where_ne("$column.locname",$tdata["cond_value"][$kk]);
					
				}elseif($column == "item"){
					
					$this->mongo_db->where_ne("$column.item_name",$tdata["cond_value"][$kk]);
					
				}else{
				
					$this->mongo_db->where_ne($set[0],$tdata["cond_value"][$kk]);				
					
				}
								
			}elseif($tdata["condition"][$kk] == "starts with"){

				if($column == "tlocation" || ($column == "locname" && $table == "tbl_inventory") || $column == "flocation" || $column == "tlcoation"){
					
					$this->mongo_db->like("$column.locname",$tdata["cond_value"][$kk],"i","^",TRUE);
					
				}elseif($column == "item"){
					
					$this->mongo_db->like("$column.item_name",$tdata["cond_value"][$kk],"i","^",TRUE);
					
				}else{
					
					$this->mongo_db->like($set[0],$tdata["cond_value"][$kk],"i","^",TRUE);

				}
					
			}elseif($tdata["condition"][$kk] == "ends with"){

				if($column == "tlocation" || ($column == "locname" && $table == "tbl_inventory") || $column == "flocation" || $column == "tlcoation"){
					
					$this->mongo_db->like("$column.locname",$tdata["cond_value"][$kk],"i",TRUE,"$");
					
				}elseif($column == "item"){
					
					$this->mongo_db->like("$column.item_name",$tdata["cond_value"][$kk],"i",TRUE,"$");
					
				}else{	
				
					$this->mongo_db->like($set[0],$tdata["cond_value"][$kk],"i",TRUE,"$");

				}
				
			}elseif($tdata["condition"][$kk] == "is not blank" || $tdata["condition"][$kk] == "is any"){

				if($column == "tlocation" || ($column == "locname" && $table == "tbl_inventory") || $column == "flocation" || $column == "tlcoation" || $column == "item"){
					
					$this->mongo_db->where_ne("$column.id","");
					
				}else{
				
					$this->mongo_db->where_ne($set[0],"");

				}
				
			}elseif($tdata["condition"][$kk] == "higher than"){

				if($set[1] == "number"){
				
					$this->mongo_db->where_gt($set[0],intval($tdata["cond_value"][$kk]));

				}else{
					
					$this->mongo_db->where_gt($set[0],$tdata["cond_value"][$kk]);					
					
				}
					
			}elseif($tdata["condition"][$kk] == "lower than"){

				if($set[1] == "number"){
				
					$this->mongo_db->where_lt($set[0],intval($tdata["cond_value"][$kk]));

				}else{
					
					$this->mongo_db->where_lt($set[0],$tdata["cond_value"][$kk]);					
					
				}
				
			}
			elseif($tdata["condition"][$kk] == "is during the current"){

				if($tdata["cond_value"][$kk] == "week"){

					$dates = $this->getDays("week");
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "month"){

					$dates = $this->getDays("month");
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);						

				}elseif($tdata["cond_value"][$kk] == "quarter"){

					$dates = $this->getDays("quarter");
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "year"){

					$dates = $this->getDays("year");
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],new MongoDB\BSON\Regex($start,'i'),new MongoDB\BSON\Regex($end,'i'));
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);

				}

			}elseif($tdata["condition"][$kk] == "is during the previous"){

				if($tdata["cond_value"][$kk] == "days"){

					$dates = $this->getDayscount("days","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);


				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);

				}

			}elseif($tdata["condition"][$kk] == "is before the previous"){
				
				
				if($tdata["cond_value"][$kk] == "days"){

					$dates = $this->getDayscount("days","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_lte($set[0],$start);


				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_lte($set[0],$start);

				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_lte($set[0],$start);

				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_lte($set[0],$start);

				}

			}elseif($tdata["condition"][$kk] == "is during the next" || $tdata["condition"][$kk] == "is after the next"){

				if($tdata["cond_value"][$kk] == "days"){

					$dates = $this->getDayscount("days","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//					$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);
				}

			}elseif($tdata["condition"][$kk] == "is before" || $tdata["condition"][$kk] == "is after"){

				$date = date("Y-m-d",strtotime($tdata["cond_value"][$kk]));

				if($tdata["condition"][$kk] == "is before"){

					$this->mongo_db->where_lt($set[0],$date);	

				}elseif($tdata["condition"][$kk] == "is after"){

					$this->mongo_db->where_gt($set[0],$date);

				}

			}elseif($tdata["condition"][$kk] == "is today or before" || $tdata["condition"][$kk] == "is today or after" || $tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time" || $tdata["condition"][$kk] == "is before current time"){

				$date = date("Y-m-d");

				if($tdata["condition"][$kk] == "is today or before" || $tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is before current time"){

					$this->mongo_db->where_lte($set[0],$date);	

				}elseif($tdata["condition"][$kk] == "is today or after" || $tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time"){

					$this->mongo_db->where_gte($set[0],$date);

				}

			}elseif($tdata["condition"][$kk] == "is today"){

				$date = date("Y-m-d");

				$this->mongo_db->like($set[0],$date);	

			}elseif($tdata["condition"][$kk] == "is blank"){

				if($column == "tlocation" || ($column == "locname" && $table == "tbl_inventory") || $column == "flocation" || $column == "tlcoation" || $column == "item"){
					
					$this->mongo_db->where("$column.id","");
					
				}else{
					
					$this->mongo_db->where($set[0],"");	

				}
			}

		}
		
	}

	
	public function run_taskcronjob(){
		
		
		// location & items update starts
		
		
			$this->updateLocations();


	// location & items update ends
		
		
//		$tid = new MongoDB\BSON\ObjectID($this->input->post("tid"));
//		$table = $this->input->post("table");
		
		$taskdata = $this->mongo_db->get_where("tbl_tasks",["status"=>"on"]);
				
		$cdate = date("Y-m-d H:ia");
		
		foreach($taskdata as $tdata){
			
			$this->mongo_db->switch_db($this->mdb."_".$tdata['appId']);
			
			if($tdata['status'] == "on"){
				
				$tid = $tdata["_id"]->{'$id'};	
				$table = $tdata["table"];	
				
				if(($tdata['next_run_date']." ".$tdata['next_run_time']) == $cdate){
				
			// Insert Task History	
				
				$hdata = array("task_id"=>$tid,"started_at"=>date("Y-m-d H:i:s"),"status"=>"Finished (Run Automatically)");
					
//				$his = $this->mongo_db->insert("tbl_tasks_history",$hdata);
				$his = $this->admin->mongoInsert("$this->mdb.tbl_tasks_history",$hdata,"");
				$hd = $this->admin->getRow("",[],["sort"=>["_id"=>-1],"limit"=>1],"$this->mdb.tbl_tasks_history");
				$lastid =  $hd->_id;
				
				$hid = $lastid;
					
				$wheres = $this->where($tdata["cond_column"],$tdata);
				$ucount = $this->mongo_db->count($table);	
				
			// where	

			//	set

				$sets = $tdata["scond_column"];
				$update = [];			
			
				foreach($sets as $key => $sdata){

					$sset = $tdata["ssetcondition"][$key];

					if($sset == "to a field value"){

//						$this->where($tdata["cond_column"],$tdata);
//						$value = $this->mongo_db->select([$sval])->get($table)[0];
						
						$this->where($tdata["cond_column"],$tdata);
						$finaldata = $this->mongo_db->get($table); 

//						$this->where($tdata["cond_column"],$tdata);
//						$this->mongo_db->set([explode("-",$sdata)[0]=>$value[$sval]])->update_all($table);
						
						$finalIds = $this->getFinalupdateids($finaldata,$tdata["condition"],$tdata);
						$sval = explode("-",$tdata["ssetvalue"][$key])[0];
						
						foreach($finalIds as $ids){
							
							$setvalue = $this->mongo_db->select([$sval])->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($ids)])[0];
							
							if(explode("-",$sdata)[1] == "date"){
								
								$conDate = $this->common->getYmddate($setvalue[$sval]);
							
								$update[$ids][explode("-",$sdata)[0]] = date("Y-m-d",strtotime($conDate));
								
//								$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ids)])->set([explode("-",$sdata)[0]=>date("Y-m-d",strtotime($conDate))])->update($table);
								
							}elseif(explode("-",$sdata)[1] == "number"){
								
								$update[$ids][explode("-",$sdata)[0]] = ($setvalue[$sval] != "") ? intval($setvalue[$sval]) : intval();
								
							}else{
								
								$update[$ids][explode("-",$sdata)[0]] = $setvalue[$sval];
								
//								$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ids)])->set([explode("-",$sdata)[0]=>$setvalue[$sval]])->update($table);
								
							}
	
						}

					}else{
						
						$this->where($tdata["cond_column"],$tdata);
						$finaldata = $this->mongo_db->get($table); 
						
						$finalIds = $this->getFinalupdateids($finaldata,$tdata["condition"],$tdata);
						$sval = $tdata["ssetvalue"][$key];
						
//						print_r($finalIds);
//						exit();
						
						foreach($finalIds as $ids){
							
							if(explode("-",$sdata)[1] == "date"){
							
								$update[$ids][explode("-",$sdata)[0]] = date("Y-m-d",strtotime($sval));
								
//								$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ids)])->set([explode("-",$sdata)[0]=>date("Y-m-d",strtotime($sval))])->update($table);
								
							}elseif(explode("-",$sdata)[1] == "number"){
								
								$update[$ids][explode("-",$sdata)[0]] = ($sval != "") ? intval($sval) : intval();
								
							}else{
								
								$update[$ids][explode("-",$sdata)[0]] = $sval;
								
//								$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ids)])->set([explode("-",$sdata)[0]=>$sval])->update($table);
								
							}
							
							
							
						}
						
						

					}

				}
			
//				print_r($update);
//				exit();
			
				if(count($update) > 0){
					
					/*$this->mongo_db->switch_db($this->mdb);

					foreach($update as $crid => $postdata){
					
						$conRulescheck = $this->conditions_model->checkConditionrules($table,$postdata,$tdata['appId'],"");

						if(count($conRulescheck) > 0){

							foreach($conRulescheck as $con){

								$postdata[$con['column']] = $con['value'];

							}
							
							$update[$crid] = $postdata;

						}
												
					}
					
					$this->mongo_db->switch_db($this->mdb."_".$tdata['appId']);

					foreach($update as $kid => $data){

						$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($kid)])->set($data)->update($table);

					}
					*/
					
					
					$postdata = [];
					$ik = 0;
					
//					print_r($update);
//					exit();
					
					foreach($update as $crid => $value){
						
						$this->mongo_db->switch_db($this->mdb."_".$tdata['appId']);
						$postdata[] = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($crid)])[0]; 
						
						foreach($value as $tkey => $val){
					
							if($tkey == "starting_balance" || $tkey == "issues" || $tkey == "returns" || $tkey == "transfer_ins" || $tkey == "transfer_outs" || $tkey == "adjustments" || $tkey == "ending_balance" || ($tkey == "quantity" && $table == "tbl_touts") || ($tkey == "quantity" && $table == "tbl_issues") || ($tkey == "quantity" && $table == "tbl_returns") || ($tkey == "quantity" && $table == "tbl_adjustments")){

								$postdata[$ik][$tkey] = ($val != "") ? intval($val) : intval();

							}else{

								$postdata[$ik][$tkey] = $val;

							}
						}
						
						/*$this->mongo_db->switch_db($this->mdb);
						$conRulescheck = $this->conditions_model->checkConditionrules($table,$postdata[$ik],$tdata['appId'],"");

						if(count($conRulescheck) > 0){

							foreach($conRulescheck as $ck => $con){
								
								$postdata[$ik][$con['column']] = $con['value'];

							}
							
						}*/
						
//						print_r($conRulescheck);
						
						$ik++;
												
					}
					
//					print_r($postdata);
//					exit();
					
					$this->mongo_db->switch_db($this->mdb."_".$tdata['appId']);
					
					$crulechk = $this->admin->getCount("","$this->mdb.tbl_conditional_rules",["appId"=>$tdata['appId'],"table"=>$table],[]);
					

					foreach($postdata as $key => $val){
				
						unset($val["_id"]);
						unset($val["id"]);
						
						if($crulechk > 0){
							
							$conRulescheck = $this->conditions_model->checkConditionrules($table,$val,$tdata['appId'],"");

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
//							$val["notes"] = $tlocdata["notes"];

						}

						
						$exdata = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'})])[0];

//						print_r($exdata);
						if($table == "tbl_touts"){

							$d = $this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'}))->set($val)->update($table);
							
							$tins = $this->common->updateLocationinventorycount($this->mdb."_".$tdata['appId'],"tbl_touts",$tdata['appId'],$val["tlocationcode"],"tlocationcode",$val["item"]["item_name"],$val["quantity"],"transfer_ins",$exdata);

							$touts = $this->common->updateLocationinventorycount($this->mdb."_".$tdata['appId'],"tbl_touts",$tdata['appId'],$val["flcoationcode"],"flcoationcode",$val["item"]["item_name"],$val["quantity"],"transfer_outs",$exdata);
							
						}elseif($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){
							
							if($table == "tbl_adjustments"){
								
								$lcol = "adjustments";
								
							}elseif($table == "tbl_issues"){
								
								$lcol = "issues";
								
							}elseif($table == "tbl_returns"){
								
								$lcol = "returns";
								
							}
							
							$d = $this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'}))->set($val)->update($table);
							
							
							$ff = $this->common->updateLocationinventorycount($this->mdb."_".$tdata['appId'],$table,$tdata['appId'],$val["tlcoationcode"],"tlcoationcode",$val["item"]["item_name"],$val["quantity"],$lcol,$exdata);
							
						}elseif($table == "tbl_inventory"){
						
							$loccode = $val["locname"]["loccode"];
							$item = $val["item"]["item_name"];

							if($exdata["locname"]->locname != $val["locname"]["locname"]){

								$ltdata = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$loccode,"item.item_name"=>$item])[0];

								$sbalance = ($ltdata["starting_balance"] + $val['starting_balance']);
								$issues = ($this->common->getInventorycount($this->mdb."_".$tdata['appId'],"tbl_issues",$tdata['appId'],$loccode,"tlcoationcode",$item));
								$returns = ($this->common->getInventorycount($this->mdb."_".$tdata['appId'],"tbl_returns",$tdata['appId'],$loccode,"tlcoationcode",$item));
								$transfer_ins = ($this->common->getInventorycount($this->mdb."_".$tdata['appId'],"tbl_touts",$tdata['appId'],$loccode,"tlocationcode",$item));
								$transfer_outs = ($this->common->getInventorycount($this->mdb."_".$tdata['appId'],"tbl_touts",$tdata['appId'],$loccode,"flcoationcode",$item));
								$adjustments = ($this->common->getInventorycount($this->mdb."_".$tdata['appId'],"tbl_adjustments",$tdata['appId'],$loccode,"tlcoationcode",$item));
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
								$issues = ($this->common->getInventorycount($this->mdb."_".$tdata['appId'],"tbl_issues",$tdata['appId'],$loccode,"tlcoationcode",$item));
								$returns = ($this->common->getInventorycount($this->mdb."_".$tdata['appId'],"tbl_returns",$tdata['appId'],$loccode,"tlcoationcode",$item));
								$transfer_ins = ($this->common->getInventorycount($this->mdb."_".$tdata['appId'],"tbl_touts",$tdata['appId'],$loccode,"tlocationcode",$item));
								$transfer_outs = ($this->common->getInventorycount($this->mdb."_".$tdata['appId'],"tbl_touts",$tdata['appId'],$loccode,"flcoationcode",$item));
								$adjustments = ($this->common->getInventorycount($this->mdb."_".$tdata['appId'],"tbl_adjustments",$tdata['appId'],$loccode,"tlcoationcode",$item));
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
							
							$d = $this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'}))->set($val)->update($table);
							
						}
						
//							echo $touts." ".$tins;
						
				// end update location inventory					


							
						
						
						$ulocdata = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($postdata[$key]["_id"]->{'$id'})])[0];
					
						if($table == "tbl_locations"){

							if(($ulocdata['locname'] != $exdata['locname']) || ($ulocdata['loccode'] != $exdata["loccode"]) || ($ulocdata['status'] != $exdata["status"]) || ($ulocdata['notes'] != $exdata["notes"]) || ($ulocdata['Type'] != $exdata["Type"])){				

								$udata = ["id"=>$exdata["_id"]->{'$id'},"previous_name"=>$exdata['locname'],"new_name"=>$ulocdata['locname'],"code"=>$ulocdata['loccode'],"status"=>$ulocdata['status'],"notes"=>$ulocdata['notes'],"loctype"=>$ulocdata['Type'],"appId"=>$tdata['appId']];
								$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);

							}

						}

						if($table == "tbl_items"){

							if(($ulocdata['item_name'] != $exdata['item_name']) || ($ulocdata['item_code'] != $exdata['item_code']) || ($ulocdata['status'] != $exdata['status'])){

								$udata = ["id"=>$exdata["_id"]->{'$id'},"previous_name"=>$exdata["item_name"],"new_name"=>$ulocdata['item_name'],"code"=>$ulocdata['item_code'],"status"=>$ulocdata['status'],"appId"=>$tdata['appId']];
								$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);

							}

						}
						
						
					}
					
					
				}
					
				if($tdata['schedule_type'] == "daily"){
					
					$plus = "+1 day";
					
				}elseif($tdata['schedule_type'] == "weekly"){
					
					$plus = "+7 days";
					
				}elseif($tdata['schedule_type'] == "monthly"){
					
					$plus = "+1 month";
					
				}
					
				$ndate = date("Y-m-d",strtotime($plus,strtotime($tdata['next_run_date'])));				
				$this->admin->mongoUpdate("$this->mdb.tbl_tasks",["_id"=>new MongoDB\BSON\ObjectID($tid)],["next_run_date"=>$ndate],[]);
					
				$hdata = array("ended_at"=>date("Y-m-d H:i:s"),"records_processed"=>count($finalIds));
				$this->admin->mongoUpdate("$this->mdb.tbl_tasks_history",["_id"=>new MongoDB\BSON\ObjectID($hid)],$hdata,[]);
				
			}
				
			}
			
		}
		
// task cron job ends		
		
		
		
		
		
	}
	
	public function updateLocations(){
		
		$this->mongo_db->switch_db($this->mdb);
		
		$uloc = $this->mongo_db->get("tbl_locations_updated");
		
		echo '<pre>';
		
		if(count($uloc) > 0){
			
			foreach($uloc as $ul){
				
				$this->mongo_db->switch_db($this->mdb."_".$ul['appId']);
				
				$ftransfers = $this->mongo_db->select(["_id"])->get_where("tbl_touts",["flocation.id"=>$ul['id']]);
				$ttransfers = $this->mongo_db->select(["_id"])->get_where("tbl_touts",["tlcoation.id"=>$ul['id']]);
				$itransfers = $this->mongo_db->select(["_id"])->get_where("tbl_touts",["item.id"=>$ul['id']]);
				
	// tranfers
				
				$tlocdata = ["id"=>$ul["id"],"locname"=>$ul["new_name"],"loccode"=>$ul["code"],"status"=>$ul["status"]];
				$titemdata = ["id"=>$ul["id"],"item_name"=>$ul["new_name"],"status"=>$ul["status"]];
				
				foreach($ftransfers as $ft){
					
//					echo $ft['_id']->{'$id'}."<br>";
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ft['_id']->{'$id'})])->set(["flocation"=>$tlocdata,"flcoationcode"=>$ul['code']])->update("tbl_touts");
					
				}
				
				foreach($ttransfers as $tt){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($tt['_id']->{'$id'})])->set(["tlcoation"=>$tlocdata,"tlocationcode"=>$ul['code']])->update("tbl_touts");
					
				}
				
				foreach($itransfers as $it){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($it['_id']->{'$id'})])->set(["item"=>$titemdata])->update("tbl_touts");
					
				}
				
	// end transfers
				
	// shipments
				
				$locshipments = $this->mongo_db->select(["_id"])->get_where("tbl_issues",["tlocation.id"=>$ul['id']]);
				$itemshipments = $this->mongo_db->select(["_id"])->get_where("tbl_issues",["item.id"=>$ul['id']]);
				
				foreach($locshipments as $ls){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ls['_id']->{'$id'})])->set(["tlocation"=>$tlocdata,"tlcoationcode"=>$ul['code']])->update("tbl_issues");
					
				}
				
				foreach($itemshipments as $li){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($li['_id']->{'$id'})])->set(["item"=>$titemdata])->update("tbl_issues");
					
				}
	// end shipments			
				
	// pickups
				
				$locpickups = $this->mongo_db->select(["_id"])->get_where("tbl_returns",["tlocation.id"=>$ul['id']]);
				$itempickups = $this->mongo_db->select(["_id"])->get_where("tbl_returns",["item.id"=>$ul['id']]);
				
				foreach($locpickups as $lp){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($lp['_id']->{'$id'})])->set(["tlocation"=>$tlocdata,"tlcoationcode"=>$ul['code']])->update("tbl_returns");
					
				}
				
				foreach($itempickups as $pi){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($pi['_id']->{'$id'})])->set(["item"=>$titemdata])->update("tbl_returns");
					
				}
	// end pickups	
				
	// adjustments
				
				$locadjustments = $this->mongo_db->select(["_id"])->get_where("tbl_adjustments",["tlocation.id"=>$ul['id']]);
				$itemadjustments = $this->mongo_db->select(["_id"])->get_where("tbl_adjustments",["item.id"=>$ul['id']]);
				
				foreach($locadjustments as $la){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($la['_id']->{'$id'})])->set(["tlocation"=>$tlocdata,"tlcoationcode"=>$ul['code']])->update("tbl_adjustments");
					
				}
				
				foreach($itemadjustments as $ai){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ai['_id']->{'$id'})])->set(["item"=>$titemdata])->update("tbl_adjustments");
					
				}
				
	// end adjustments
				
	// inventory
				
				$locinventory = $this->mongo_db->select(["_id"])->get_where("tbl_inventory",["locname.id"=>$ul['id']]);
				$iteminventory = $this->mongo_db->select(["_id"])->get_where("tbl_inventory",["item.id"=>$ul['id']]);
				
				foreach($locinventory as $lin){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($lin['_id']->{'$id'})])->set(["location"=>$ul['new_name']." - ".$ul['code'],"locname"=>$tlocdata,"loccode"=>$ul['code'],"loctype"=>$ul['loctype']])->update("tbl_inventory");
					
				}
				
				foreach($iteminventory as $ii){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ii['_id']->{'$id'})])->set(["item"=>$titemdata])->update("tbl_inventory");
					
				}
				
	// end inventory				
	
				
				
		// user locations  (in tbl_auths we have to update status column for existing locations)
				
				
				$this->mongo_db->switch_db($this->mdb);
			
				$users = $this->mongo_db->select(["locations"])->get_where("tbl_auths",["appid"=>$ul['appId']]);
//				print_r($users);
				
				if(count($users) > 0){
					
					foreach($users as $au){
						
						$locations =  json_decode(json_encode($au['locations']),true);
						foreach($locations as $key => $loc){
							
							if($loc["LocationId"] == $ul['id']){
								
								
								$locations[$key] = ["Date"=>date("M-d-Y H:i:s"),"LocationId"=>$ul["id"],"loccode"=>$ul['code'],"LocationName"=>$ul["new_name"],"Type"=>$loc["Type"],"status"=>$ul["status"]];
								
								
							}
							
						}
						
						$this->mongo_db->where("_id",new MongoDB\BSON\ObjectID($au["_id"]->{'$id'}))->set(["locations"=>$locations])->update("tbl_auths");
						
					}
					
				}
				
		// end user locations			
				
			}
			
			
			$this->mongo_db->delete_all("tbl_locations_updated");
			
		}
		
//		print_r($uloc);
		
		
	}


	public function getDays($condition){
	
		if($condition == "week"){

			$signupdate=date("Y-m-d");
			$signupweek=date("W",strtotime($signupdate));
			$year=date("Y",strtotime($signupdate));
			$currentweek = date("W");

			$dto = new DateTime();
			$start = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
			$finish = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');

			$astart = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
			$afinish = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
			
			$data = array("start"=>$start,"end"=>$finish,"astart"=>$astart,"aend"=>$afinish);
			
		}elseif($condition == "month"){
			
			$start = date("Y-m-d", strtotime("first day of this month"));
			$finish = date("Y-m-d", strtotime("last day of this month"));

			$astart = date("Y-m-d", strtotime("first day of this month"));
			$afinish = date("Y-m-d", strtotime("last day of this month"));
			
			$data = array("start"=>$start,"end"=>$finish,"astart"=>$astart,"aend"=>$afinish);
			
		}elseif($condition == "quarter"){
			
			$current_month = date('m');
			$current_year = date('Y');
			if($current_month>=1 && $current_month<=3)
			  {
				$start_date = ($current_year.'-01-01');  // timestamp or 1-Januray 12:00:00 AM
				$end_date = ($current_year.'-03-31');  // timestamp or 1-April 12:00:00 AM means end of 31 March
			  }
			else  if($current_month>=4 && $current_month<=6)
			  {
				$start_date = ($current_year.'-04-01');  // timestamp or 1-April 12:00:00 AM
				$end_date = ($current_year.'-06-30');  // timestamp or 1-July 12:00:00 AM means end of 30 June
			  }
			else  if($current_month>=7 && $current_month<=9)
			  {
				$start_date = ($current_year.'-07-01');  // timestamp or 1-July 12:00:00 AM
				$end_date = ($current_year.'-09-30');  // timestamp or 1-October 12:00:00 AM means end of 30 September
			  }
			else  if($current_month>=10 && $current_month<=12)
			  {
				$start_date = ($current_year.'-10-01');  // timestamp or 1-October 12:00:00 AM
				$end_date = ($current_year.'-12-31');  // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
			  }

			$start = date("Y-m-d",strtotime($start_date));
			$finish = date("Y-m-d",strtotime($end_date));

			$astart = $start_date;
			$afinish = $end_date;
			
			$data = array("start"=>$start,"end"=>$finish,"astart"=>$astart,"aend"=>$afinish);
			
		}elseif($condition == "year"){
			
			$start = date("01-01-Y");
			$finish = date("12-31-Y");
			
			$astart = date("Y-01-01");
			$afinish = date("Y-12-31");

			$data = array("start"=>$start,"end"=>$finish,"astart"=>$astart,"aend"=>$afinish);
			
		}
		
		return $data;
		
	}
	
	public function getDayscount($selection,$condition,$count){
	
		$data = [];
		
		$incdec = ($condition == "plus") ? "+" : "-";
		
		if($selection == "days"){
			
			$days = ($count == 1) ? "day" : "days";
		
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("$incdec$count $days"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$astart = date('Y-m-d');
			$afinish = date('Y-m-d',strtotime("$incdec$count $days"));

			$asdate = ($condition == "plus") ? $astart : $afinish;
			$aedate = ($condition == "plus") ? $afinish : $astart;
			
			$data = array("start"=>$sdate,"end"=>$edate,"astart"=>$asdate,"aend"=>$aedate);
			
		}elseif($selection == "weeks"){
			
			$weeks = ($count == 1) ? "week" : "weeks";
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("$incdec$count $weeks"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$astart = date('Y-m-d');
			$afinish = date('Y-m-d',strtotime("$incdec$count $weeks"));

			$asdate = ($condition == "plus") ? $astart : $afinish;
			$aedate = ($condition == "plus") ? $afinish : $astart;

			$data = array("start"=>$sdate,"end"=>$edate,"astart"=>$asdate,"aend"=>$aedate);
			
		}elseif($selection == "months"){
			
			$months = ($count == 1) ? "month" : "months";
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("$incdec$count $months"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$astart = date('Y-m-d');
			$afinish = date('Y-m-d',strtotime("$incdec$count $months"));

			$asdate = ($condition == "plus") ? $astart : $afinish;
			$aedate = ($condition == "plus") ? $afinish : $astart;
			
			$data = array("start"=>$sdate,"end"=>$edate,"astart"=>$asdate,"aend"=>$aedate);
			
		}elseif($selection == "years" || $selection == "rolling years"){
			
			$years = ($count == 1) ? "year" : "years";
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("$incdec$count $years"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$astart = date('Y-m-d');
			$afinish = date('Y-m-d',strtotime("$incdec$count $years"));

			$asdate = ($condition == "plus") ? $astart : $afinish;
			$aedate = ($condition == "plus") ? $afinish : $astart;
			
			$data = array("start"=>$sdate,"end"=>$edate,"astart"=>$asdate,"aend"=>$aedate);
			
		}
		
		/*$this->mongo_db->switch_db($this->database);
		
		echo '<pre>';
		$da = $this->mongo_db->where_gte("import_date",$sdate)->where_lt("import_date",$edate)->get("tbl_locations");
		
		print_r($da);
		exit();*/
		
		return($data);
		
		
	}
	
	public function delTask($id){
		
		$d = $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->delete("tbl_tasks");
		
		if($d){
			
			echo 'success';	
			
		}else{
			
			echo 'fail';
			
		}
		
	}

}
