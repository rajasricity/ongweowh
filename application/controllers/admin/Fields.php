<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Fields extends CI_Controller {

	public function __construct(){
		
		parent::__construct();
		
		/*if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}*/
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
		$this->mdb = mongodb;
		
		
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
	
	public function createFields(){
		
		$rCount = $this->input->post("rulesCount");

		$data = [];	
		
		$status = $this->input->post("validationrule");
		
		if($status == "on"){
		
			if(count($rCount) > 0){

				foreach($rCount as $rc){

					$cval = $this->input->post("cond_value$rc");

					$cdays = [];

					$i = 0;
					foreach($cval as $kk => $cvalue){

						if($cvalue == "days" || $cvalue == "weeks" || $cvalue == "months" || $cvalue == "years" || $cvalue == "rolling years"){

							$cdays[] = $this->input->post("cond_days$rc")[$i];
							$i++;

						}else{

							$cdays[] = "";

						}

					}


					$data[] = array(
								"cond_column"=>$this->input->post("cond_column$rc"),
								"condition"=>$this->input->post("condition$rc"),
								"cond_value"=>$this->input->post("cond_value$rc"),
								"alertMessage"=>$this->input->post("alertMessage$rc"),
								"cond_days"=>$cdays
							  ); 

				}

				$fdata = array(

					"table" =>$this->input->post("vrtable"),
					"field" =>$this->input->post("fieldname"),
					"status" =>$this->input->post("validationrule"),
					"conditions" =>$data,
					"appId" => $_SESSION['appid']

				);

				$vid = $this->input->post("vid");

				if($vid){

					$id = new MongoDB\BSON\ObjectID($this->input->post("vid"));

					$d = $this->mongo_db->where(array('_id'=>$id))->set($fdata)->update('tbl_validation_rules');

				}else{
					
					$fdata["id"] = $this->admin->insert_id("tbl_validation_rules");
					$d = $this->mongo_db->insert("tbl_validation_rules",$fdata);			

				}

				if($d){

					echo 'success';

				}else{

					echo 'fail';

				}

			}
			
		}else{
			
			/*$data = array(
							"cond_column"=>[],
							"condition"=>[],
							"cond_value"=>[],
							"alertMessage"=>[],
							"cond_days"=>[]
						  ); 
			
		
			$fdata = array(

				"table" =>$this->input->post("vrtable"),
				"field" =>$this->input->post("fieldname"),
				"status" =>$this->input->post("validationrule"),
				"conditions" =>$data,
				"appId" => $_SESSION['appid']

			);

			$vid = $this->input->post("vid");

			if($vid){

				$id = new MongoDB\BSON\ObjectID($this->input->post("vid"));
				$d = $this->mongo_db->where(array('_id'=>$id))->set($fdata)->update('tbl_validation_rules');

			}*/
				
			$id = new MongoDB\BSON\ObjectID($this->input->post("vid"));
			$d = $this->mongo_db->where(array('_id'=>$id))->delete('tbl_validation_rules');
			
			if($d){

				echo 'success';

			}else{

				echo 'fail';

			}
			
//				echo json_encode(['status'=>'error','msg'=>'please select validati']);
			
		}
	}
	
	public function editField(){
		
//		$this->mongo_db->switch_db($this->database);
		
		$vrid = $this->input->post("vrid");
		
		$vrdata = $this->mongo_db->get_where("tbl_validation_rules",array("_id"=>new MongoDB\BSON\ObjectID($vrid)))[0];
		$table = $vrdata["table"];
		
		$this->mongo_db->switch_db($this->database);
		$lcolumns = $this->mongo_db->get_where("settings",["table"=>$table])[0];
		
		$cond_column = '';
		
		if(count($vrdata['conditions']) > 0){
			
			$opid = 1;
			$deleterule = (count($vrdata['conditions']) == 1) ? "deleteallRules" : "deleteRule";
			
			foreach($vrdata['conditions'] as  $tdata){
				
//			$tdata = $vrdata['conditions'];
					
				$rkey=random_string("alnum",10);

				
				$cond_column .= '<div class="row delSelRule'.$rkey.'" style="background-color: #ccc;padding: 12px;margin: 5px;border-radius: 5px;"><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 16px;padding:0px;"><label>When</label></div><div class="col-md-10" style="background-color: #eee;">';
					
				$fid = 0;
				
				foreach($tdata->cond_column as $key1 => $coc){
					
					$delete = "";

					$refkey=random_string("alnum",10);				
					$dataType = explode("-",$coc);

					$coldata = $this->common->getUpdatedFieldsOperatorsUpdated($dataType[0],$dataType[1],$table,$tdata->condition[$key1],$tdata->cond_value[$key1],'getopAruleval'.$refkey.'',"$rkey");

					$fields = $coldata['fields'];
					$operators = $coldata['operators'];
	
						$cond_column .= '<div class="row removeLabel'.$refkey.'" style="padding: 10px;margin-bottom: -10px;"><div class="col-md-4"><div class="form-group"><select name="cond_column'.$rkey.'[]" class="form-control changeLabelArule" rid="refvalLabels'.$refkey.'" rCount="'.$rkey.'" uopid="getopAruleval'.$refkey.'">';
														   foreach($lcolumns['labels'] as $key => $labels){

															   $lsel = (($lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key]) == $coc) ? 'selected' : '';

															   $cond_column .= '<option value="'.$lcolumns['columns'][$key]."-".$lcolumns['dataType'][$key].'" '.$lsel.'>'.$labels.'</option>';

														   }

													$cond_column .=	'</select>

													</div></div><div class="col-md-3"><div class="form-group oprefvalLabels'.$refkey.'">'.$operators.'</div>
												</div>

												<div class="col-md-3"><div class="form-group refvalLabels'.$refkey.' getopAruleval'.$refkey.' vrulesConditionValue">';
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

													}elseif($tdata->condition[$key1] == "is today" || $tdata->condition[$key1] == "is today or before" || $tdata->condition[$key1] == "is today or after" || $tdata->condition[$key1] == "is before today" || $tdata->condition[$key1] == "is after today" || $tdata->condition[$key1] == "is before current time" || $tdata->condition[$key1] == "is after current time" || $tdata->condition[$key1] == "is blank" || $tdata->condition[$key1] == "is not blank"){

														$cond_column .= '<div class="row"><div class="col-md-4" style="padding:0px"></div></div>';

													}else{

														$cond_column .= $fields;

													}	

					
													if($fid != 0){
														
														$delete = '<i class="fa fa-times-circle remVlabels" lid="removeLabel'.$refkey.'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i>&nbsp;';
														
													}
					
													$cond_column .= '</div>
												</div><div class="col-md-2" align="right">'.$delete.'<i class="fa fa-plus-circle addWhencondArule" crid="addedWhencondArule'.$rkey.'" rCount="'.$rkey.'" style="color: green; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div></div>';
					++$fid;
					
				}
											$cond_column .=	'<div class="addedWhencondArule'.$rkey.'"></div></div><div class="col-md-1" align="right"><i class="fa fa-times-circle '.$deleterule.'" delRule="delSelRule'.$rkey.'" style="color: red; margin-top: 5px; font-size: x-large;cursor: pointer"></i></div><div class="col-md-1" align="center" style="margin-top: 5px;font-size: 15px;padding:0px;"><label>Message</label></div><div class="col-md-10" style="background-color: #eee;"><textarea rows="6" cols="10" class="form-control" name="alertMessage'.$rkey.'[]" style="margin:10px">'.$tdata->alertMessage[0].'</textarea><input type="hidden" name="rulesCount[]" value="'.$rkey.'"></div></div>';

					++$opid;
				}
			
			
		}else{
			
//			$cond_column = '<div class="col-md-2" align="right" style="margin-top: 5px;font-size: 18px"><label>When</label></div><div class="col-md-4" style="margin-top: 5px;"><p>Every Record. <a href="javascript:void(0)" class="elwhenCondition"><strong style="font-size: 18px">add criteria</strong></a></p></div>';
			
		} 
		
		echo json_encode(["status"=>$vrdata["status"],"cond_data"=>$cond_column]);
		
	}
			
	public function where($wheres,$tdata){
		
		$where = [];
		foreach($wheres as $kk => $wh){

			$set = explode("-",$wh);

	// where condition

			if($tdata["condition"][$kk] == "contains" || $tdata["condition"][$kk] == "is"){

				$this->mongo_db->where($set[0],$tdata["cond_value"][$kk]);

			}elseif($tdata["condition"][$kk] == "does not contain" || $tdata["condition"][$kk] == "is not"){

				$this->mongo_db->where_ne($set[0],$tdata["cond_value"][$kk]);

			}elseif($tdata["condition"][$kk] == "starts with"){

				$this->mongo_db->like($set[0],$tdata["cond_value"][$kk],"i","^",TRUE);

			}elseif($tdata["condition"][$kk] == "ends with"){

				$this->mongo_db->like($set[0],$tdata["cond_value"][$kk],"i",TRUE,"$");

			}elseif($tdata["condition"][$kk] == "is blank"){

				$this->mongo_db->where([$set[0]=>""]);

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
					$this->mongo_db->where_gte($set[0],$start)->where_lte($set[0],$end);

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

					$this->mongo_db->where_lt($set[0],$date);	

				}elseif($tdata["condition"][$kk] == "is after"){

					$this->mongo_db->where_gt($set[0],$date);

				}

			}elseif($tdata["condition"][$kk] == "is today or before" || $tdata["condition"][$kk] == "is today or after" || $tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time" || $tdata["condition"][$kk] == "is before current time"){

				$date = date("Y-m-d");

				if($tdata["condition"][$kk] == "is today or before" || $tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is before current time"){

					$this->mongo_db->where_lt($set[0],$date);	

				}elseif($tdata["condition"][$kk] == "is today or after" || $tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time"){

					$this->mongo_db->where_gt($set[0],$date);

				}

			}elseif($tdata["condition"][$kk] == "is today"){

				$date = date("Y-m-d");

				$this->mongo_db->like($set[0],$date);	

			}

		}
		
	}
	
	public function getDays($condition){
	
		if($condition == "week"){
		
//			$start = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
//			$finish = (date('D') != 'Sun') ? date('Y-m-d', strtotime('next Sunday')) : date('Y-m-d');

			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("+7 days"));

			$data = array("start"=>$start,"end"=>$finish);
			
		}elseif($condition == "month"){
			
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("+30 days"));

			$data = array("start"=>$start,"end"=>$finish);
			
		}elseif($condition == "quarter"){
			
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("+3 months"));

			$data = array("start"=>$start,"end"=>$finish);
			
		}elseif($condition == "year"){
			
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("+1 year"));

			$data = array("start"=>$start,"end"=>$finish);
			
		}
		
		return $data;
		
	}
	
	public function getDayscount($selection,$condition,$count){
	
		$incdec = ($condition == "plus") ? "+" : "-";
		
		if($selection == "days"){
			
			$days = ($count == 1) ? "day" : "days";
		
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("$incdec$count $days"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$data = array("start"=>$sdate,"end"=>$edate);
			
		}elseif($selection == "weeks"){
			
			$weeks = ($count == 1) ? "week" : "weeks";
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("$incdec$count $weeks"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$data = array("start"=>$sdate,"end"=>$edate);
			
		}elseif($selection == "months"){
			
			$months = ($count == 1) ? "month" : "months";
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("$incdec$count $months"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$data = array("start"=>$sdate,"end"=>$edate);
			
		}elseif($selection == "years" || $selection == "rolling years"){
			
			$years = ($count == 1) ? "year" : "years";
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("$incdec$count $years"));

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
	
	
}
