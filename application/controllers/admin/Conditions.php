<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Conditions extends CI_Controller {
	
	public function __construct(){
		
		parent::__construct();
		
		if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
		$this->mdb = mongodb;
		
	}
	
	public function test(){
		
		$this->mongo_db->switch_db($this->database);
		
		echo '<pre>';
		$data = $this->mongo_db->like("flocation.id","5e9083d044640000b00035c6")->get("tbl_touts");
		
		print_r($data);
	}
	
	public function updateDatatypes(){
		
		$apps = $this->mongo_db->get_where("tbl_apps",["deleted"=>0]);
		
		echo '<pre>';
		foreach($apps as $app){
			
			$this->mongo_db->switch_db($this->mdb."_".$app["appId"]);
			
			$table = "tbl_touts";
			$settings = $this->mongo_db->get_where("settings",["table"=>$table])[0];
			
			$columns = $settings["columns"];
			$dtypes = $settings["dataType"];
			
			$datatypes = [];
			foreach($columns as $ck => $col){
				
//				if($col == "starting_balance"){ // tbl_inventory
//				if($col == "status"){           // tbl_items 
//				if($col == "loctype"){          // tbl_inventory
				if($col == "quantity"){         // tbl_adjustments, tbl_issues, tbl_returns, tbl_touts
				
					$datatypes[] = "number"; 	
//					$datatypes[] = "select"; 	
//					$datatypes[] = "select"; 	
						
				}else{
					
					$datatypes[] = $dtypes[$ck];
				}
				
			}
			
			$this->mongo_db->where(["table"=>$table])->set(["dataType"=>$datatypes])->update('settings');
			
//			print_r($datatypes);
			
			$this->mongo_db->switch_db($this->mdb);
			
		}
		
	}
	
	public function getColumns(){
		
		$this->mongo_db->switch_db($this->database);
		
		$table = $this->input->post("table");
		$rcount = $this->input->post("rcount");
		
		$lcolumns = $this->mongo_db->get_where("settings",["table"=>$table])[0];
		
		$columns = ''; 
		
		$columns .= '<select name="ssetvalue'.$rcount.'[]" class="form-control">';
		
		foreach($lcolumns['labels'] as $key => $labels){	
			
			if($table == "tbl_inventory"){	
			
				if(($lcolumns['columns'][$key] != "location") && ($lcolumns['columns'][$key] != "loccode") && ($lcolumns['columns'][$key] != "loctype") && ($lcolumns['columns'][$key] != "issues") && ($lcolumns['columns'][$key] != "returns") && ($lcolumns['columns'][$key] != "transfer_ins") && ($lcolumns['columns'][$key] != "transfer_outs") && ($lcolumns['columns'][$key] != "adjustments") && ($lcolumns['columns'][$key] != "ending_balance")){
				
					$columns .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'">'.$labels.'</option>';

				}
					
			}elseif($table == "tbl_touts"){
				
				if($lcolumns['columns'][$key] != "tlocationcode" && $lcolumns['columns'][$key] != "flocationcode" && $lcolumns['columns'][$key] != "shipperpo" && $lcolumns['columns'][$key] != "chepumi"){

					$columns .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'">'.$labels.'</option>';

				}

			}elseif(($table == "tbl_returns") || ($table == "tbl_issues") || ($table == "tbl_adjustments")){

				if($lcolumns['columns'][$key] != "tlcoationcode" && $lcolumns['columns'][$key] != "umi"){

					$columns .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'">'.$labels.'</option>';

				}

			}else{
				
				$columns .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'">'.$labels.'</option>';
				
			}
		}
		
		$columns .= '</select>';
		
		echo $columns;
		
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
			
			if(($table == "tbl_inventory" && $column == "starting_balance") || ($table == "tbl_transfers" && $column == "quantity")){
			
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

	public function editField(){
		
		$vrid = $this->input->post("vrid");
		
		$vrdata = $this->mongo_db->get_where("tbl_conditional_rules",array("_id"=>new MongoDB\BSON\ObjectID($vrid)))[0];
		$table = $vrdata["table"];
		
		$this->mongo_db->switch_db($this->database);
		$lcolumns = $this->mongo_db->get_where("settings",["table"=>$table])[0];
		
		$cond_column = '';
		
		if(count($vrdata['conditions']) > 0){
			
			$opid = 1;
			$deleterule = (count($vrdata['conditions']) == 1) ? "deleteallCRules" : "deleteCRule";
			
			foreach($vrdata['conditions'] as  $tdata){
				
//			$tdata = $vrdata['conditions'];
					
				$rkey=random_string("alnum",10);

				
				$cond_column .= '<div class="row delCSelRule'.$rkey.'" style="background-color: #ccc;padding: 12px;margin: 5px;border-radius: 5px;"><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 16px;padding:0px;"><label>When</label></div><div class="col-md-10" style="background-color: #eee;">';
					
				$fid = 0;
				
				foreach($tdata->cond_column as $key1 => $coc){
					
					$delete = "";

					$refkey=random_string("alnum",10);				
					$dataType = explode("-",$coc);

					$coldata = $this->common->getUpdatedFieldsOperators($dataType[0],$dataType[1],$table,$tdata->condition[$key1],$tdata->cond_value[$key1],'getopCruleval'.$refkey.'',"$rkey","changeOperatorCrule");

					$fields = $coldata['fields'];
					$operators = $coldata['operators'];
	
						$cond_column .= '<div class="row removeLabel'.$refkey.'" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column'.$rkey.'[]" class="form-control changeCLabelArule" rid="refCvalLabels'.$refkey.'" rCount="'.$rkey.'" id="getopCruleval'.$refkey.'" uopid="getopCruleval'.$refkey.'">';
														   foreach($lcolumns['labels'] as $key => $labels){
															   
															   $lsel = (($lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key]) == $coc) ? 'selected' : '';															   
															   if($table == "tbl_inventory"){	
			
															   		if(($lcolumns['columns'][$key] != "location") && ($lcolumns['columns'][$key] != "loccode") && ($lcolumns['columns'][$key] != "loctype") && ($lcolumns['columns'][$key] != "issues") && ($lcolumns['columns'][$key] != "returns") && ($lcolumns['columns'][$key] != "transfer_ins") && ($lcolumns['columns'][$key] != "transfer_outs") && ($lcolumns['columns'][$key] != "adjustments")){

																	   $cond_column .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';
																	}
																   
															   }elseif($table == "tbl_touts"){
				
																	if($lcolumns['columns'][$key] != "tlocationcode"){

																	   $cond_column .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

																	}

																}elseif(($table == "tbl_returns") || ($table == "tbl_issues") || ($table == "tbl_adjustments")){

																	if($lcolumns['columns'][$key] != "tlcoationcode"){

																	   $cond_column .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

																	}

																}else{
				
																	$cond_column .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

																}
														   }

													$cond_column .=	'</select>

													</div></div><div class="col-md-3"><div class="form-group oprefCvalLabels'.$refkey.'">'.$operators.'</div>
												</div>

												<div class="col-md-3"><div class="form-group refCvalLabels'.$refkey.' getopCruleval'.$refkey.' crulesConditionValue">';
                                                    if($tdata->condition[$key1] == "is during the current"){

														$cond_column .= '<div class="row">';

														$current_weeksel = ($tdata->cond_value[$key1] == 'week') ? 'selected' : '';
														$current_monsel = ($tdata->cond_value[$key1] == 'month') ? 'selected' : '';
														$current_yearsel = ($tdata->cond_value[$key1] == 'year') ? 'selected' : '';
														$current_quarter = ($tdata->cond_value[$key1] == 'quarter') ? 'selected' : '';

														$cond_column .= '<div class="col-md-8" style="padding:0px"><select name="cond_value'.$rkey.'[]" class="form-control"><option value="week" '.$current_weeksel.'>week</option><option value="month" '.$current_monsel.'>month</option><option value="quarter" '.$current_quarter.'>quarter</option><option value="year" '.$current_yearsel.'>year</option></select></div></div>';

													}
													elseif($tdata->condition[$key1] == "is during the previous" || $tdata->condition[$key1] == "is during the next" || $tdata->condition[$key1] == "is before the previous" || $tdata->condition[$key1] == "is after the next"){

														$cond_column .= '<div class="row"><div class="col-md-4" style="padding:0px"><select name="cond_days'.$rkey.'[]" class="form-control">';

														for($i=1; $i<=31; $i++){

															$selcond_days = ($tdata->cond_days[$key1] == $i) ? 'selected' : '';

															$cond_column .= '<option value="'.$i.'" '.$selcond_days	.'>'.$i.'</option>';

														}

														$dayssel = ($tdata->cond_value[$key1] == 'days') ? 'selected' : '';
														$weeksel = ($tdata->cond_value[$key1] == 'weeks') ? 'selected' : '';
														$monsel = ($tdata->cond_value[$key1] == 'months') ? 'selected' : '';
														$yearsel = ($tdata->cond_value[$key1] == 'years') ? 'selected' : '';
														$ryearsel = ($tdata->cond_value[$key1] == 'rolling years') ? 'selected' : '';

														$cond_column .= '</select></div><div class="col-md-8" style="padding:0px"><select name="cond_value'.$rkey.'[]" class="form-control"><option value="days" '.$dayssel.'>days</option><option value="weeks" '.$weeksel.'>weeks</option><option value="months" '.$monsel.'>months</option><option value="years" '.$yearsel.'>years</option><option value="rolling years" '.$ryearsel.'>rolling years</option></select></div></div>';

													}elseif($tdata->condition[$key1] == "is today" || $tdata->condition[$key1] == "is any" || $tdata->condition[$key1] == "is today or before" || $tdata->condition[$key1] == "is today or after" || $tdata->condition[$key1] == "is before today" || $tdata->condition[$key1] == "is after today" || $tdata->condition[$key1] == "is before current time" || $tdata->condition[$key1] == "is after current time" || $tdata->condition[$key1] == "is blank" || $tdata->condition[$key1] == "is not blank"){

														$cond_column .= '<div class="row"><div class="col-md-4" style="padding:0px">
														
														<input type="hidden" name="cond_value'.$rkey.'[]" value="">
														
														</div></div>';

													}else{

														$cond_column .= $fields;

													}	

					
													if($fid != 0){
														
														$delete = '<i class="fa fa-times-circle remClabels" lid="removeLabel'.$refkey.'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;';
														
													}
					
													$cond_column .= '</div>
												</div><div class="col-md-2" align="right">'.$delete.'<i class="fa fa-plus-circle addWhencondCrule" crid="addedWhencondCrule'.$rkey.'" rCount="'.$rkey.'" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>';
					++$fid;
					
				}
											$cond_column .=	'<div class="addedWhencondCrule'.$rkey.'"></div></div><div class="col-md-1" align="right"><i class="fa fa-times-circle '.$deleterule.'" delRule="delCSelRule'.$rkey.'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div>
											<div class="col-md-1" align="center" style="margin-top: 5px;font-size: 15px;padding:0px;"><label>Values</label></div><div class="col-md-10" style="background-color: #eee;"><div class="row"><div class="col-md-1" style="margin-top: 7px;font-size: 16px;font-weight: 400;">Set</div><div class="col-md-3"><div class="form-group">
											
											<select name="ssetcondition'.$rkey.'[]" class="form-control getConditionalLabels" uid="getCconConditionalst'.$rkey.'" rcount="'.$rkey.'">';
											
											$tcv = ($tdata->ssetcondition[0] == "to a custom value") ? "selected" : "";
											$trv = ($tdata->ssetcondition[0] == "to a field value") ? "selected" : "";
				
											$ssetvalueinput = "";

											if($tdata->ssetcondition[0] == "to a field value"){
												
												$ssetvalueinput .= '<select name="ssetvalue'.$rkey.'[]" class="form-control">';

												foreach($lcolumns['labels'] as $key => $labels){

												   $lsel = (($lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key]) == $tdata->ssetvalue[0]) ? 'selected' : '';
													
												   if($table == "tbl_inventory"){	
			
														if(($lcolumns['columns'][$key] != "location") && ($lcolumns['columns'][$key] != "loccode") && ($lcolumns['columns'][$key] != "loctype") && ($lcolumns['columns'][$key] != "issues") && ($lcolumns['columns'][$key] != "returns") && ($lcolumns['columns'][$key] != "transfer_ins") && ($lcolumns['columns'][$key] != "transfer_outs") && ($lcolumns['columns'][$key] != "adjustments") && ($lcolumns['columns'][$key] != "ending_balance")){

														   $ssetvalueinput .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

														}

												   }elseif($table == "tbl_touts"){
				
														if($lcolumns['columns'][$key] != "tlocationcode" && $lcolumns['columns'][$key] != "flocationcode" && $lcolumns['columns'][$key] != "shipperpo" && $lcolumns['columns'][$key] != "chepumi"){

														   $ssetvalueinput .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

														}

													}elseif(($table == "tbl_returns") || ($table == "tbl_issues") || ($table == "tbl_adjustments")){

														if($lcolumns['columns'][$key] != "tlcoationcode" && $lcolumns['columns'][$key] != "umi"){

														   $ssetvalueinput .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

														}



												   }else{

													   $ssetvalueinput .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

													}	


											    }

												$ssetvalueinput .= '</select>';

												
											}else{
												
//												$ssetvalueinput = '<input type="text" name="ssetvalue'.$rkey.'[]" value="'.$tdata->ssetvalue[0].'" class="form-control">';
												
												$ssetvalueinput = $this->conditions_model->getSetvaluefield($vrdata['field'],$rkey,$table,$tdata->ssetvalue[0]);
												
											}
												
									$cond_column .=	'<option value="to a custom value" '.$tcv.'>To a custom value</option>
												<option value="to a field value" '.$trv.'>To a record value</option>
											</select></div></div><div class="col-md-3"><div class="form-group getCconConditionalst'.$rkey.'">'.$ssetvalueinput.'</div></div><div class="col-md-2" align="right"></div></div><input type="hidden" name="rulesCCount[]" value="'.$rkey.'"></div></div>';

					++$opid;
				}
			
			
		}else{
			
//			$cond_column = '<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>When</label></div><div class="col-md-4" style="margin-top: 5px;"><p>Every Record. <a href="javascript:void(0)" class="elwhenCondition"><strong style="font-size: 18px">add criteria</strong></a></p></div>';
			
		} 
		
		echo json_encode(["status"=>$vrdata["status"],"cond_data"=>$cond_column]);
		
	}
		
	public function createFields(){
		
		$rCount = $this->input->post("rulesCCount");

		$data = [];	
		
		$status = $this->input->post("conditionalrule");
		
		if($status == "on"){
		
			if(count($rCount) > 0){

				foreach($rCount as $rc){

					$cval = $this->input->post("cond_value$rc");

					$cdays = [];
					$conditionvalue = [];

					$i = 0;
					foreach($cval as $kk => $cvalue){

						if($cvalue == "days" || $cvalue == "weeks" || $cvalue == "months" || $cvalue == "years" || $cvalue == "rolling years"){

							$cdays[] = $this->input->post("cond_days$rc")[$i];
							$i++;

						}else{

							$cdays[] = "";

						}
						
						if($cvalue == "is blank" || $cvalue == "is not blank"){
							
							$conditionvalue[] = ""; 
							
						}else{
							
							$conditionvalue[] = $cvalue;
							
						}

					}

					$data[] = array(
								"cond_column"=>$this->input->post("cond_column$rc"),
								"condition"=>$this->input->post("condition$rc"),
								"cond_value"=>$conditionvalue,
								"ssetcondition"=>$this->input->post("ssetcondition$rc"),
								"ssetvalue"=>$this->input->post("ssetvalue$rc"),
								"cond_days"=>$cdays
							  ); 

				}

				$fdata = array(

					"table" =>$this->input->post("contable"),
					"field" =>$this->input->post("fieldname"),
					"status" =>$this->input->post("conditionalrule"),
					"conditions" =>$data,
					"appId" => $_SESSION['appid']

				);
				
//				echo '<pre>';
//				print_r($fdata);
//				exit();
				
				$vid = $this->input->post("conid");

				if($vid){

					$rcdata = $this->run_conditionalrule($fdata,$data,$this->input->post("fieldname"),$this->input->post("contable"));
					
//					print_r($rcdata);
//					exit();
					
					$this->mongo_db->switch_db($this->mdb);
					$id = new MongoDB\BSON\ObjectID($this->input->post("conid"));
					$d = $this->mongo_db->where(array('_id'=>$id))->set($fdata)->update('tbl_conditional_rules');

				}else{

					$rcdata = $this->run_conditionalrule($fdata,$data,$this->input->post("fieldname"),$this->input->post("contable"));
					$this->mongo_db->switch_db($this->mdb);
					$fdata["id"] = $this->admin->insert_id("tbl_conditional_rules");
					$d = $this->mongo_db->insert("tbl_conditional_rules",$fdata);			

				}
				
				if($d){

					echo 'success';

				}else{

					echo 'fail';

				}

			}
			
		}else{
			
			$id = new MongoDB\BSON\ObjectID($this->input->post("conid"));
			$d = $this->mongo_db->where(array('_id'=>$id))->delete('tbl_conditional_rules');
			
			if($d){

				echo 'success';

			}else{

				echo 'fail';

			}
			
		}
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
						
						if($fd[$column] != ""){
							$uids[] = $fd["_id"]->{'$id'};	
						}
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
						
						if($fd[$column] != ""){
							$uids[] = $fd["_id"]->{'$id'};	
						}
						
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
						
						if($fd[$column] != ""){
							$uids[] = $fd["_id"]->{'$id'};	
						}	
						
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
						
						if($fd[$column] != ""){
							$uids[] = $fd["_id"]->{'$id'};	
						}
						
					}
					
				}
				
			}elseif($con == "is before" || $con == "is after"){

				$cdate = date("Y-m-d",strtotime($tdata["cond_value"][$cc]));

				if($con == "is before"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) < strtotime($cdate))){

							if($fd[$column] != ""){
								$uids[] = $fd["_id"]->{'$id'};	
							}	

						}

					}	

				}elseif($con == "is after"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) > strtotime($cdate))){

							if($fd[$column] != ""){
								$uids[] = $fd["_id"]->{'$id'};	
							}

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

							if($fd[$column] != ""){
								$uids[] = $fd["_id"]->{'$id'};	
							}	

						}

					}	

				}elseif($con == "is before today"){
					
					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) < strtotime($cdate))){

							if($fd[$column] != ""){
								$uids[] = $fd["_id"]->{'$id'};	
							}

						}

					}
					
				}elseif($con == "is today or after" || $con == "is after current time"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) >= strtotime($cdate))){

							if($fd[$column] != ""){
								$uids[] = $fd["_id"]->{'$id'};	
							}	

						}

					}

				}elseif($con == "is after today"){
					
					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getYmddate($exDate);

						if((strtotime($date) > strtotime($cdate))){

							if($fd[$column] != ""){
								$uids[] = $fd["_id"]->{'$id'};	
							}

						}

					}
					
				}

			}/*elseif($con == "higher than"){
				
				foreach($fdata as $fd){
					
					if($datatype == "number"){
						
						if(intval($fd[$column]) > intval($tdata["cond_value"][$cc])){

							$uids[] = $fd["_id"]->{'$id'};	

						}
						
					}else{
					
						if($fd[$column] > $tdata["cond_value"][$cc]){

							$uids[] = $fd["_id"]->{'$id'};	

						}
						
					}

				}
				
			}elseif($con == "lower than"){
				
				foreach($fdata as $fd){
					
					if($datatype == "number"){
						
						if(intval($fd[$column]) < intval($tdata["cond_value"][$cc])){

							$uids[] = $fd["_id"]->{'$id'};	

						}
						
					}else{
					
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
				
			}*/else{
				
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
	
	public function run_conditionalrule($cdata,$conddata,$field,$table){
		
//		return $conddata;
		
		$this->mongo_db->switch_db($this->mdb."_".$cdata['appId']);
		foreach($conddata as $key => $tdata){
			
//			print_r($tdata);
		
			if($cdata['status'] == "on"){
				
				$table = $cdata["table"];	
				$column = $cdata["field"];	

					$settings = $this->mongo_db->get_where("settings",["table"=>$table])[0];

					$columns = $settings["columns"];
					$dtypes = $settings["dataType"];

					$datatype = "";
					foreach($columns as $ck => $col){

						if($col == $column){
						
							$datatype = $dtypes[$ck];
							
						}

					}
				
				
				$sset = $tdata["ssetcondition"][0];

				if($sset == "to a field value"){

					$this->where($tdata["cond_column"],$tdata,$table);
					$finaldata = $this->mongo_db->get($table); 

					$finalIds = $this->getFinalupdateids($finaldata,$tdata["condition"],$tdata);
					$sval = explode("-",$tdata["ssetvalue"][0])[0];
					
					foreach($finalIds as $ids){

						$setvalue = $this->mongo_db->select([$sval])->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($ids)])[0];

						if($datatype == "date"){

							$conDate = $this->common->getYmddate($setvalue[$sval]);
							$update[$ids][$cdata['field']] = date("Y-m-d",strtotime($conDate));

						}elseif($datatype == "number"){
							
							$update[$ids][$cdata['field']] = ($setvalue[$sval] != "") ? intval($setvalue[$sval]) : intval();
							
						}else{

							$update[$ids][$cdata['field']] = $setvalue[$sval];

						}
						
					}

				}else{

					$this->where($tdata["cond_column"],$tdata,$table);
					$finaldata = $this->mongo_db->get($table); 

					$finalIds = $this->getFinalupdateids($finaldata,$tdata["condition"],$tdata);
					$sval = $tdata["ssetvalue"][0];

//					echo $sval;
//						print_r($tdata);
//						exit();

					foreach($finalIds as $ids){

						if($datatype == "date"){

							$update[$ids][$cdata['field']] = date("Y-m-d",strtotime($sval));

						}elseif($datatype == "number"){
							
							$update[$ids][$cdata['field']] = ($sval != "") ? intval($sval) : intval();
							
						}else{

							$update[$ids][$cdata['field']] = $sval;

						}
						
						$fromlocation = ($cdata['field'] == "flocation") ? $sval : "";
						$tolocation = ($cdata['field'] == "tlcoation") ? $sval : "";
						$spatolocation = ($cdata['field'] == "tlocation") ? $sval : "";
						$item = ($cdata['field'] == "item") ? $sval : "";
						$invlocname = ($cdata['field'] == 'locname' && $table == "tbl_inventory") ? $sval : "";

						if($fromlocation){

							$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$sval])[0];
							$update[$ids][$cdata['field']] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
							$update[$ids]["flcoationcode"] = $tlocdata["loccode"];

						}
						if($tolocation){

							$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$sval])[0];
							$update[$ids][$cdata['field']] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
							$update[$ids]["tlocationcode"] = $tlocdata["loccode"];

						}
						if($spatolocation){

							$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$sval])[0];

		//					print_r($tlocdata);
							$update[$ids][$cdata['field']] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
							$update[$ids]["tlcoationcode"] = $tlocdata["loccode"];


						}
						if($item){

							$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$sval])[0];
							$update[$ids][$cdata['field']] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];

						}
						
						if($invlocname){

							$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$sval])[0];					
							$update[$ids][$cdata['field']] = ["id"=>$tlocdata['_id']->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
							$update[$ids]["location"] = $tlocdata['locname']." - ".$tlocdata['loccode'];
							$update[$ids]["loccode"] = $tlocdata["loccode"];
							$update[$ids]["loctype"] = $tlocdata["Type"];
//							$update[$ids]["notes"] = $tlocdata["notes"];

						}

					}

				}

			}

		}
