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
	}

	public function runTask(){
		
		$this->load->view("admin/runTask");
		
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
		
		$table = $this->input->post("table");
		$lcolumns = $this->admin->getRow("",["table"=>$table],[],$this->admin->getAppdb().".settings");
	
		$columns = ''; 
		
		foreach($lcolumns->labels as $key => $labels){													
			
			$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'">'.$labels.'</option>';

		}
		
		$operators = $this->common->getConditionbydatatype("");
		
		$oper = '';
		
		foreach($operators as $op){
			
			$oper .= '<option value="'.$op.'">'.$op.'</option>';
			
		}
		
		echo json_encode(array("columns"=>$columns,"operators"=>$oper));
		
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
		
		
		if($column == "location" || $column == "flocation" || $column == "tlcoation" || $column == "tlocation" && $table != "tbl_locations"){
			
			$locations = $this->mongo_db->where(["status"=>'Active'])->get("tbl_locations");
			
			$locnames = "";
			
			$locnames = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$rRef.'[]" required>';			
			
			foreach($locations as $loc){

				$locnames .= '<option value='.$loc['locname'].'>'.$loc['locname'].'</option>';

			}
			$locnames .= '</select>'; 
			
		}elseif($column == "status"){
			
			$status = '<select class="form-control" name="cond_value'.$rRef.'[]" required=""><option value="Active">Active</option><option value="Inactive">Inactive</option></select>';
			
		}elseif($column == "Type"){
			
			$loctype = '<select class="form-control" name="cond_value'.$rRef.'[]" required><option value="External">External</option><option value="Internal">Internal</option></select>';
			
		}elseif($column == "import_date" || $column == "shippmentdate" || $column == "reportdate" || $column == "processdate" || $column == "chepprocessdate" || $column == "last_report_date" || $column == "audit_date2019"){
			
			$import_date = '<input type="date" class="form-control" name="cond_value'.$rRef.'[]" value="'.date("Y-m-d").'">';
			
		}elseif($column == "adjdirection"){
			
			$accounts = '<select class="form-control" name="ssetvalue[]" required>';
			
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
			
		}elseif($column == "reasonforhold"){
			
			$accounts = '<select class="form-control" name="cond_value'.$rRef.'[]" required>';
			
			$accounts .= '<option value="Reversed in Customer">Reversed in Customer</option><option value="Suspended During Customer Upload">Suspended During Customer Upload</option><option value="Rejected During Customer Upload">Rejected During Customer Upload</option><option value="Error During Customer Upload">Error During Customer Upload</option><option value="Need Customer ID">Need Customer ID</option><option value="Duplicate Transaction">Duplicate Transaction</option><option value="International Shipment">International Shipment</option><option value="Data Error on Submission to">Data Error on Submission to</option>';
			
			$accounts .= '</select>';
			
		}else{
			
			$common = '<input type="text" name="cond_value'.$rRef.'[]" class="form-control">';
			
		}
		
		$operators = $this->common->getConditionbydatatype($datatype);
		
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

				$locnames .= '<option value='.$loc['locname'].'>'.$loc['locname'].'</option>';

			}
			$locnames .= '</select>'; 
			
		}elseif($column == "status"){
			
			$status = '<select class="form-control" name="ssetvalue[]" required=""><option value="Active">Active</option><option value="Inactive">Inactive</option></select>';
			
		}elseif($column == "Type"){
			
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
			
		}
		
		
		$fields = array("locnames"=>$locnames,"status"=>$status,"location_type"=>$loctype,"import_date"=>$import_date,"accounts"=>$accounts);
		
		echo json_encode(["fields"=>$fields]);
		
	}
	
	public function getsetvalfields(){
		
		$table = $this->input->post("table");
		
		$lcolumns = $this->admin->getRow("",["table"=>$table],[],$this->admin->getAppdb().".settings");
	
		$columns = '<select name="ssetvalue[]" class="form-control">'; 
		foreach($lcolumns->labels as $key => $labels){													
			
			$columns .= '<option value="'.$lcolumns->columns[$key].'">'.$labels.'</option>';

		}
		$columns .= '</select>';
		
		echo json_encode(array("columns"=>$columns));
		
	}

	public function createTask(){
		
//		$this->mongo_db->switch_db($this->database);
		
		$data = $this->input->post();
		$data["appId"] = $_SESSION['appid'];
		
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
				
				$coldata = $this->common->getUpdatedFieldsOperators($dataType[0],$dataType[1],$table,$tdata['condition'][$key1],$tdata['cond_value'][$key1],$refopid);
				
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
														   
														   $cond_column .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

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
															   
															   $scond_col .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key] .'" '.$lsel.'>'.$labels.'</option>';

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
				
				$tbody .= '<tr>
							  <td>'.$th->started_at.'</td>	
							  <td>'.$th->ended_at.'</td>	
							  <td>'.$th->status.'</td>	
							  <td>'.$th->records_processed.'</td>	
						   </tr>';
				
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
		
		if($tdata['status'] == "on"){
			
			// Insert Task History	
				
				$hdata = array("task_id"=>$this->input->post("tid"),"started_at"=>date("Y-m-d H:i:s"),"status"=>"Finished (Run Manually)");
				$his = $this->admin->mongoInsert("$this->mdb.tbl_tasks_history",$hdata,"");
				
				$hd = $this->admin->getRow("",[],["sort"=>["_id"=>-1],"limit"=>1],"$this->mdb.tbl_tasks_history");
				$lastid =  $hd->_id;
				
				$hid = $lastid;
			
				$wheres = $this->where($tdata["cond_column"],$tdata);
				$ucount = $this->mongo_db->count($table);
				
			// where	

			//	set

				$sets = $tdata["scond_column"];

				foreach($sets as $key => $sdata){

					$sset = $tdata["ssetcondition"][$key];

					if($sset == "to a field value"){

						$sval = $tdata["ssetvalue"][$key];
						$this->where($tdata["cond_column"],$tdata);
						$value = $this->mongo_db->select([$sval])->get($table)[0];

						$this->where($tdata["cond_column"],$tdata);
						$this->mongo_db->set([explode("-",$sdata)[0]=>$value[$sval]])->update_all($table);

					}else{

						$sval = $tdata["ssetvalue"][$key];
						$this->where($tdata["cond_column"],$tdata);
						$this->mongo_db->set([explode("-",$sdata)[0]=>$sval])->update_all($table);

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
				
				$hdata = array("ended_at"=>date("Y-m-d H:i:s"),"records_processed"=>$ucount);
				$this->admin->mongoUpdate("$this->mdb.tbl_tasks_history",["_id"=>new MongoDB\BSON\ObjectID($hid)],$hdata,[]);
			
				echo 'success';
		
		}else{
			
			echo 'error';
			
		}
		
	}
	
	public function where($wheres,$tdata){
		
		$where = [];
		foreach($wheres as $kk => $wh){

			$set = explode("-",$wh);

	// where condition

			if($tdata["condition"][$kk] == "contains"){

				$this->mongo_db->like($set[0],$tdata["cond_value"][$kk]);

			}elseif($tdata["condition"][$kk] == "is"){
				
				if($set[1] == "date"){
					
					$this->mongo_db->like($set[0],date("m-d-Y",strtotime($tdata["cond_value"][$kk])));
					
				}else{
				
					$this->mongo_db->where($set[0],$tdata["cond_value"][$kk]);
				
				}
				
			}elseif($tdata["condition"][$kk] == "does not contain"){

				$this->mongo_db->where_not_in($set[0],[new MongoDB\BSON\Regex($tdata["cond_value"][$kk],'i')]);

			}elseif($tdata["condition"][$kk] == "is not"){
				
				if($set[1] == "date"){
					
					$this->mongo_db->where_not_in($set[0],[new MongoDB\BSON\Regex(date("m-d-Y",strtotime($tdata["cond_value"][$kk])),'i')]);
					
				}else{
				
					$this->mongo_db->where_ne($set[0],$tdata["cond_value"][$kk]);				
					
				}
								
			}elseif($tdata["condition"][$kk] == "starts with"){

				$this->mongo_db->like($set[0],$tdata["cond_value"][$kk],"i","^",TRUE);

			}elseif($tdata["condition"][$kk] == "ends with"){

				$this->mongo_db->like($set[0],$tdata["cond_value"][$kk],"i",TRUE,"$");

			}elseif($tdata["condition"][$kk] == "is blank"){

				$this->mongo_db->where_or([$set[0]=>"",$set[0]=>" "]);

			}elseif($tdata["condition"][$kk] == "is not blank"){

				$this->mongo_db->where_ne($set[0],"");

			}elseif($tdata["condition"][$kk] == "higher than"){

				$this->mongo_db->where_gt($set[0],intval($tdata["cond_value"][$kk]));

			}elseif($tdata["condition"][$kk] == "lower than"){

				$this->mongo_db->where_lt($set[0],intval($tdata["cond_value"][$kk]));

			}elseif($tdata["condition"][$kk] == "is during the current"){

				if($tdata["cond_value"][$kk] == "week"){

					$dates = $this->getDays("week");
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "month"){

					$dates = $this->getDays("month");
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);						

				}elseif($tdata["cond_value"][$kk] == "quarter"){

					$dates = $this->getDays("quarter");
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "year"){

					$dates = $this->getDays("year");
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);

				}

			}elseif($tdata["condition"][$kk] == "is during the previous" || $tdata["condition"][$kk] == "is before the previous"){

				if($tdata["cond_value"][$kk] == "days"){

					$dates = $this->getDayscount("days","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);


				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);

				}

			}elseif($tdata["condition"][$kk] == "is during the next" || $tdata["condition"][$kk] == "is after the next"){

				if($tdata["cond_value"][$kk] == "days"){

					$dates = $this->getDayscount("days","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);

				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//						$this->mongo_db->where_between($set[0],$start,$end);
					$this->mongo_db->where_gte($set[0],$start)->where_lt($set[0],$end);
				}

			}elseif($tdata["condition"][$kk] == "is before" || $tdata["condition"][$kk] == "is after"){

				$date = $tdata["cond_value"][$kk];

				if($tdata["condition"][$kk] == "is before"){

					$this->mongo_db->where_lt([$set[0]=>$date]);	

				}elseif($tdata["condition"][$kk] == "is after"){

					$this->mongo_db->where_gt([$set[0]=>$date]);

				}

			}elseif($tdata["condition"][$kk] == "is today or before" || $tdata["condition"][$kk] == "is today or after" || $tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time" || $tdata["condition"][$kk] == "is before current time"){

				$date = date("Y-m-d");

				if($tdata["condition"][$kk] == "is today or before" || $tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is before current time"){

					$this->mongo_db->where_lt([$set[0]=>$date]);	

				}elseif($tdata["condition"][$kk] == "is today or after" || $tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time"){

					$this->mongo_db->where_gt([$set[0]=>$date]);

				}

			}elseif($tdata["condition"][$kk] == "is today"){

				$date = date("m-d-Y");

				$this->mongo_db->like($set[0],$date);	

			}

		}
		
	}
	
	public function run_taskcronjob(){
		
		
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
				
				$hdata = array("task_id"=>$tid,"started_at"=>date("Y-m-d H:i:s"),"status"=>"Finished (Run Manually)");
					
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

				foreach($sets as $key => $sdata){

					$sset = $tdata["ssetcondition"][$key];

					if($sset == "to a field value"){

						$sval = $tdata["ssetvalue"][$key];
						$this->where($tdata["cond_column"],$tdata);
						$value = $this->mongo_db->select([$sval])->get($table)[0];

						$this->where($tdata["cond_column"],$tdata);
						$this->mongo_db->set([explode("-",$sdata)[0]=>$value[$sval]])->update_all($table);

					}else{

						$sval = $tdata["ssetvalue"][$key];
						$this->where($tdata["cond_column"],$tdata);
						$this->mongo_db->set([explode("-",$sdata)[0]=>$sval])->update_all($table);

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
					
				$hdata = array("ended_at"=>date("Y-m-d H:i:s"),"records_processed"=>$ucount);
				$this->admin->mongoUpdate("$this->mdb.tbl_tasks_history",["_id"=>new MongoDB\BSON\ObjectID($hid)],$hdata,[]);
				
			}
				
			}
			
		}
		
	}

	public function getDays($condition){
	
		if($condition == "week"){
		
//			$start = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
//			$finish = (date('D') != 'Sun') ? date('Y-m-d', strtotime('next Sunday')) : date('Y-m-d');

			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("+7 days"));

			$data = array("start"=>$start,"end"=>$finish);
			
		}elseif($condition == "month"){
			
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("+30 days"));

			$data = array("start"=>$start,"end"=>$finish);
			
		}elseif($condition == "quarter"){
			
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("+3 months"));

			$data = array("start"=>$start,"end"=>$finish);
			
		}elseif($condition == "year"){
			
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("+1 year"));

			$data = array("start"=>$start,"end"=>$finish);
			
		}
		
		return $data;
		
	}
	
	public function getDayscount($selection,$condition,$count){
	
		$incdec = ($condition == "plus") ? "+" : "-";
		
		if($selection == "days"){
			
			$days = ($count == 1) ? "day" : "days";
		
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("$incdec$count $days"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$data = array("start"=>$sdate,"end"=>$edate);
			
		}elseif($selection == "weeks"){
			
			$weeks = ($count == 1) ? "week" : "weeks";
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("$incdec$count $weeks"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$data = array("start"=>$sdate,"end"=>$edate);
			
		}elseif($selection == "months"){
			
			$months = ($count == 1) ? "month" : "months";
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("$incdec$count $months"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$data = array("start"=>$sdate,"end"=>$edate);
			
		}elseif($selection == "years" || $selection == "rolling years"){
			
			$years = ($count == 1) ? "year" : "years";
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("$incdec$count $years"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$data = array("start"=>$sdate,"end"=>$edate);
			
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
