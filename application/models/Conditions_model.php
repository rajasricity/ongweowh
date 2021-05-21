<?php

defined("BASEPATH") OR exit("No direct script access allow");


class Conditions_model extends CI_Model{

	public function __construct(){
		
		parent::__construct();
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
		$this->mdb = mongodb;
		
	}
	
	public function checkConditionrules($table,$fields,$appid,$fval=""){
		
		$this->mongo_db->switch_db($this->mdb);
		
		$vrdata = $this->mongo_db->get_where("tbl_conditional_rules",array("table"=>$table,"appId"=>$appid));
		
//		return($vrdata);
		
		if(count($vrdata) > 0){
			
			$lcolumns = $this->admin->getRow("",["table"=>"$table"],[],$this->admin->getAppdb().".settings");
			
			$flag = $fields['flag'];
			
			
			$fdata = [];
			foreach($vrdata as $vr){
			
				$dataType = "";
				
				foreach($lcolumns->labels as $key => $labels){
					
					if($vr['field'] == $lcolumns->columns[$key]){
						
						$dataType = $lcolumns->dataType[$key];
						
					}
				
				} 
				
				if($vr["status"] == "on"){
					
//					if($fields[$vr['field']]){

						$conditions = json_decode(json_encode($vr['conditions']),true);
						$cdata = [];		
						foreach($conditions as $tdata){
//                            if(count($tdata["cond_column"]) > '1'){
//								$cdata[] = implode("||",$this->where($tdata["cond_column"],$tdata,$fields,$vr['field'],$flag));
//							}else{
								$cdata[] = implode("&&",$this->where($tdata["cond_column"],$tdata,$fields,$vr['field'],$flag));
//							}
							//$cdata[] = implode("&&",$this->where($tdata["cond_column"],$tdata,$fields,$vr['field']));

						}
                        //echo '<pre>';print_r($cdata);exit;
						foreach($cdata as $k => $cval){

//							return $cval;
							$cond = eval("return $cval;");

							if($cond){
								
								if($conditions[$k]["ssetcondition"][0] == "to a field value"){
									
									if($dataType == "date"){
										
										$fdata[] = ["column"=>$vr['field'],"value"=>date("Y-m-d",strtotime($fields[explode("-",$conditions[$k]["ssetvalue"][0])[0]]))];	
										
									}if($dataType == "number"){
										
										$fdata[] = ["column"=>$vr['field'],"value"=>intval($fields[explode("-",$conditions[$k]["ssetvalue"][0])[0]])];	
										
									}else{
										
										$fdata[] = ["column"=>$vr['field'],"value"=>$fields[explode("-",$conditions[$k]["ssetvalue"][0])[0]]];
										
									}
									
								}else{
									
									if($dataType == "date"){
										
										$fdata[] = ["column"=>$vr['field'],"value"=>date("Y-m-d",strtotime($conditions[$k]["ssetvalue"][0]))];
									
									}if($dataType == "number"){
										
										$fdata[] = ["column"=>$vr['field'],"value"=>intval($conditions[$k]["ssetvalue"][0])];	
										
									}else{
										
										$fdata[] = ["column"=>$vr['field'],"value"=>$conditions[$k]["ssetvalue"][0]];
										
									}
										
								}

	//							return $cval;

							}
							
						}

//					}
				
				}
			}
			
			$this->mongo_db->switch_db($this->database);
			
// return all data
			
			return $fdata;
			
		}
		
	}
	