//		exit();
//		return $update;
			
			if(count($update) > 0){
				
				
				$postdata = [];
				$ik = 0;

//					print_r($update);
//					exit();

				foreach($update as $crid => $value){

//					$this->mongo_db->switch_db($this->mdb."_".$tdata['appId']);
					$postdata[] = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($crid)])[0]; 

					foreach($value as $tkey => $val){

						if($tkey == "starting_balance" || $tkey == "issues" || $tkey == "returns" || $tkey == "transfer_ins" || $tkey == "transfer_outs" || $tkey == "adjustments" || $tkey == "ending_balance" || ($tkey == "quantity" && $table == "tbl_touts") || ($tkey == "quantity" && $table == "tbl_issues") || ($tkey == "quantity" && $table == "tbl_returns") || ($tkey == "quantity" && $table == "tbl_adjustments")){

							$postdata[$ik][$tkey] = ($val != "") ? intval($val) : intval();

						}else{

							$postdata[$ik][$tkey] = $val;

						}
					}

					$ik++;

				}
				
//				return $postdata;
				
				
				$lcol = "";

				foreach($postdata as $kid => $data){
					
					unset($data["_id"]);
					unset($data["id"]);
					
					$exdata = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($postdata[$kid]["_id"]->{'$id'})])[0];
			
