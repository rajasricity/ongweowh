<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Filter extends CI_Controller {
	
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
	
		$_SESSION['appid'] = '';
		$this->load->view('admin/apps/allApps');
		
	}
	
   public function addFilter(){	
        //echo '<pre>';print_r($_POST);exit;
        $cause = $this->input->post('cause');
		$field = $this->input->post('field');
		$condition = $this->input->post('value');
		$value = $this->input->post('svalue');
		$appid = $this->input->post('id');
		$table = $this->input->post('table');
		
		$cdays = [];
        $i = 0;
		
					foreach($condition as $kk => $val){
                       
						if($val == "is during the previous" || $val == "is before the previous" || $val == "is during the next"){
							
							$cdays[] = $this->input->post("dvalue")[$i];
							$i++;

						}else{

							$cdays[] = "";

						}

					}//exit;
		$dvalue=$cdays;
		//echo '<pre>';print_r($dvalue);exit;
		$this->mongo_db->switch_db($this->database);
		
		$this->where($cause,$field,$condition,$value);
		
		$finaldata = $this->mongo_db->get($table); 
		//echo '<pre>';print_r($finaldata);exit; 
		if(in_array("or",$cause)){
			$finaldata_or = $this->mongo_db->get($table);			
			$finalIds = $this->getFinalupdateids_or($finaldata_or,$field,$condition,$value,$dvalue);
		}else{
			$finalIds = $this->getFinalupdateids($finaldata,$field,$condition,$value,$dvalue);
		} 
		//
		 
		if(count($finalIds) > 0){
			foreach($finalIds as $f1){
			   $fres[] = new MongoDB\BSON\ObjectID($f1);
		    } 
		
		}else{
			$fres[] = "";
		}
		//echo '<pre>';print_r($fres);exit;
		$this->mongo_db->where_in("_id", $fres);
	    $result = $this->mongo_db->get($table);
		
		
		if(in_array("or",$cause)){
			$final_res = array_merge($finaldata,$result);
		}else{
			$final_res = $result;
		} 
		
	    
	    
	
	    
		
	  //echo '<pre>';print_r($result);exit;
	 
	
		
		$out = $this->common->getFiltervalues($final_res,$table);
		
		
	    
		$results = ["sEcho" => 1,"iTotalRecords" => count($out),"iTotalDisplayRecords" => count($out),"aaData" => $out];
		echo json_encode($results); 	
		
	}
	
	
	
	public function where($cause,$wheres,$condition,$value){
		/* echo '<pre>';print_r($wheres);
		 echo '<pre>';print_r($condition);
		 echo '<pre>';print_r($value);
		exit; */ 
		
		$where = [];
		if(in_array("or",$cause)){
				 $res = true;
			 }else{
				 $res = false;
			 }
		foreach($wheres as $kk => $wh){
			
			if($cause[$kk] == "where" || $cause[$kk] == "and"){
				
				$set = explode("-",$wh);
				if($condition[$kk] == "contains"){
                    if($res){
						$this->mongo_db->where_or([$set[0]=>new MongoDB\BSON\Regex($value[$kk],'i')]);
						
					}else{
						$this->mongo_db->like($set[0],$value[$kk]);
					}
					

				}elseif($condition[$kk] == "is"){
				
					if($set[1] == "date"){
						//var_dump($res);
						if($res){
							
							$this->mongo_db->where_or([$set[0]=>new MongoDB\BSON\Regex(date("m-d-Y",strtotime($value[$kk])),'i')]);
							
						}else{
							$this->mongo_db->like($set[0],date("m-d-Y",strtotime($value[$kk])));
						}
						
						
					}else{
					    if($res){
							$this->mongo_db->where_or([$set[0]=>$value[$kk]]);
							
						}else{
							$this->mongo_db->where([$set[0]=>$value[$kk]]);
						}
						
					
					}
					
				}elseif($condition[$kk] == "does not contain"){

					$this->mongo_db->where_not_in($set[0],[new MongoDB\BSON\Regex($value[$kk],'i')]);

				}elseif($condition[$kk] == "is not"){
					
					if($set[1] == "date"){
						
						$this->mongo_db->where_not_in($set[0],[new MongoDB\BSON\Regex(date("m-d-Y",strtotime($value[$kk])),'i')]);
						
					}else{
					
						$this->mongo_db->where_ne($set[0],$value[$kk]);				
						
					}
									
				}elseif($condition[$kk] == "starts with"){

					$this->mongo_db->like($set[0],$value[$kk],"i","^",TRUE);

				}elseif($condition[$kk] == "ends with"){

					$this->mongo_db->like($set[0],$value[$kk],"i",TRUE,"$");

				}elseif($condition[$kk] == "is not blank"){

					$this->mongo_db->where_ne($set[0],"");

				}elseif($condition[$kk] == "is today"){

					$date = date("m-d-Y");

					$this->mongo_db->like($set[0],$date);	

				}
				
			}
			elseif($cause[$kk] == "where" || $cause[$kk] == "or"){
				
				$set = explode("-",$wh);
				if($condition[$kk] == "contains"){

					 $this->mongo_db->where_or([$set[0]=>new MongoDB\BSON\Regex($value[$kk],'i')]);

				}elseif($condition[$kk] == "is"){
					
					if($set[1] == "date"){
						
						$this->mongo_db->where_or([$set[0]=>new MongoDB\BSON\Regex(date("m-d-Y",strtotime($value[$kk])),'i')]);
					}else{
					
						$this->mongo_db->where_or([$set[0]=>$value[$kk]]);
					
					}
					
				}elseif($condition[$kk] == "does not contain"){

					$this->mongo_db->where_not_in($set[0],[new MongoDB\BSON\Regex($value[$kk],'i')]);

				}elseif($condition[$kk] == "is not"){
					
					if($set[1] == "date"){
						
						$this->mongo_db->where_not_in($set[0],[new MongoDB\BSON\Regex(date("m-d-Y",strtotime($value[$kk])),'i')]);
						
					}else{
					
						$this->mongo_db->where_ne($set[0],$value[$kk]);				
						
					}
									
				}elseif($condition[$kk] == "starts with"){

					$this->mongo_db->like($set[0],$value[$kk],"i","^",TRUE);

				}elseif($condition[$kk] == "ends with"){

					$this->mongo_db->like($set[0],$value[$kk],"i",TRUE,"$");

				}elseif($condition[$kk] == "is not blank"){

					$this->mongo_db->where_ne($set[0],"");

				}elseif($condition[$kk] == "is today"){

					$date = date("m-d-Y");

					$this->mongo_db->like($set[0],$date);	

				}
				
			}

			

		}
		
	}	
    public function getFinalupdateids($fdata,$field,$conditions,$value,$dvalue){
	
		/* echo '<pre>';print_r($conditions);
		echo '<pre>';print_r($value);
		echo '<pre>';print_r($dvalue);
		exit; */
		
		$uids = [];
		$wids = [];
		
		foreach($conditions as $cc => $con){

			$column = explode("-",$field[$cc])[0];
			
			if($con == "is during the current"){
			
				$sldates = $this->getDays($value[$cc]);
				$start = $sldates["astart"];
				$end = $sldates["aend"];
				//echo $start;echo '<br>';echo $end;exit; 
				$column = explode("-",$field[$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getConverteddate($exDate);
					
					if(strtotime($date) >= strtotime($start) && (strtotime($date) <= strtotime($end))){
						
						$uids[] = new MongoDB\BSON\ObjectID($fd["_id"]->{'$id'});	
						
					}
					
				}
				
				
			}elseif($con == "is during the previous"){
              
				$dates = $this->getDayscount($dvalue[$cc],"minus",$value[$cc]);
				$start = $dates["astart"];
				$end = $dates["aend"];
				$column = explode("-",$field[$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getConverteddate($exDate);
					
					if(strtotime($date) >= strtotime($start) && (strtotime($date) <= strtotime($end))){
						
						$uids[] = $fd["_id"]->{'$id'};	
						
					}
					
				}
				//echo '<pre>';print_r($uids);exit;
			}elseif($con == "is before the previous"){

				$dates = $this->getDayscount($dvalue[$cc],"minus",$value[$cc]);
				$start = $dates["astart"];
				$end = $dates["aend"];
               
				$column = explode("-",$field[$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getConverteddate($exDate);
					$beforeDate = date('Y-m-d', strtotime('-1 day', strtotime($date)));
					
					if((strtotime($date) < strtotime($start))){
						
						$uids[] = $fd["_id"]->{'$id'};	
						
					}
					
				}
			}elseif($con == "is during the next" || $con == "is after the next"){
				
				$dates = $this->getDayscount($dvalue[$cc],"plus",$value[$cc]);
				$start = $dates["astart"];
				$end = $dates["aend"];
                
				$column = explode("-",$field[$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getConverteddate($exDate);
					
					if(strtotime($date) >= strtotime($start) && (strtotime($date) <= strtotime($end))){
						
						$uids[] = $fd["_id"]->{'$id'};	
						
					}
					
				}
			}elseif($con == "is before" || $con == "is after"){

				$cdate = date("Y-m-d",strtotime($value[$cc]));

				if($con == "is before"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getConverteddate($exDate);

						if((strtotime($date) < strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}	

				}elseif($con == "is after"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getConverteddate($exDate);

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
						$date = $this->common->getConverteddate($exDate);

						if((strtotime($date) <= strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}	

				}elseif($con == "is before today"){
					
					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getConverteddate($exDate);

						if((strtotime($date) < strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
					
				}elseif($con == "is today or after" || $con == "is after current time"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getConverteddate($exDate);

						if((strtotime($date) >= strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}

				}elseif($con == "is after today"){
					
					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getConverteddate($exDate);

						if((strtotime($date) > strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
					
				}

			}elseif($con == "higher than"){
				
				foreach($fdata as $fd){
					
					if($fd[$column] > $value[$cc]){

						$uids[] = $fd["_id"]->{'$id'};	

					}

				}
				
			}elseif($con == "lower than"){
				
				foreach($fdata as $fd){
					
					if($fd[$column] < $value[$cc]){

						$uids[] = $fd["_id"]->{'$id'};	

					}

				}
				
			}elseif($con == "is blank"){
				
				foreach($fdata as $fd){
					
					if($fd[$column] == "" || $fd[$column] == " "){

						$uids[] = $fd["_id"]->{'$id'};	

					}

				}
				
			}else{
				
				if(((count($uids) <= 0) || $uids == "") && in_array("is blank",$conditions)){

				}else{
					foreach($fdata as $fd){
	
					$wids[] = $fd["_id"]->{'$id'};	
					
				    }
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
	public function getFinalupdateids_or($fdata,$field,$conditions,$value,$dvalue){
		
		/* echo '<pre>';print_r($conditions);
		echo '<pre>';print_r($value);
		echo '<pre>';print_r($dvalue);
		exit; */
		
		$uids = [];
		$wids = [];
		
		foreach($conditions as $cc => $con){

			$column = explode("-",$field[$cc])[0];
			
			if($con == "is during the current"){
			
				$sldates = $this->getDays($value[$cc]);
				$start = $sldates["astart"];
				$end = $sldates["aend"];
				//echo $start;echo '<br>';echo $end;exit; 
				$column = explode("-",$field[$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getConverteddate($exDate);
					
					if(strtotime($date) >= strtotime($start) && (strtotime($date) <= strtotime($end))){
						
						$uids[] = new MongoDB\BSON\ObjectID($fd["_id"]->{'$id'});	
						
					}
					
				}
				
				
			}elseif($con == "is during the previous"){
            
				$dates = $this->getDayscount($dvalue[$cc],"minus",$value[$cc]);
				$start = $dates["astart"];
				$end = $dates["aend"];
				$column = explode("-",$field[$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getConverteddate($exDate);
					
					if(strtotime($date) >= strtotime($start) && (strtotime($date) <= strtotime($end))){
						
						$uids[] = $fd["_id"]->{'$id'};	
						
					}
					
				}
				//echo '<pre>';print_r($uids);exit;
			}elseif($con == "is before the previous"){

				$dates = $this->getDayscount($dvalue[$cc],"minus",$value[$cc]);
				$start = $dates["astart"];
				$end = $dates["aend"];
               
				$column = explode("-",$field[$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getConverteddate($exDate);
					$beforeDate = date('Y-m-d', strtotime('-1 day', strtotime($date)));
					
					if((strtotime($date) < strtotime($start))){
						
						$uids[] = $fd["_id"]->{'$id'};	
						
					}
					
				}
			}elseif($con == "is during the next" || $con == "is after the next"){
				
				$dates = $this->getDayscount($dvalue[$cc],"plus",$value[$cc]);
				$start = $dates["astart"];
				$end = $dates["aend"];

				$column = explode("-",$field[$cc])[0];
				
				foreach($fdata as $fd){
					
					$exDate = explode(" ",$fd[$column])[0];
					$date = $this->common->getConverteddate($exDate);
					
					if(strtotime($date) >= strtotime($start) && (strtotime($date) <= strtotime($end))){
						
						$uids[] = $fd["_id"]->{'$id'};	
						
					}
					
				}
				
			}elseif($con == "is before" || $con == "is after"){

				$cdate = date("Y-m-d",strtotime($value[$cc]));

				if($con == "is before"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getConverteddate($exDate);

						if((strtotime($date) < strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}	

				}elseif($con == "is after"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getConverteddate($exDate);

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
						$date = $this->common->getConverteddate($exDate);

						if((strtotime($date) <= strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}	

				}elseif($con == "is before today"){
					
					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getConverteddate($exDate);

						if((strtotime($date) < strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
					
				}elseif($con == "is today or after" || $con == "is after current time"){

					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getConverteddate($exDate);

						if((strtotime($date) >= strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}

				}elseif($con == "is after today"){
					
					foreach($fdata as $fd){
					
						$exDate = explode(" ",$fd[$column])[0];
						$date = $this->common->getConverteddate($exDate);

						if((strtotime($date) > strtotime($cdate))){

							$uids[] = $fd["_id"]->{'$id'};	

						}

					}
					
				}

			}elseif($con == "higher than"){
				
				foreach($fdata as $fd){
					
					if($fd[$column] > $value[$cc]){

						$uids[] = $fd["_id"]->{'$id'};	

					}

				}
				
			}elseif($con == "lower than"){
				
				foreach($fdata as $fd){
					
					if($fd[$column] < $value[$cc]){

						$uids[] = $fd["_id"]->{'$id'};	

					}

				}
				
			}elseif($con == "is blank"){
				
				foreach($fdata as $fd){
					
					if($fd[$column] == "" || $fd[$column] == " "){

						$uids[] = $fd["_id"]->{'$id'};	

					}

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
	public function getDayscount($selection,$condition,$count){
	
		$data = [];
		
		$incdec = ($condition == "plus") ? "+" : "-";
		
		if($selection == "days"){
			
			$days = ($count == 1) ? "day" : "days";
		
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("$incdec$count $days"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$astart = date('Y-m-d');
			$afinish = date('Y-m-d',strtotime("$incdec$count $days"));

			$asdate = ($condition == "plus") ? $astart : $afinish;
			$aedate = ($condition == "plus") ? $afinish : $astart;
			
			$data = array("start"=>$sdate,"end"=>$edate,"astart"=>$asdate,"aend"=>$aedate);
			
		}elseif($selection == "weeks"){
			
			$weeks = ($count == 1) ? "week" : "weeks";
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("$incdec$count $weeks"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$astart = date('Y-m-d');
			$afinish = date('Y-m-d',strtotime("$incdec$count $weeks"));

			$asdate = ($condition == "plus") ? $astart : $afinish;
			$aedate = ($condition == "plus") ? $afinish : $astart;

			$data = array("start"=>$sdate,"end"=>$edate,"astart"=>$asdate,"aend"=>$aedate);
			
		}elseif($selection == "months"){
			
			$months = ($count == 1) ? "month" : "months";
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("$incdec$count $months"));

			$sdate = ($condition == "plus") ? $start : $finish;
			$edate = ($condition == "plus") ? $finish : $start;
			
			$astart = date('Y-m-d');
			$afinish = date('Y-m-d',strtotime("$incdec$count $months"));

			$asdate = ($condition == "plus") ? $astart : $afinish;
			$aedate = ($condition == "plus") ? $afinish : $astart;
			
			$data = array("start"=>$sdate,"end"=>$edate,"astart"=>$asdate,"aend"=>$aedate);
			
		}elseif($selection == "years" || $selection == "rolling years"){
			
			$years = ($count == 1) ? "year" : "years";
			$start = date('m-d-Y');
			$finish = date('m-d-Y',strtotime("$incdec$count $years"));

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
	public function getDays($condition){
	
		if($condition == "week"){

			$signupdate=date("Y-m-d");
			$signupweek=date("W",strtotime($signupdate));
			$year=date("Y",strtotime($signupdate));
			$currentweek = date("W");

			$dto = new DateTime();
			$start = $dto->setISODate($year, $signupweek, 0)->format('m-d-Y');
			$finish = $dto->setISODate($year, $signupweek, 6)->format('m-d-Y');

			$astart = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
			$afinish = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
			
			$data = array("start"=>$start,"end"=>$finish,"astart"=>$astart,"aend"=>$afinish);
			
		}elseif($condition == "month"){
			
			$start = date("m-d-Y", strtotime("first day of this month"));
			$finish = date("m-d-Y", strtotime("last day of this month"));

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

			$start = date("m-d-Y",strtotime($start_date));
			$finish = date("m-d-Y",strtotime($end_date));

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
}