	public function where($wheres,$tdata,$fValue,$column,$flag="empty"){
		
		$where = [];
		$ii = 0;
		
		$cond_value = "";
		$value = "";
		
		foreach($wheres as $kk => $wh){
			
			$bool = "false";

			$column = explode("-",$wh)[0];
			$datatype = explode("-",$wh)[1];
            $field_val = $this->common->getConverteddate($fValue[$column]);
	// where condition

			if($tdata["condition"][$kk] == "contains"){

				if($tdata["cond_column"][$kk] == "accounts-multiselect"){
					$boo = "";
					foreach($fValue[$column] as $f_one){
						$value = strtolower($f_one);
						$cond_value = strtolower($tdata["cond_value"][$kk]);
						if(strpos($value, $cond_value) !== false){

						$boo = "true";
						

						}else{

							$boo = "false";
							

						}
						$bools[] = $boo;
						 
						
					}
						if(in_array("true",$bools)){
							$bool = "true";
						}else{
							$bool = "false";
						}
                   
				}else{
					  $cond_value = strtolower($tdata["cond_value"][$kk]);
				      $value = strtolower($fValue[$column]);
				    if(strpos($value, $cond_value) !== false){

						$bool = "true";
						

					}else{

						$bool = "false";
						

					}
				}
                   
				$where[] = '('.$bool.')';
					
//				}

			}elseif($tdata["condition"][$kk] == "is"){
//				    if($flag == "excel" && $tdata["cond_column"][$kk] == "import_date-date"){
//						$where[] = '('.strtotime($field_val) .' == '. strtotime($tdata["cond_value"][$kk]) .')';
//					}else
					if($datatype == "date"){
						
						if($flag == "excel"){
							
							if(strtotime($field_val) == strtotime($tdata["cond_value"][$kk])){
								
								$bool = "true";
								
							}else{
								
								$bool = "false";
								
							}
							
							$where[] = $bool;
						}else{
							
							if(strtotime($fValue[$column]) == strtotime($tdata["cond_value"][$kk])){
								
								$bool = "true";
								
							}else{
								
								$bool = "false";
								
							}
							
							$where[] = $bool;
						}
						
					}elseif($datatype == "number"){
						
						if(intval($fValue[$column]) == intval($tdata["cond_value"][$kk])){
								
							$bool = "true";

						}else{

							$bool = "false";

						}
						
						$where[] = $bool;
						
					}else{
						if($tdata["cond_column"][$kk] == "accounts-multiselect"){
							
							if (in_array($tdata["cond_value"][$kk], $fValue[$column]))
							  {
								  $bool = "true";
							  }
							else
							  {
								   $bool = "false";
							  }
							  
							$where[] = '('.$bool.')';  
							  
						}else{
							
							if($fValue[$column] == $tdata["cond_value"][$kk]){
								
								$bool = "true";
								
							}else{
								
								$bool = "false";
								
							}
							
							$where[] = $bool;
							
//							$where[] = '('.'"'.$fValue[$column] .'"'.' == '. '"'.$tdata["cond_value"][$kk].'"'.')';
						}
					}
					
				
			}elseif($tdata["condition"][$kk] == "does not contain"){

				if($tdata["cond_column"][$kk] == "accounts-multiselect"){
					$boo = "";
					foreach($fValue[$column] as $f_two){
						$value = strtolower($f_two);
						$cond_value = strtolower($tdata["cond_value"][$kk]);
						if(strpos($value, $cond_value) !== false){

						$boo = "false";
						

						}else{

							$boo = "true";
							

						}
						$bools[] = $boo;
						 
						
					}
						if(in_array("true",$bools)){
							$bool = "true";
						}else{
							$bool = "false";
						}
				}
				else{
					$cond_value = strtolower($tdata["cond_value"][$kk]);
				$value = strtolower($fValue[$column]);
				    if(strpos($value, $cond_value) !== false){

						$bool = "false";
						

					}else{
						$bool = "true";

						
					}
				}
				$where[] = '('.$bool.')';

			}elseif($tdata["condition"][$kk] == "is not"){
//                    if($flag == "excel" && $tdata["cond_column"][$kk] == "import_date-date"){
//						$where[] = '('.strtotime($field_val) .' != '. strtotime($tdata["cond_value"][$kk]) .')';
//					}else
					if($datatype == "date"){
						
						if($flag == "excel"){
							
							if(strtotime($field_val) != strtotime($tdata["cond_value"][$kk])){
								
								$bool = "true";
								
							}else{
								
								$bool = "false";
								
							}
							
							$where[] = $bool;
						}elseif($datatype == "number"){
						
							if(intval($fValue[$column]) != intval($tdata["cond_value"][$kk])){
								
								$bool = "true";
								
							}else{
								
								$bool = "false";
								
							}
							
							$where[] = $bool;
							
//							$where[] = '('.'"'.intval($fValue[$column]) .'"'.' == '. '"'.intval($tdata["cond_value"][$kk]).'"'.')';

						}else{
							
							if(strtotime($fValue[$column]) != strtotime($tdata["cond_value"][$kk])){
								
								$bool = "true";
								
							}else{
								
								$bool = "false";
								
							}
							
							$where[] = $bool;
						}
						
					}else{
						if($tdata["cond_column"][$kk] == "accounts-multiselect"){
							
							if (in_array($tdata["cond_value"][$kk], $fValue[$column]))
							  {
								  $bool = "false";
							  }
							else
							  {
								   $bool = "true";
							  }
							  
							$where[] = '('.$bool.')';   
						}else{
							if(strtotime($fValue[$column]) != strtotime($tdata["cond_value"][$kk])){
								
								$bool = "true";
								
							}else{
								
								$bool = "false";
								
							}
							
							$where[] = $bool;
						}
					}

				
			}elseif($tdata["condition"][$kk] == "starts with"){

				/*if($this->startsWith($fValue,$tdata["cond_value"][$kk])){ 
					
					$where[] = ["column"=>$column,"value"=>$tdata["cond_value"][$kk]];
					
				}
				*/
				

				if($tdata["cond_column"][$kk] == "accounts-multiselect"){
					$boo = "";
					foreach($fValue[$column] as $f_three){
						$value = strtolower($f_three);
						if($this->startsWith(strtolower(strval($f_three)),strtolower(strval($tdata["cond_value"][$kk])))){

						$boo = "true";
						

						}else{

							$boo = "false";
							

						}
						$bools[] = $boo;
						 
						
					}
						if(in_array("true",$bools)){
							$bool = "true";
						}else{
							$bool = "false";
						}
				}else{
					$bool = "";

					if($this->startsWith(strtolower(strval($fValue[$column])),strtolower(strval($tdata["cond_value"][$kk])))){

						$bool = "true";

					}else{

						$bool = "false";

					}
				}

				
				$where[] = '('.$bool.')';
				
				
			}elseif($tdata["condition"][$kk] == "ends with"){

				if($tdata["cond_column"][$kk] == "accounts-multiselect"){
					$boo = "";
					foreach($fValue[$column] as $f_four){
						$value = strtolower($f_four);
						if($this->endsWith(strtolower(strval($f_four)),strtolower(strval($tdata["cond_value"][$kk])))){

							$boo = "true";
						
						}else{

							$boo = "false";
						
						}
						$bools[] = $boo;
						 
						
					}
						if(in_array("true",$bools)){
							$bool = "true";
						}else{
							$bool = "false";
						}
						
				}
				else{
					$bool = "";

					if($this->endsWith(strtolower(strval($fValue[$column])),strtolower(strval($tdata["cond_value"][$kk])))){

						$bool = "true";

					}else{

						$bool = "false";

					}
                }

				
				$where[] = '('.$bool.')';

			}elseif($tdata["condition"][$kk] == "is blank"){

				/*if($fValue == ""){
					
					$where[] = ["column"=>$column,"value"=>""];
					
				}*/
				$bool = "";
				if($fValue[$column] == "" || $fValue[$column] == " "){
					$bool = "true";
				}else{
					$bool = "false";
				}
				
				$where[] = '('.$bool.')';
				//$where[] = '('."'".$fValue[$column]."'" .' == '. "".')';

			}elseif($tdata["condition"][$kk] == "is not blank" || $tdata["condition"][$kk] == "is any"){

				/*if($fValue != ""){
					
					$where[] = ["column"=>$column,"value"=>""];
					
				}*/
                $bool = "";
				if((string) $fValue[$column] != ""){
					$bool = "true";
				}else{
					$bool = "false";
				}
				
				$where[] = '('.$bool.')';
				//$where[] = '('."'".$fValue[$column]."'" .' != '. "".')';

				
			}elseif($tdata["condition"][$kk] == "higher than"){

				/*if($fValue > intval($tdata["cond_value"][$kk])){
					
					return $tdata['alertMessage'][0];
					
				}*/
				
				if($datatype == "number"){
					
					if(intval($fValue[$column]) > intval($tdata["cond_value"][$kk])){
								
						$bool = "true";

					}else{

						$bool = "false";

					}

					$where[] = $bool;
					
				}else{
					
					if($fValue[$column] > $tdata["cond_value"][$kk]){
								
						$bool = "true";

					}else{

						$bool = "false";

					}

					$where[] = $bool;	
					
				}
				
//				$where[] = '('."'".$fValue[$column]."'" .' > '. "'".intval($tdata["cond_value"][$kk])."'".')';
				
			}elseif($tdata["condition"][$kk] == "lower than"){

				if($datatype == "number"){
					
					if(intval($fValue[$column]) < intval($tdata["cond_value"][$kk])){
								
						$bool = "true";

					}else{

						$bool = "false";

					}

					$where[] = $bool;
					
				}else{
					
					if($fValue[$column] < $tdata["cond_value"][$kk]){
								
						$bool = "true";

					}else{

						$bool = "false";

					}

					$where[] = $bool;	
					
				}

				$where[] = $bool;
//				$where[] = '('."'".intval($fValue[$column])."'" .' < '. "'".intval($tdata["cond_value"][$kk])."'".')';

				
			}elseif($tdata["condition"][$kk] == "is during the current"){

				if($tdata["cond_value"][$kk] == "week"){

					$dates = $this->getDays("week");
					$start = $dates["start"];
					$end = $dates["end"];
				
					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($start)) && (strtotime($field_val) <= strtotime($end))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($start)) && (strtotime($fValue[$column]) <= strtotime($end))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = $bool;


				}elseif($tdata["cond_value"][$kk] == "month"){

					$first_date_month = date('Y-m-d',strtotime('first day of this month'));
                    $last_date_month = date('Y-m-d',strtotime('last day of this month'));
                    
					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($first_date_month)) && (strtotime($field_val) <= strtotime($last_date_month))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($first_date_month)) && (strtotime($fValue[$column]) <= strtotime($last_date_month))){
						$bool = "true";
						}else { 
						   $bool = "false";
						}
					} 
					
					$where[] = $bool;

				}elseif($tdata["cond_value"][$kk] == "quarter"){

					$dates = $this->getDays("quarter");
					$start = $dates["start"];
					$end = $dates["end"];

					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($start)) && (strtotime($field_val) <= strtotime($end))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($start)) && (strtotime($fValue[$column]) <= strtotime($end))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = $bool;
					
					
					/*if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}*/
	
				}elseif($tdata["cond_value"][$kk] == "year"){

					$first_date_year = date('d-m-Y',strtotime('first day of january this year'));
                    $last_date_year = date('d-m-Y',strtotime('last day of december this year'));

					if($flag == "excel"){
						if((strtotime($field_val) >= strtotime($first_date_year)) && (strtotime($field_val) <= strtotime($last_date_year))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($first_date_year)) && (strtotime($fValue[$column]) <= strtotime($last_date_year))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = $bool;

				}

			}elseif($tdata["condition"][$kk] == "is during the previous"){

				if($tdata["cond_value"][$kk] == "days"){

					$dates = $this->getDayscount("days","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];
					
					/*if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}*/
					
					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($start)) && (strtotime($field_val) <= strtotime($end))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($start)) && (strtotime($fValue[$column]) <= strtotime($end))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = '('.$bool.')';
					

				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					/*if($flag == "excel"){

						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}*/

					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($start)) && (strtotime($field_val) <= strtotime($end))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($start)) && (strtotime($fValue[$column]) <= strtotime($end))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = '('.$bool.')';
					
				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					/*if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}*/

					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($start)) && (strtotime($field_val) <= strtotime($end))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($start)) && (strtotime($fValue[$column]) <= strtotime($end))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = $bool;
					
				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					/*if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}*/
					
					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($start)) && (strtotime($field_val) <= strtotime($end))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($start)) && (strtotime($fValue[$column]) <= strtotime($end))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = '('.$bool.')';
					
				}

			}elseif($tdata["condition"][$kk] == "is before the previous"){
               
				if($tdata["cond_value"][$kk] == "days"){
             
					$dates = $this->getDayscount("days","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];
					
//					if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' < '. strtotime($start) .')';
//					}else{
//						$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($start) .')';
//					}
					
					if($flag == "excel"){
							
						if(strtotime($field_val) < strtotime($start)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}else{

						if(strtotime($fValue[$column]) < strtotime($start)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}
                      
				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];
//                    if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' < '. strtotime($start) .')';
//					}else{
//						$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($start) .')';
//					}
					
					if($flag == "excel"){
							
						if(strtotime($field_val) < strtotime($start)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}else{

						if(strtotime($fValue[$column]) < strtotime($start)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}

				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//					if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' < '. strtotime($start) .')';
//					}else{
//						$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($start) .')';
//					}
					
					if($flag == "excel"){
							
						if(strtotime($field_val) < strtotime($start)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}else{

						if(strtotime($fValue[$column]) < strtotime($start)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}

				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

//					if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' < '. strtotime($start) .')';
//					}else{
//						$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($start) .')';
//					}
					
					if($flag == "excel"){
							
						if(strtotime($field_val) < strtotime($start)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}else{

						if(strtotime($fValue[$column]) < strtotime($start)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}
					
					
				}

			}elseif($tdata["condition"][$kk] == "is during the next" || $tdata["condition"][$kk] == "is after the next"){

				if($tdata["cond_value"][$kk] == "days"){

					$dates = $this->getDayscount("days","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					/*if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}*/
					
					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($start)) && (strtotime($field_val) <= strtotime($end))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($start)) && (strtotime($fValue[$column]) <= strtotime($end))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = '('.$bool.')';
					

				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					/*if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}*/
					
					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($start)) && (strtotime($field_val) <= strtotime($end))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($start)) && (strtotime($fValue[$column]) <= strtotime($end))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = '('.$bool.')';

				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					/*if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}*/
					
					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($start)) && (strtotime($field_val) <= strtotime($end))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($start)) && (strtotime($fValue[$column]) <= strtotime($end))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = '('.$bool.')';

				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					/*if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}*/
					
					if($flag == "excel"){						
						if((strtotime($field_val) >= strtotime($start)) && (strtotime($field_val) <= strtotime($end))){
						  $bool = "true";
						}else { 
						   $bool = "false";
						}
					}else{
						if((strtotime($fValue[$column]) >= strtotime($start)) && (strtotime($fValue[$column]) <= strtotime($end))){
						   $bool = "true";
						}else { 
						   $bool = "false";
						}
					}
					$where[] = '('.$bool.')';

				}

			}elseif($tdata["condition"][$kk] == "is before" || $tdata["condition"][$kk] == "is after"){

				$date = $tdata["cond_value"][$kk];

				if($tdata["condition"][$kk] == "is before"){

//					if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' < '. strtotime($date).')';
//					}else{
//						$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($date).')';
//					}
					
					if($flag == "excel"){
							
						if(strtotime($field_val) < strtotime($date)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}else{

						if(strtotime($fValue[$column]) < strtotime($date)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}
					

				}elseif($tdata["condition"][$kk] == "is after"){

//					if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' > '. strtotime($date).')';
//					}else{
//						$where[] = '('.strtotime($fValue[$column]) .' > '. strtotime($date).')';
//					}
					
					if($flag == "excel"){
							
						if(strtotime($field_val) > strtotime($date)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}else{

						if(strtotime($fValue[$column]) > strtotime($date)){

							$bool = "true";

						}else{

							$bool = "false";

						}

						$where[] = $bool;
					}

				}

			}elseif($tdata["condition"][$kk] == "is today or before" || $tdata["condition"][$kk] == "is today or after" || $tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time" || $tdata["condition"][$kk] == "is before current time"){

				$date = date("Y-m-d");

				if($tdata["condition"][$kk] == "is today or before" || $tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is before current time"){

					if($tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is before current time"){
//						if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' < '. strtotime($date).')';
//						}else{
//							$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($date).')';
//						}
						
						if($flag == "excel"){
							
							if(strtotime($field_val) < strtotime($date)){

								$bool = "true";

							}else{

								$bool = "false";

							}

							$where[] = $bool;
						}else{

							if(strtotime($fValue[$column]) < strtotime($date)){

								$bool = "true";

							}else{

								$bool = "false";

							}

							$where[] = $bool;
						}
						
					}else{
//						if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' <= '. strtotime($date).')';
//						}else{
//							$where[] = '('.strtotime($fValue[$column]) .' <= '. strtotime($date).')';
//						}
						
						if($flag == "excel"){
							
							if(strtotime($field_val) <= strtotime($date)){

								$bool = "true";

							}else{

								$bool = "false";

							}

							$where[] = $bool;
						}else{

							if(strtotime($fValue[$column]) <= strtotime($date)){

								$bool = "true";

							}else{

								$bool = "false";

							}

							$where[] = $bool;
						}
						
					}
					

					
				}elseif($tdata["condition"][$kk] == "is today or after" || $tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time"){

					if($tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time"){
//						if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' > '. strtotime($date).')';
//						}else{
//							$where[] = '('.strtotime($fValue[$column]) .' > '. strtotime($date).')';
//						}
						
						if($flag == "excel"){
							
							if(strtotime($field_val) > strtotime($date)){

								$bool = "true";

							}else{

								$bool = "false";

							}

							$where[] = $bool;
						}else{

							if(strtotime($fValue[$column]) > strtotime($date)){

								$bool = "true";

							}else{

								$bool = "false";

							}

							$where[] = $bool;
						}
					}else{
//						if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' >= '. strtotime($date).')';
//						}else{
//							$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($date).')';
//						}
						
						if($flag == "excel"){
							
							if(strtotime($field_val) >= strtotime($date)){

								$bool = "true";

							}else{

								$bool = "false";

							}

							$where[] = $bool;
						}else{

							if(strtotime($fValue[$column]) >= strtotime($date)){

								$bool = "true";

							}else{

								$bool = "false";

							}

							$where[] = $bool;
						}
					}
					

				}

			}elseif($tdata["condition"][$kk] == "is today"){

				$date = date("Y-m-d");

//				if($flag == "excel"){
//						$where[] = '('.strtotime($field_val) .' == '. strtotime($date).')';
//					}else{
//						$where[] = '('.strtotime($fValue[$column]) .' == '. strtotime($date).')';
//					}
				
				if($flag == "excel"){
							
					if(strtotime($field_val) == strtotime($date)){

						$bool = "true";

					}else{

						$bool = "false";

					}

					$where[] = $bool;
				}else{

					if(strtotime($fValue[$column]) == strtotime($date)){

						$bool = "true";

					}else{

						$bool = "false";

					}

					$where[] = $bool;
				}
				

			}

		}
		
			return ($where);
		
	}
	
	function startsWith($string,$startString) { 
		$len = strlen($startString); 
		return (substr($string, 0, $len) === $startString); 
	} 
	
	function endsWith($string,$endString) { 
		$len = strlen($endString); 
		if ($len == 0) { 
			return true; 
		} 
		return (substr($string, -$len) === $endString); 
	} 

	public function getDays($condition){
	
		if($condition == "week"){
		
//			$start = (date('D') != 'Mon') ? date('Y-m-d', strtotime('last Monday')) : date('Y-m-d');
//			$finish = (date('D') != 'Sun') ? date('Y-m-d', strtotime('next Sunday')) : date('Y-m-d');

		    $signupdate=date("Y-m-d");
			$signupweek=date("W",strtotime($signupdate));
			$year=date("Y",strtotime($signupdate));
			$currentweek = date("W");

			$dto = new DateTime();
			$start = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
			$finish = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');

			$data = array("start"=>$start,"end"=>$finish);
			
		}elseif($condition == "month"){
			
			$start = date('Y-m-d');
			$finish = date('Y-m-d',strtotime("+30 days"));

			$data = array("start"=>$start,"end"=>$finish);
			
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
	
	public function getSetvaluefield($columnname="",$nameref="",$tablenm="",$recordValue=""){
		
		$this->mongo_db->switch_db($this->database);
		
		$col = $this->input->post("column");
		$rCount = $this->input->post("rCount");
		$tablename = $this->input->post("table");

		if($rCount){$rRef = $rCount;}else{$rRef = $nameref;}
		if($col){$column = $col;}else{$column = $columnname;}
		if($tablename){$table = $tablename;}else{$table = $tablenm;}
		
		if($column == "location" || $column == "flocation" || $column == "tlcoation" || $column == "tlocation" || ($column == "locname" && $table != "tbl_locations")){
			
			$locations = $this->mongo_db->where(["status"=>'Active'])->get("tbl_locations");
			
			$locnames = "";
			
			$locnames = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue'.$rRef.'[]" required>';			
			
			foreach($locations as $loc){
				
				$locsel = ($loc['locname'] == $recordValue) ? "selected" : "";

				$locnames .= '<option value="'.$loc['locname'].'" '.$locsel.'>'.$loc['locname'].'</option>';

			}
			$locnames .= '</select>'; 
			
		}elseif($column == "status"){
			
			$ast = ($recordValue == "Active") ? "selected" : "";
			$iast = ($recordValue == "Inactive") ? "selected" : "";
			
			$status = '<select class="form-control" name="ssetvalue'.$rRef.'[]" required="">
						  <option value="Active" '.$ast.'>Active</option>
						  <option value="Inactive" '.$iast.'>Inactive</option>
					   </select>';
			
		}elseif($column == "Type" || $column == "loctype"){
			
			$text = ($recordValue == "External") ? "selected" : "";
			$tint = ($recordValue == "Internal") ? "selected" : "";
			
			$loctype = '<select class="form-control" name="ssetvalue'.$rRef.'[]" required>
							<option value="External" '.$text.'>External</option>
							<option value="Internal" '.$tint.'>Internal</option>
						</select>';
			
		}elseif($column == "import_date" || $column == "shippmentdate" || $column == "reportdate" || $column == "processdate" || $column == "chepprocessdate" || $column == "last_report_date" || $column == "audit_date2019"){
			
			$impdate = ($recordValue != "") ? $recordValue : date("Y-m-d");
			$import_date = '<input type="date" class="form-control" name="ssetvalue'.$rRef.'[]" value="'.$impdate.'">';
			
		}elseif($column == "adjdirection"){
			
			$ain = ($recordValue == "IN") ? "selected" : "";
			$aout = ($recordValue == "OUT") ? "selected" : "";
			
			$accounts = '<select class="form-control" name="ssetvalue'.$rRef.'[]" required>';
			$accounts .= '<option value="IN" '.$ain.'>IN</option>
						  <option value="OUT" '.$aout.'>OUT</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "accounts" || $column == "user"){
			
			$accounts = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue'.$rRef.'[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active',"appid"=>$_SESSION['appid']],[],"$this->mdb.tbl_auths");
			
			 foreach($users as $u){
				 
				 $auser = ($recordValue == $u->uname) ? "selected" : "";
				 $accounts .= '<option value="'.$u->uname.'" '.$auser.'>'.$u->uname.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "item"){
			
			$accounts = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue'.$rRef.'[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active'],[],"$this->database.tbl_items");
			
			 foreach($users as $u){
				 
				 $sitem = ($recordValue == $u->item_name) ? "selected" : "";
				 $accounts .= '<option value="'.$u->item_name.'" '.$sitem.'>'.$u->item_name.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "uploadedetochep"){
			
			$uyes = ($recordValue == "Yes") ? "selected" : "";
			$uhold = ($recordValue == "Hold") ? "selected" : "";
			$ufcust = ($recordValue == "From Customer") ? "selected" : "";
			$uno = ($recordValue == "No") ? "selected" : "";
			
			$accounts = '<select class="form-control" name="ssetvalue'.$rRef.'[]" required>';
			$accounts .= '<option value="Yes" '.$uyes.'>Yes</option>
						  <option value="Hold" '.$uhold.'>Hold</option>
						  <option value="From Customer" '.$ufcust.'>From Customer</option>
						  <option value="No" '.$uno.'>No</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "reasonforhold"){
			
			$ric = ($recordValue == 'Reversed in Customer') ? 'selected' : '';
			$sdcu = ($recordValue == 'Suspended During Customer Upload') ? 'selected' : '';
			$rdcu = ($recordValue == 'Rejected During Customer Upload') ? 'selected' : '';
			$edcu = ($recordValue == 'Error During Customer Upload') ? 'selected' : '';
			$nci = ($recordValue == 'Need Customer ID') ? 'selected' : '';
			$dt = ($recordValue == 'Duplicate Transaction') ? 'selected' : '';
			$is = ($recordValue == 'International Shipment') ? 'selected' : '';
			$deost = ($recordValue == 'Data Error on Submission to') ? 'selected' : '';
			
			$accounts = '<select class="form-control" name="ssetvalue'.$rRef.'[]" required><option value="Reversed in Customer" '.$ric.'>Reversed in Customer</option><option value="Suspended During Customer Upload" '.$sdcu.'>Suspended During Customer Upload</option><option value="Rejected During Customer Upload" '.$rdcu.'>Rejected During Customer Upload</option><option value="Error During Customer Upload" '.$edcu.'>Error During Customer Upload</option><option value="Need Customer ID" '.$nci.'>Need Customer ID</option><option value="Duplicate Transaction" '.$dt.'>Duplicate Transaction</option><option value="International Shipment" '.$is.'>International Shipment</option><option value="Data Error on Submission to" '.$deost.'>Data Error on Submission to</option></select>';
			
		}else{
			
			if(($table == "tbl_inventory" && $column == "starting_balance") || ($table == "tbl_transfers" && $column == "quantity")){
			
				$import_date = '<input type="number" name="ssetvalue'.$rRef.'[]" class="form-control" pattern="^[1-9]" value="'.$recordValue.'" min="1" required>';
			
			}elseif($column == "issues" || $column == "returns" || $column == "transfer_ins" || $column == "transfer_outs" || $column == "adjustments" || $column == "ending_balance" || $column == "audit_count2019" || $column == "quantity"){
				
				$import_date = '<input type="number" name="ssetvalue'.$rRef.'[]" class="form-control" pattern="^[0-9]" value="'.$recordValue.'" required>';
				
			}else{
				
				$import_date = '<input type="text" name="ssetvalue'.$rRef.'[]" value="'.$recordValue.'" class="form-control">';
				
			}
		}
		
//		$fields = array("locnames"=>$locnames,"status"=>$status,"location_type"=>$loctype,"import_date"=>$import_date,"accounts"=>$accounts,"common"=>$common);
		
		if($locnames != ""){
			
			$fields = $locnames;
			
		}elseif($status != ""){
			
			$fields = $status;
			
		}elseif($loctype != ""){
			
			$fields = $loctype;
			
		}elseif($import_date != ""){
			
			$fields = $import_date;
			
		}elseif($accounts != ""){
			
			$fields = $accounts;
			
		}elseif($common != ""){
			
			$fields = $common;
			
		}
		
		return $fields;
		
	}

	
}