//					if($field == "quantity"){
						
						// update location inventory		
		
						if($table == "tbl_touts"){
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($postdata[$kid]["_id"]->{'$id'})])->set($data)->update($table);
						
							$tins = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$data["tlocationcode"],"tlocationcode",$data["item"]["item_name"],$data["quantity"],"transfer_ins",$exdata);

							$touts = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$data["flcoationcode"],"flcoationcode",$data["item"]["item_name"],$data["quantity"],"transfer_outs",$exdata);
							
//							print_r($tins);
//							print_r($touts);
							
						}elseif($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){
							
							if($table == "tbl_adjustments"){
								
								$lcol = "adjustments";
								
							}elseif($table == "tbl_issues"){
								
								$lcol = "issues";
								
							}elseif($table == "tbl_returns"){
								
								$lcol = "returns";
								
							}
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($postdata[$kid]["_id"]->{'$id'})])->set($data)->update($table);	
							
							$ff = $this->common->updateLocationinventorycount($this->database,$table,$_SESSION['appid'],$data["tlcoationcode"],"tlcoationcode",$data["item"]["item_name"],$data["quantity"],$lcol,$exdata);
							
//							print_r($data["item"]["item_name"]);
							
						}

						
				// end update location inventory
						
//					}else
					if($table == "tbl_inventory"){
						
						$loccode = $data["locname"]->loccode;
						$item = $data["item"]->item_name;

						if($exdata["locname"]->locname != $data["locname"]->locname){

							$ltdata = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$loccode,"item.item_name"=>$item])[0];
							
							$sbalance = ($ltdata["starting_balance"] + $data['starting_balance']);
							$issues = ($this->common->getInventorycount($this->database,"tbl_issues",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$returns = ($this->common->getInventorycount($this->database,"tbl_returns",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$transfer_ins = ($this->common->getInventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"tlocationcode",$item));
							$transfer_outs = ($this->common->getInventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"flcoationcode",$item));
							$adjustments = ($this->common->getInventorycount($this->database,"tbl_adjustments",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$ending_balance = ($sbalance+$issues+$returns+$transfer_ins-$transfer_outs+$adjustments);

							$data['starting_balance'] = intval($sbalance);
							$data['issues'] = intval($issues);
							$data['returns'] = intval($returns);
							$data['transfer_ins'] = intval($transfer_ins);
							$data['transfer_outs'] = intval($transfer_outs);
							$data['adjustments'] = intval($adjustments);
							$data['ending_balance'] = intval($ending_balance);
							
//							echo $sbalance;
							
							$this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($ltdata['_id']->{'$id'}))->set($data)->update($table);
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($postdata[$kid]["_id"]->{'$id'})])->delete("tbl_inventory");
							
//							print_r($val);

						}else{
							
							$sbalance = $data['starting_balance'];
							$issues = ($this->common->getInventorycount($this->database,"tbl_issues",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$returns = ($this->common->getInventorycount($this->database,"tbl_returns",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$transfer_ins = ($this->common->getInventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"tlocationcode",$item));
							$transfer_outs = ($this->common->getInventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"flcoationcode",$item));
							$adjustments = ($this->common->getInventorycount($this->database,"tbl_adjustments",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
							$ending_balance = ($sbalance+$issues+$returns+$transfer_ins-$transfer_outs+$adjustments);

							$data['starting_balance'] = intval($sbalance);
							$data['issues'] = intval($issues);
							$data['returns'] = intval($returns);
							$data['transfer_ins'] = intval($transfer_ins);
							$data['transfer_outs'] = intval($transfer_outs);
							$data['adjustments'] = intval($adjustments);
							$data['ending_balance'] = intval($ending_balance);	
							
							
//							return $data;
							$this->mongo_db->where('_id',new MongoDB\BSON\ObjectID($postdata[$kid]["_id"]->{'$id'}))->set($data)->update($table);
							
						}
					}else{
						
						$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($postdata[$kid]["_id"]->{'$id'})])->set($data)->update($table);
						
					}
									
					
//					return $ff." ";
//					exit();

					$ulocdata = $this->mongo_db->get_where($table,["_id"=>new MongoDB\BSON\ObjectID($postdata[$kid]["_id"]->{'$id'})])[0];
					
					if($table == "tbl_locations"){
						
						if(($ulocdata['locname'] != $exdata['locname']) || ($ulocdata['loccode'] != $exdata["loccode"]) || ($ulocdata['status'] != $exdata["status"]) || ($ulocdata['notes'] != $exdata["notes"]) || ($ulocdata['Type'] != $exdata["Type"])){				

							$udata = ["id"=>$exdata["_id"]->{'$id'},"previous_name"=>$exdata['locname'],"new_name"=>$ulocdata['locname'],"code"=>$ulocdata['loccode'],"status"=>$ulocdata['status'],"notes"=>$ulocdata['notes'],"loctype"=>$ulocdata['Type'],"appId"=>$cdata['appId']];
							$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);

						}

					}

					if($table == "tbl_items"){

						if(($ulocdata['item_name'] != $exdata['item_name']) || ($ulocdata['item_code'] != $exdata['item_code']) || ($ulocdata['status'] != $exdata['status'])){

							$udata = ["id"=>$exdata["_id"]->{'$id'},"previous_name"=>$exdata["item_name"],"new_name"=>$ulocdata['item_name'],"code"=>$ulocdata['item_code'],"status"=>$ulocdata['status'],"appId"=>$cdata['appId']];
							$this->admin->mongoInsert("$this->mdb.tbl_locations_updated",$udata);

						}

					}					

				}
			}
			
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
			
			$start = date("Y-01-01");
			$finish = date("Y-12-31");
			
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
	

}
