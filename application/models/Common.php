<?php

defined("BASEPATH") OR exit("No direct script access allow");


class Common extends CI_Model{

	public function __construct(){
		
		parent::__construct();
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
		$this->mdb = mongodb;
		
	}
	
	public function getConverteddate($shipmentdate){
		
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$shipmentdate)) {
			$sdate = $shipmentdate;
		} else {

			if(strpos($shipmentdate,"-")){
				$mp=explode("-",$shipmentdate);
				$sdate = $mp[2]."-".$mp[0]."-".$mp[1];
			}else if(strpos($shipmentdate,"/")){
				$mp=explode("/",$shipmentdate);
				$sdate = $mp[2]."-".$mp[0]."-".$mp[1];
			}

		}
		
		if($sdate == null || is_numeric($sdate)){
			
			$date = "";
			
		}else{
			
			$date = date("Y-m-d", strtotime($sdate));			
		}
		
		return $date;
		
	}


	public function getYmddate($shipmentdate){
		
		return $shipmentdate;
		
	}
	
	public function getInventorycount($database,$table,$aid,$loccode,$column,$itemname){
		
		$this->mongo_db->switch_db($database);
		
		if($table == "tbl_touts"){
			
			$this->mongo_db->where(['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active"]);
			
		}elseif($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){

			$this->mongo_db->where(['item.status'=>"Active","tlocation.status"=>"Active"]);
								   
		}
		$data = $this->mongo_db->select(["quantity"])->get_where($table,["appId"=>$aid,$column=>$loccode,"item.item_name"=>$itemname]);
		
		$sum = 0;
		
		if(count($data) > 0){
			
			foreach($data as $d){
				
				$sum += intval($d['quantity']);
				
			}
		}
		/*$data = $this->mongo_db->aggregate($table,[
				['$match' => ["appId"=>$aid,$column=>$loccode,"item.item_name"=>$itemname,"flag"=>"excel",'item.status'=>"Active","tlocation.status"=>"Active"]],
				['$group' => ["_id"=>null,"totalQty"=>['$sum'=>'$quantity']]],
			]);*/
		
//		return $data[0]['totalQty'];
//		return $data;
		
		return $sum;
		
	}
	
	public function getAddinventorycount($database,$table,$aid,$loccode,$column,$itemname){
		
		$this->mongo_db->switch_db($database);
		$sum = 0;
		
		if($table == "tbl_touts"){
			
			$this->mongo_db->where(['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active"]);
			
		}elseif($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){

			$this->mongo_db->where(['item.status'=>"Active","tlocation.status"=>"Active"]);
								   
		}
		$data = $this->mongo_db->select(["quantity","item","$column"])->get_where($table,["appId"=>$aid,$column=>$loccode,"item.item_name"=>$itemname]);
		
		if(count($data) > 0){
			
			foreach($data as $d){
				
				$inv = $this->mongo_db->where(array('item.item_name'=>$d["item"]->item_name,"loccode"=>$d[$column]))->get('tbl_inventory')[0];

				if($inv){
					
					$this->mongo_db->where(["flag"=>"excel","_id"=>new MongoDB\BSON\ObjectID($d["_id"]->{'$id'})])->set(["flag"=>"uexcel"])->update($table);		
					
				}

				$sum += intval($d['quantity']);
				
			}
		}
		
		return $sum;
		
	}

	public function getcronInventorycount($database,$table,$aid,$loccode,$column,$itemname,$flag=""){
		
		$data = $this->mongo_db->aggregate($table,[
				['$group' => ["_id"=>'$'.$column,"totalQty"=>['$sum'=>'$quantity']]],
				['$match' => ["appId"=>$aid,$column=>$loccode,"item.item_name"=>$itemname,'item.status'=>"Active","tlocation.status"=>"Active","flag"=>"excel"]],
			]);
		
		return $data[0]['totalQty'];

	}

	public function updateLocationinventorycount($database,$table,$aid,$loccode,$column,$itemname,$quantity,$field,$exdata=""){
		
		$this->mongo_db->switch_db($database);		
		
		$exInv = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$loccode,"item.item_name"=>$itemname])[0];
		
		if($exdata){
			
			if(($loccode != $exdata[$column]) || ($itemname != $exdata["item"]->item_name)){
			
				$count = intval($quantity);
		
			}else{
				
				$count = (intval($exInv[$field]) - intval($exdata["quantity"])) + intval($quantity);
				
			}
			
		}else{
			
			$count = intval($exInv[$field]) + intval($quantity);	
			
		}
		
//		return($count);
		
		if((($loccode != $exdata[$column]) || ($itemname != $exdata["item"]->item_name)) && $exdata){

			$ltdata = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$loccode,"item.item_name"=>$itemname])[0];
			
			$val['starting_balance'] = intval($ltdata["starting_balance"]);
			$val['issues'] = intval($ltdata["issues"]);
			$val['returns'] = intval($ltdata["returns"]);
			$val['transfer_ins'] = intval($ltdata["transfer_ins"]);
			$val['transfer_outs'] = intval($ltdata["transfer_outs"]);
			$val['adjustments'] = intval($ltdata["adjustments"]);
			
			if($table == "tbl_touts"){
				
				if($column == "tlocationcode"){
					
					$val['transfer_ins'] = intval($ltdata["transfer_ins"]) + intval($count);
					
				}else{
					
					$val['transfer_outs'] = intval($ltdata["transfer_outs"]) + intval($count);
					
				}
				
			}elseif($table == "tbl_issues"){
				
				$val['issues'] = intval($ltdata["issues"]) + intval($count);
				
			}elseif($table == "tbl_returns"){
				
				$val['returns'] = intval($ltdata["returns"]) + intval($count);
				
			}elseif($table == "tbl_adjustments"){
				
				$val['adjustments'] = intval($ltdata["adjustments"]) + intval($count);
				
			}
			
			$ending_balance = ($val['starting_balance'] + $val['issues'] + $val['returns'] + $val['transfer_ins'] - $val['transfer_outs'] + $val['adjustments']); 
			
			$val['ending_balance'] = intval($ending_balance);
			
//			return($val);

			if($ltdata){
				
				$this->mongo_db->where(["loccode"=>$loccode,"item.item_name"=>$itemname])->set($val)->update("tbl_inventory");

			}else{
				
				$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$itemname])[0];
				$val["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];

				$tlocdata = $this->mongo_db->get_where("tbl_locations",["loccode"=>$loccode])[0];					
				$val["locname"] = ["id"=>$tlocdata['_id']->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
				$val["location"] = $tlocdata['locname']." - ".$tlocdata['loccode'];
				$val["loccode"] = $tlocdata["loccode"];
				$val["loctype"] = $tlocdata["Type"];
				$val["id"] = $this->admin->insert_id("tbl_inventory",$database);
			
				$this->mongo_db->insert("tbl_inventory",$val);	
				
			}

// decrease quantity
			
			$oldinvdata = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$exdata[$column],"item.item_name"=>$exdata["item"]->item_name])[0];
			
			$dval['starting_balance'] = intval($oldinvdata["starting_balance"]);
			$dval['issues'] = intval($oldinvdata["issues"]);
			$dval['returns'] = intval($oldinvdata["returns"]);
			$dval['transfer_ins'] = intval($oldinvdata["transfer_ins"]);
			$dval['transfer_outs'] = intval($oldinvdata["transfer_outs"]);
			$dval['adjustments'] = intval($oldinvdata["adjustments"]);
			
			if($table == "tbl_touts"){
				
				if($column == "tlocationcode"){
					
					$dval['transfer_ins'] = intval($oldinvdata["transfer_ins"]) - intval($count);
					
				}else{
					
					$dval['transfer_outs'] = intval($oldinvdata["transfer_outs"]) - intval($count);
					
				}
				
			}elseif($table == "tbl_issues"){
				
				$dval['issues'] = intval($oldinvdata["issues"]) - intval($count);
				
			}elseif($table == "tbl_returns"){
				
				$dval['returns'] = intval($oldinvdata["returns"]) - intval($count);
				
			}elseif($table == "tbl_adjustments"){
				
				$dval['adjustments'] = intval($oldinvdata["adjustments"]) - intval($count);
				
			}
			
			$ending_balance = ($dval['starting_balance'] + $dval['issues'] + $dval['returns'] + $dval['transfer_ins'] - $dval['transfer_outs'] + $dval['adjustments']); 
			
			$dval['ending_balance'] = intval($ending_balance);
			
			$this->mongo_db->where(["loccode"=>$exdata[$column],"item.item_name"=>$exdata["item"]->item_name])->set($dval)->update("tbl_inventory");
			
		}else{
			
//			return($count);
//			return "yes";

			$this->mongo_db->where(["loccode"=>$loccode,"item.item_name"=>$itemname])->set([$field=>$count])->update("tbl_inventory");

			$invdata = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$loccode,"item.item_name"=>$itemname])[0];

			$issues = (int) $invdata["issues"];
			$returns = (int) $invdata["returns"];
			$transfer_ins = (int) $invdata["transfer_ins"];
			$transfer_outs = (int) $invdata["transfer_outs"];
			$adjustments = (int) $invdata["adjustments"];
			$starting_balance = (int) $invdata["starting_balance"];

			$ending_balance = $starting_balance+$issues+$returns+$transfer_ins-$transfer_outs+$adjustments;

	//		return $ending_balance;

			$this->mongo_db->where(["loccode"=>$loccode,"item.item_name"=>$itemname])->set(["ending_balance"=>intval($ending_balance)])->update("tbl_inventory");

		}
		
	}
	
	
	public function getFiltervalues($cursor,$table){
	
		$this->mongo_db->switch_db($this->database);
		$out=[];
		$i=1;
		$j=1;
		foreach($cursor as $erow){
			
			$row = json_decode(json_encode($erow), true);
			
			if($table == "tbl_locations"){
				
				$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]{'$oid'}.'">';
			    $row["locid"] = $row["locid"];
				$date = "";
				$time = "";
				if($row["import_date"] != ''){
					$date = $this->common->getConverteddate(explode(" ",$row["import_date"])[0]);
					$time = explode(" ",$row["import_date"])[1];
					$row["import_date"] = date("m-d-Y",strtotime($date))." ".$time;
				}

				if($row["nameid"]){

					$row["nameid"] = $row["locname"]."-".$row["loccode"];

				}

				$row["Actions"] = '<a href="'.$row["_id"]{'$oid'}.'" class="editLocate" lid="'.$row["_id"]{'$oid'}.'"  lcode="'.$row["loccode"].'" lname="'.$row["locname"].'" address="'.$row["address"].'" city="'.$row["city"].'" state="'.$row["state"].'" zip="'.$row["zip"].'" country="'.$row["country"].'" status="'.$row["status"].'" Type="'.$row["Type"].'" impdate="'.$date.'" time="'.$time.'" accounts="'.implode(",",$row["accounts"]).'" notes="'.$row["notes"].'" data-toggle="modal" data-target=".bs-example-modal-lg"><i class="far fa-edit"></i></a> | <a href="javascript:void(0)" id="'.$row["_id"]->{'$oid'}.'" onclick="archiveFunction(this.id)"><i class="fas fa-trash-alt" style="color: red"></i></a>';
				array_push($out,$row);
				
			}elseif($table == "tbl_items"){
				$row["itemid"] = $row["id"];
				$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]{'$oid'}.'">';
				$row["Actions"] = '<a href="javascript:void(0)" class="editItem" icode="'.$row['item_code'].'" iname="'.str_replace('"',"'",$row['item_name']).'" status="'.$row['status'].'" iid="'.$row["_id"]{'$oid'}.'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]{'$oid'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';
				array_push($out,$row);
				
			}elseif($table == "tbl_touts"){

				$shipmentdate = "";
				$processdate = "";
				$chepprocessdate = "";
				$reportdate = "";
				
	/*			$itemstatus = $this->mongo_db->get_where("tbl_items",["item_name"=>$row['item']['item_name']])[0];
				$flocstatus = $this->mongo_db->get_where("tbl_locations",["loccode"=>$row["flcoationcode"]])[0];
				$tlocstatus = $this->mongo_db->get_where("tbl_locations",["loccode"=>$row["tlocationcode"]])[0];*/

//				if($row['item']["status"] == "Active" && ($row['flocation']["status"] == "Active") && ($row['tlcoation']["status"] == "Active")){
				
					$shipmentdate=$this->common->getConverteddate($row["shippmentdate"]);
					$processdate=$this->common->getConverteddate($row["processdate"]);
					$chepprocessdate=$this->common->getConverteddate($row["chepprocessdate"]);
					$reportdate=$this->common->getConverteddate(explode(" ",$row["reportdate"])[0]);
                    $row["Sno"] = $j;
					$row["transferid"] = $row["id"];
					$row["item"] = $row['item']['item_name'];
					$row["flocation"] = $row["flocation"]['locname'];
					$row["tlcoation"] = $row["tlcoation"]['locname'];
					$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]{'$oid'}.'">';
					$row["Actions"] = '<a href="javascript:void(0)" class="editTransfer" lid="'.$row["_id"]{'$oid'}.'" shipperpo="'.$row["shipperpo"].'" shippmentdate="'.$shipmentdate.'" pronum="'.$row["pronum"].'" reference="'.$row["reference"].'" item="'.$row["item"].'" flocation="'.$row["flocation"].'" flcoationcode="'.$row["flcoationcode"].'" tlcoation="'.$row["tlcoation"].'" tlocationcode="'.$row["tlocationcode"].'" quantity="'.$row["quantity"].'" reportdate="'.$reportdate.'" time="'.explode(" ",$row["reportdate"])[1].'" user="'.$row["user"].'" processdate="'.$processdate.'" chepprocessdate="'.$chepprocessdate.'" chepumi="'.$row["chepumi"].'" uploadedetochep="'.$row["uploadedetochep"].'" reasonforhold="'.$row["reasonforhold"].'" transactionid="'.$row["transactionid"].'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]{'$oid'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';

					$row["shippmentdate"] = ($row["shippmentdate"] != "") ? date("m-d-Y",strtotime($shipmentdate)) : "";
					$row["processdate"] = ($row["processdate"] != "") ? date("m-d-Y",strtotime($processdate)) : "";
					$row["chepprocessdate"] = ($row["chepprocessdate"] != "") ? date("m-d-Y",strtotime($chepprocessdate)) : "";
					$row["reportdate"] = ($row["reportdate"][0] != "") ? date("m-d-Y",strtotime($reportdate)) : "";

					array_push($out,$row);
					
//				}
				
			}elseif($table == "tbl_issues"){
				
				$shipmentdate = "";
				$chepprocessdate = "";

/*				$itemstatus = $this->mongo_db->get_where("tbl_items",["item_name"=>$row['item']['item_name']])[0];
				$tlocstatus = $this->mongo_db->get_where("tbl_locations",["loccode"=>$row["tlcoationcode"]])[0];*/

//				if($row['item']["status"] == "Active" && $row['tlocation']["status"] == "Active"){
				
					if($row["shippmentdate"] != ""){
						$shipmentdate=$this->common->getConverteddate($row["shippmentdate"]);
					}else{
						$shipmentdate="";
					}
					if($row["chepprocessdate"] != ""){
						$chepprocessdate=$this->common->getConverteddate($row["chepprocessdate"]);
					}else{
						$chepprocessdate="";
					}
					$row["Sno"] = $j;
				    $row["issueid"] = $row["id"];
				    $row["item"] = $row['item']['item_name'];
					$row["tlocation"] = $row["tlocation"]['locname'];
					$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]{'$oid'}.'">';
					$row["Actions"] = '<a href="javascript:void(0)" class="editLocate" id="'.$row["_id"]{'$oid'}.'" chepreference="'.$row["chepreference"].'" ongreference="'.$row["ongreference"].'" shippmentdate="'.$shipmentdate.'" quantity="'.$row["quantity"].'" item="'.$row["item"].'" tlcoation="'.$row["tlocation"].'" tlcoationcode="'.$row["tlcoationcode"].'" chepprocessdate="'.$chepprocessdate.'" umi="'.$row["umi"].'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]{'$oid'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';
					
					$row["shippmentdate"] = ($row["shippmentdate"][0] != "") ? date("m-d-Y",strtotime($shipmentdate)) : "";
					$row["chepprocessdate"] = ($row["chepprocessdate"][0] != "") ? date("m-d-Y",strtotime($chepprocessdate)) : "";
					
					array_push($out,$row);
				
//				}
					
			}elseif($table == "tbl_returns"){
				
				$shipmentdate = "";
				$chepprocessdate = "";
								
/*				$itemstatus = $this->mongo_db->get_where("tbl_items",["item_name"=>$row['item']['item_name']])[0];
				$tlocstatus = $this->mongo_db->get_where("tbl_locations",["loccode"=>$row["tlcoationcode"]])[0];*/

//				if($row['item']["status"] == "Active" && $row['tlocation']["status"] == "Active"){
					
					$shipmentdate=$this->common->getConverteddate($row["shippmentdate"]);				
					$chepprocessdate=$this->common->getConverteddate($row["chepprocessdate"]);
					$row["Sno"] = $j;				
					$row["returnid"] = $row["id"];
					$row["item"] = $row['item']['item_name'];
					$row["tlocation"] = $row["tlocation"]['locname'];				
					$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]{'$oid'}.'">';
					$row["Actions"] = '<a href="javascript:void(0)" class="editLocate" id="'.$row["_id"]{'$oid'}.'" chepreference="'.$row["chepreference"].'" ongreference="'.$row["ongreference"].'" shippmentdate="'.$shipmentdate.'" quantity="'.$row["quantity"].'" item="'.$row["item"].'" tlocation1="'.$row["tlocation"].'" tlcoationcode="'.$row["tlcoationcode"].'" chepprocessdate="'.$chepprocessdate.'" umi="'.$row["umi"].'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]{'$oid'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';
					
					$row["shippmentdate"] = ($row["shippmentdate"][0] != "") ? date("m-d-Y",strtotime($shipmentdate)) : "";
					$row["chepprocessdate"] = ($row["chepprocessdate"][0] != "") ? date("m-d-Y",strtotime($chepprocessdate)) : "";
				
					
					array_push($out,$row);
				
//				}
					
			}elseif($table == "tbl_adjustments"){
				
				$shipmentdate = "";
				$chepprocessdate = "";				
				
/*				$itemstatus = $this->mongo_db->get_where("tbl_items",["item_name"=>$row['item']['item_name']])[0];
				$tlocstatus = $this->mongo_db->get_where("tbl_locations",["loccode"=>$row["tlcoationcode"]])[0];

				if($row['item']["status"] == "Active" && $row['tlocation']["status"] == "Active"){	*/
					
					$shipmentdate=$this->common->getConverteddate($row["shippmentdate"]);				
					$chepprocessdate=$this->common->getConverteddate($row["chepprocessdate"]);	
					$row["Sno"] = $j;								
                    $row["adjustmentid"] = $row["id"];
                    $row["item"] = $row['item']['item_name'];
					$row["tlocation"] = $row["tlocation"]['locname'];
					$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]{'$oid'}.'">';
					$row["Actions"] = '<a href="javascript:void(0)" class="editLocate" lid="'.$row["_id"]{'$oid'}.'" chepreference="'.$row["chepreference"].'" ongreference="'.$row["ongreference"].'" shippmentdate="'.$shipmentdate.'" quantity="'.$row["quantity"].'" item="'.$row["item"].'" tlocation="'.$row["tlocation"].'" chepprocessdate="'.$chepprocessdate.'" adjdirection="'.strtoupper($row["adjdirection"]).'" umi="'.$row["umi"].'" tlcoationcode="'.$row["tlcoationcode"].'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]{'$oid'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';
					
					$row["shippmentdate"] = ($row["shippmentdate"][0] != "") ? date("m-d-Y",strtotime($shipmentdate)) : "";
					$row["chepprocessdate"] = ($row["chepprocessdate"][0] != "") ? date("m-d-Y",strtotime($chepprocessdate)) : "";

					array_push($out,$row);

//				}
				
			}elseif($table == "tbl_inventory"){
				
				$last_report_date = "";
				$audit_date2019 = "";				
				/*$itemstatus = $this->mongo_db->get_where("tbl_items",["item_name"=>$row['item']['item_name']])[0];
				$tlocstatus = $this->mongo_db->get_where("tbl_locations",["loccode"=>$row["loccode"]])[0];

				if($row['item']["status"] == "Active" && $row['locname']["status"] == "Active"){*/
					
					$last_report_date=$this->common->getConverteddate($row["last_report_date"]);				
					$audit_date2019=$this->common->getConverteddate($row["audit_date2019"]);				
					$row["inventoryid"] = $row["id"];
					$row["item"] = $row['item']['item_name'];
					$row["locname"] = $row['locname']['locname'];				
					$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]{'$oid'}.'">';
					$row["Actions"] = '<a href="javascript:void(0)" class="editInventory" lid="'.$row["_id"]{'$oid'}.'" location="'.$row["location"].'" locname="'.$row["locname"].'" loccode="'.$row["loccode"].'" loctype="'.$row["loctype"].'" notes="'.$row["notes"].'" last_report_date="'.$last_report_date.'" starting_balance="'.$row["starting_balance"].'" issues="'.$row["issues"].'" returns="'.$row["returns"].'" transfer_ins="'.$row["transfer_ins"].'" transfer_outs="'.$row["transfer_outs"].'" adjustments="'.$row["adjustments"].'" ending_balance="'.$row["ending_balance"].'" audit_count2019="'.$row["audit_count2019"].'" audit_date2019="'.$audit_date2019.'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]{'$oid'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';
					
					$row["last_report_date"] = ($row["last_report_date"] != "") ? date("m-d-Y",strtotime($last_report_date)) : "";
					$row["audit_date2019"] = ($row["audit_date2019"] != "") ? date("m-d-Y",strtotime($audit_date2019)) : "";
					
					
					array_push($out,$row);
				
//				}
			}
			$j++;
		}
		
		return $out;
		
	}
	
	public function getConditionbydatatype($datatype,$table="",$column=""){
		
		if($datatype == "text" || $datatype == "textarea"){
			
			if($table == "tbl_touts" && $column == "transactionid"){
				
				$data = ["contains","does not contain","is","is not","starts with","ends with","higher than","lower than","is blank","is not blank"];		

			}elseif($table == "tbl_inventory" && $column == "locname"){
				
				$data = ["contains","does not contain","is","is not","is any","starts with","ends with","is blank","is not blank"];
				
			}else{
				
				$data = ["contains","does not contain","is","is not","starts with","ends with","is blank","is not blank"];		
			
			}
			
		}elseif($datatype == "select" || $datatype == "multiselect"){
		
			$data = ["is","is not","is any","starts with","ends with","is blank","is not blank"];
		
		}elseif($datatype == "date"){
			
			$data = ["is","is not","is during the current","is during the previous","is before the previous","is during the next","is before","is after","is today","is today or before","is today or after","is before today","is after today","is before current time","is after current time","is blank","is not blank"];		
			
		}elseif($datatype == "number"){
			
			if(($table == "tbl_locations" && $column == "zip")){
				
				$data = ["contains","does not contain","is","is not","higher than","lower than","starts with","ends with","is blank","is not blank"];		
				
			}else{
			
				$data = ["is","is not","higher than","lower than","is blank","is not blank"];		
			
			}
			
		}else{
			
			$data = ["contains","does not contain","is","is not","starts with","ends with","is blank","is not blank"];		
			
		}
		
		return $data;
	}


	public function getInventoryChepAdminConsolidated($item){
		
		$appid= $this->session->userdata("appid");
		$mng = $this->admin->Mconfig();
		$out = [];
		$rows = $this->admin->getRows($mng,["appId"=>$appid,"last_report_date"=>['$ne'=>" "],"item"=>$item],['sort'=>['_id'=>-1]],"$this->database.tbl_inventory");
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
			array_push($issues, abs($row->issues));
			array_push($returns, abs($row->returns));
			array_push($tins, abs($row->transfer_ins));
			array_push($touts, abs($row->transfer_outs));
			array_push($adjustments, abs($row->adjustments));
			array_push($ebal, abs($row->ending_balance));
			array_push($acount2019, abs($row->audit_count2019));
		}
		array_push($out, array("issues"=>array_sum($issues),"returns"=>array_sum($returns),"tins"=>array_sum($tins),"touts"=>array_sum($touts),"adjustments"=>array_sum($adjustments),"ebal"=>array_sum($ebal),"acount2019"=>array_sum($acount2019)));
		
//		echo json_encode($out);
		
		return $out;
		
	}
		

	public function getexcellabels($headers,$table){
		
//		$exheaders = $headers;
		
		if($table == "tbl_locations"){
			
			array_unshift($headers,"Location ID");
			
		}elseif($table == "tbl_items"){
			
			array_unshift($headers,"Item ID");
			
		}elseif($table == "tbl_touts"){
			
			array_unshift($headers,"Transfer ID");
			
		}elseif($table == "tbl_issues"){
			
			array_unshift($headers,"Shipment ID");
			
		}elseif($table == "tbl_returns"){
			
			array_unshift($headers,"Pickup ID");
			
		}elseif($table == "tbl_adjustments"){
			
			array_unshift($headers,"Adjustment ID");
			
		}elseif($table == "tbl_inventory"){
			
			array_unshift($headers,"Inventory ID");
			
		}
		
		return $headers;
		
	}
	
	public function getexcelcolumns($headers,$table){
		
//		$exheaders = $headers;
		
		if($table == "tbl_locations"){
			
			array_unshift($headers,"locid");
			
		}elseif($table == "tbl_items"){
			
			array_unshift($headers,"id");
			
		}elseif($table == "tbl_touts"){
			
			array_unshift($headers,"id");
			
		}elseif($table == "tbl_issues"){
			
			array_unshift($headers,"id");
			
		}elseif($table == "tbl_returns"){
			
			array_unshift($headers,"id");
			
		}elseif($table == "tbl_adjustments"){
			
			array_unshift($headers,"id");
			
		}elseif($table == "tbl_inventory"){
			
			array_unshift($headers,"id");
			
		}
		
		return $headers;
		
	}
	
	public function getUpdatedFieldsOperators($col="",$dType="",$tbl="",$soperator="",$sfield="",$refopid="",$cnameref="",$operatorclass=""){
		
		$this->mongo_db->switch_db($this->database);
		
		$column = $col;
		$datatype = $dType;
		$table = $tbl;
		
		$accounts = "";
		$locnames = "";
		$status = "";
		$loctype = "";
		$import_date = "";
		$common = "";
		
		if(($column == "locname" && $table != "tbl_locations")){
			
			$locations = $this->mongo_db->get_where("tbl_locations",["status"=>"Active"]);
			
			$accounts = "";
			$accounts .= '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$cnameref.'[]" required>';
			
			
			foreach($locations as $loc){
				
				$lsel = ($loc['locname'] == $sfield) ? "selected" : "";
				$accounts .= '<option value="'.$loc['locname'].'" '.$lsel.'>'.$loc['locname'].'</option>';

			}
			
			$accounts .= '</select>';
			
		}elseif($column == "item"){
			
			$locations = $this->mongo_db->get_where("tbl_items",["status"=>"Active"]);
			
			$accounts = "";
			$accounts .= '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$cnameref.'[]" required>';
			
			
			foreach($locations as $loc){
				
				$isel = ($loc['item_name'] == $sfield) ? "selected" : "";
				$accounts .= '<option value="'.$loc['item_name'].'" '.$isel.'>'.$loc['item_name'].'</option>';

			}
			
			$accounts .= '</select>';
			
		}elseif($column == "location" || $column == "flocation" || $column == "tlcoation" || $column == "tlocation"){
			
			$locations = $this->mongo_db->get_where("tbl_locations",["status"=>"Active"]);
			
			$accounts = "";
			$accounts .= '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$cnameref.'[]" required>';
			
			
			foreach($locations as $loc){
				
				$lsel = ($loc['locname'] == $sfield) ? "selected" : "";
				$accounts .= '<option value="'.$loc['locname'].'" '.$lsel.'>'.$loc['locname'].'</option>';

			}
			
			$accounts .= '</select>';
			
		}elseif($column == "status"){
			
			$sasel = ($sfield == "Active") ? 'selected' : '';
			$sinsel = ($sfield == "Inactive") ? 'selected' : '';
			
			$status = '<select class="form-control" name="cond_value'.$cnameref.'[]" required=""><option value="Active" '.$sasel.'>Active</option><option value="Inactive" '.$sinsel.'>Inactive</option></select>';
			
		}elseif($column == "Type" || $column == "loctype"){
			
			$ltint = ($sfield == 'Internal') ? 'selected' : '';
			$ltext = ($sfield == 'External') ? 'selected' : '';
			
			$loctype = '<select class="form-control" name="cond_value'.$cnameref.'[]" required><option value="External" '.$ltext.'>External</option><option value="Internal" '.$ltint.'>Internal</option></select>';
			
		}elseif($column == "reasonforhold"){
			
			$ric = ($sfield == 'Reversed in Customer') ? 'selected' : '';
			$sdcu = ($sfield == 'Suspended During Customer Upload') ? 'selected' : '';
			$rdcu = ($sfield == 'Rejected During Customer Upload') ? 'selected' : '';
			$edcu = ($sfield == 'Error During Customer Upload') ? 'selected' : '';
			$nci = ($sfield == 'Need Customer ID') ? 'selected' : '';
			$dt = ($sfield == 'Duplicate Transaction') ? 'selected' : '';
			$is = ($sfield == 'International Shipment') ? 'selected' : '';
			$deost = ($sfield == 'Data Error on Submission to') ? 'selected' : '';
			
			$loctype = '<select class="form-control" name="cond_value'.$cnameref.'[]" required><option value="Reversed in Customer" '.$ric.'>Reversed in Customer</option><option value="Suspended During Customer Upload" '.$sdcu.'>Suspended During Customer Upload</option><option value="Rejected During Customer Upload" '.$rdcu.'>Rejected During Customer Upload</option><option value="Error During Customer Upload" '.$edcu.'>Error During Customer Upload</option><option value="Need Customer ID" '.$nci.'>Need Customer ID</option><option value="Duplicate Transaction" '.$dt.'>Duplicate Transaction</option><option value="International Shipment" '.$is.'>International Shipment</option><option value="Data Error on Submission to" '.$deost.'>Data Error on Submission to</option></select>';
			
		}elseif($column == "adjdirection"){
			
			$ain = ($sfield == 'IN') ? 'selected' : '';
			$aout = ($sfield == 'OUT') ? 'selected' : '';
			$accounts = '<select class="form-control" name="cond_value'.$cnameref.'[]" required>';
			
			$accounts .= '<option value="IN" '.$ain.'>IN</option><option value="OUT" '.$aout.'>OUT</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "uploadedetochep"){
			
			$upyes = ($sfield == 'Yes') ? 'selected' : '';
			$uphold = ($sfield == 'Hold') ? 'selected' : '';
			$upfcust = ($sfield == 'From Customer') ? 'selected' : '';
			$upno = ($sfield == 'No') ? 'selected' : '';
			
			$loctype = '<select class="form-control" name="cond_value'.$cnameref.'[]" required><option value="Yes" '.$upyes.'>Yes</option><option value="Hold" '.$uphold.'>Hold</option><option value="From Customer" '.$upfcust.'>From Customer</option><option value="No" '.$upno.'>No</option></select>';
			
		}elseif($column == "import_date"){
			
			$import_date = '<input type="date" class="form-control" name="cond_value'.$cnameref.'[]" value="'.$sfield.'">';
			
		}elseif($column == "shippmentdate" || $column == "chepprocessdate" || $column == "reportdate" || $column == "processdate" || $column == "last_report_date" || $column == "audit_date2019"){
			
			$import_date = '<input type="date" class="form-control" name="cond_value'.$cnameref.'[]" value="'.$sfield.'">';
			
		}elseif($column == "accounts" || $column == "user"){
			
			$accounts = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$cnameref.'[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active',"appid"=>$_SESSION['appid']],[],"$this->mdb.tbl_auths");
			
			 foreach($users as $u){
				 
				 $usel = ($sfield == $u->uname) ? 'selected' : '';
				 
				 $accounts .= '<option value="'.$u->uname.'" '.$usel.'>'.$u->uname.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "issues" || $column == "returns" || $column == "transfer_ins" || $column == "transfer_outs" || $column == "adjustments" || $column == "ending_balance" || $column == "audit_count2019" || $column == "quantity"){
		
			$common = '<input type="number" name="cond_value'.$cnameref.'[]" class="form-control" value="'.$sfield.'">';
			
		}elseif($column == "starting_balance"){
		
			$common = '<input type="text" name="cond_value'.$cnameref.'[]" class="form-control" value="'.$sfield.'" pattern="[0-9]+" title="Only Zero & Positive Numbers are allowed" required>';
			
		}else{
			
			$common = '<input type="text" name="cond_value'.$cnameref.'[]" class="form-control" value="'.$sfield.'">';
			
		}
		
		$operators = $this->common->getConditionbydatatype($datatype,$table,$column);
		
		$oper = '<select name="condition'.$cnameref.'[]" class="form-control '.$operatorclass.'" rCount="'.$cnameref.'" opid="'.$refopid.'">';
		
		foreach($operators as $op){
			
			$selected = ($soperator == $op) ? 'selected' : '';
			
			$oper .= '<option value="'.$op.'" '.$selected.'>'.$op.'</option>';
			
		}
		
		$oper .= '</select>';
		
		$fields = array("locnames"=>$locnames,"status"=>$status,"location_type"=>$loctype,"import_date"=>$import_date,"accounts"=>$accounts,"common"=>$common);
		
		$outFields = '';
		
			if($fields['locnames'] != null){
				
				$outFields = $fields['locnames']; 
				
			}elseif($fields['status'] != null){
				
				$outFields = $fields['status']; 
				
			}elseif($fields['location_type'] != null){
				
				$outFields = $fields['location_type']; 
				
			}elseif($fields['import_date'] != null){
				
				$outFields = $fields['import_date']; 
				
			}elseif($fields['accounts'] != null){
				
				$outFields = $fields['accounts']; 
				
			}elseif($fields['common'] != null){
				
				$outFields = $fields['common']; 
				
			}
		
		return ["fields"=>$outFields,"operators"=>$oper];
	}
	


	public function getsetvalfields($tbl="",$val=""){
		
		if($tbl == ""){
			
			$table = $this->input->post("table");
			
		}else{
			
			$table = $tbl;			
			
		}
		
		$lcolumns = $this->admin->getRow("",["table"=>$table],[],$this->database.".settings");
	
		$columns = '<select name="ssetvalue[]" class="form-control">'; 
		foreach($lcolumns->labels as $key => $labels){
			
			$selected = ($lcolumns->columns[$key]."-".$lcolumns->dataType[$key]==$val) ? 'selected' : '';
			
			if($table == "tbl_inventory"){	
				
				if(($lcolumns->columns[$key] != "location") && ($lcolumns->columns[$key] != "loccode") && ($lcolumns->columns[$key] != "loctype") && ($lcolumns->columns[$key] != "issues") && ($lcolumns->columns[$key] != "returns") && ($lcolumns->columns[$key] != "transfer_ins") && ($lcolumns->columns[$key] != "transfer_outs") && ($lcolumns->columns[$key] != "adjustments") && ($lcolumns->columns[$key] != "ending_balance")){

					$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'" '.$selected.'>'.$labels.'</option>';

				}
				
			}elseif($table == "tbl_touts"){
				
				if($lcolumns->columns[$key] != "tlocationcode"){

					$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'" '.$selected.'>'.$labels.'</option>';

				}
				
			}elseif(($table == "tbl_returns") || ($table == "tbl_issues") || ($table == "tbl_adjustments")){
				
				if($lcolumns->columns[$key] != "tlcoationcode"){

					$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'" '.$selected.'>'.$labels.'</option>';

				}
				
			}else{
				
				$columns .= '<option value="'.$lcolumns->columns[$key]."-".$lcolumns->dataType[$key].'" '.$selected.'>'.$labels.'</option>';
				
			}
			

		}
		$columns .= '</select>';
		
		return array("columns"=>$columns);
		
	}
	
	public function getUpdatedFields($column,$table,$val){
		
		$this->mongo_db->switch_db($this->database);
		
		if($column == "locname" && $table != "tbl_locations"){
			
			$locations = $this->mongo_db->get_where("tbl_locations",["status"=>"Active"]);
			
			$accounts = "";
			$accounts .= '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue[]" required>';
			
			
			foreach($locations as $loc){
				
				$lsel = ($loc['locname'] == $val) ? "selected" : "";
				$accounts .= '<option value="'.$loc['locname'].'" '.$lsel.'>'.$loc['locname'].'</option>';

			}
			
			$accounts .= '</select>';
			
		}elseif($column == "status"){
			
			$sasel = ($val == "Active") ? 'selected' : '';
			$sinsel = ($val == "Inactive") ? 'selected' : '';
			
			$status = '<select class="form-control" name="ssetvalue[]" required=""><option value="Active" '.$sasel.'>Active</option><option value="Inactive" '.$sinsel.'>Inactive</option></select>';
			
		}elseif($column == "adjdirection"){
			
			$ain = ($val == 'IN') ? 'selected' : '';
			$aout = ($val == 'OUT') ? 'selected' : '';
			$accounts = '<select class="form-control" name="ssetvalue[]" required>';
			
			$accounts .= '<option value="IN" '.$ain.'>IN</option><option value="OUT" '.$aout.'>OUT</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "Type" || $column == "loctype"){
			
			$ltexsel = ($val == "External") ? 'selected' : '';
			$ltinsel = ($val == "Internal") ? 'selected' : '';			
			
			$loctype = '<select class="form-control" name="ssetvalue[]" required><option value="External" '.$ltexsel.'>External</option><option value="Internal" '.$ltinsel.'>Internal</option></select>';
			
		}elseif($column == "import_date"){
			
			$import_date = '<input type="date" class="form-control" name="ssetvalue[]" value="'.$val.'">';
			
		}elseif($column == "shippmentdate" || $column == "chepprocessdate" || $column == "reportdate" || $column == "processdate" || $column == "last_report_date" || $column == "audit_date2019"){
			
			$import_date = '<input type="date" class="form-control" name="ssetvalue[]" value="'.$val.'">';
			
		}elseif($column == "location" || $column == "flocation" || $column == "tlcoation" || $column == "tlocation"){
			
			$locations = $this->mongo_db->get_where("tbl_locations",["status"=>"Active"]);
			
			$accounts = "";
			$accounts .= '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue[]" required>';
			
			
			foreach($locations as $loc){
				
				$lsel = ($loc['locname'] == $val) ? "selected" : "";
				$accounts .= '<option value="'.$loc['locname'].'" '.$lsel.'>'.$loc['locname'].'</option>';

			}
			
			$accounts .= '</select>';
			
		}elseif($column == "item"){
			
			$locations = $this->mongo_db->get_where("tbl_items",["status"=>"Active"]);
			
			$accounts = "";
			$accounts .= '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue[]" required>';
			
			
			foreach($locations as $loc){
				
				$isel = ($loc['item_name'] == $val) ? "selected" : "";
				$accounts .= '<option value="'.$loc['item_name'].'" '.$isel.'>'.$loc['item_name'].'</option>';

			}
			
			$accounts .= '</select>';
			
		}elseif($column == "reasonforhold"){
			
			$ric = ($val == 'Reversed in Customer') ? 'selected' : '';
			$sdcu = ($val == 'Suspended During Customer Upload') ? 'selected' : '';
			$rdcu = ($val == 'Rejected During Customer Upload') ? 'selected' : '';
			$edcu = ($val == 'Error During Customer Upload') ? 'selected' : '';
			$nci = ($val == 'Need Customer ID') ? 'selected' : '';
			$dt = ($val == 'Duplicate Transaction') ? 'selected' : '';
			$is = ($val == 'International Shipment') ? 'selected' : '';
			$deost = ($val == 'Data Error on Submission to') ? 'selected' : '';
			
			$loctype = '<select class="form-control" name="ssetvalue[]" required><option value="Reversed in Customer" '.$ric.'>Reversed in Customer</option><option value="Suspended During Customer Upload" '.$sdcu.'>Suspended During Customer Upload</option><option value="Rejected During Customer Upload" '.$rdcu.'>Rejected During Customer Upload</option><option value="Error During Customer Upload" '.$edcu.'>Error During Customer Upload</option><option value="Need Customer ID" '.$nci.'>Need Customer ID</option><option value="Duplicate Transaction" '.$dt.'>Duplicate Transaction</option><option value="International Shipment" '.$is.'>International Shipment</option><option value="Data Error on Submission to" '.$deost.'>Data Error on Submission to</option></select>';
			
		}elseif($column == "uploadedetochep"){
			
			$upyes = ($val == 'Yes') ? 'selected' : '';
			$uphold = ($val == 'Hold') ? 'selected' : '';
			$upfcust = ($val == 'From Customer') ? 'selected' : '';
			$upno = ($val == 'No') ? 'selected' : '';
			
			$loctype = '<select class="form-control" name="ssetvalue[]" required><option value="Yes" '.$upyes.'>Yes</option><option value="Hold" '.$uphold.'>Hold</option><option value="From Customer" '.$upfcust.'>From Customer</option><option value="No" '.$upno.'>No</option></select>';
			
		}elseif($column == "accounts" || $column == "user"){
			
			$accounts = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="ssetvalue[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active',"appid"=>$_SESSION['appid']],[],"$this->mdb.tbl_auths");
			
			
			 foreach($users as $u){
				 
				 $usel = ($u->uname == $val) ? 'selected' : '';
				 $accounts .= '<option value="'.$u->uname.'" '.$usel.'>'.$u->uname.'</option>';
				 
			 }
			
			$accounts .= '</select>';
		}elseif($column == "issues" || $column == "returns" || $column == "transfer_ins" || $column == "transfer_outs" || $column == "adjustments" || $column == "ending_balance" || $column == "audit_count2019"){
		
			$common = '<input type="number" name="ssetvalue[]" class="form-control" value="'.$val.'">';
			
		}elseif($column == "starting_balance"){
		
			$common = '<input type="number" name="ssetvalue[]" class="form-control" value="'.$val.'" required>';
			
		}else{
			
			$common = '<input type="text" name="ssetvalue[]" class="form-control" value="'.$val.'">';
			
		}
		
		
		$fields = array("locnames"=>$locnames,"status"=>$status,"location_type"=>$loctype,"import_date"=>$import_date,"accounts"=>$accounts,"common"=>$common);
		
		$outFields = '';
		
			if($fields['locnames'] != null){
				
				$outFields = $fields['locnames']; 
				
			}elseif($fields['status'] != null){
				
				$outFields = $fields['status']; 
				
			}elseif($fields['location_type'] != null){
				
				$outFields = $fields['location_type']; 
				
			}elseif($fields['import_date'] != null){
				
				$outFields = $fields['import_date']; 
				
			}elseif($fields['accounts'] != null){
				
				$outFields = $fields['accounts']; 
				
			}elseif($fields['common'] != null){
				
				$outFields = $fields['common']; 
				
			}
		
		return $outFields;
		
	}

	public function checkValidationrules($table,$fields,$appid,$fval=""){
		
		$vrdata = $this->mongo_db->get_where("tbl_validation_rules",array("table"=>$table,"appId"=>$appid));
		
//		return($vrdata);
		
		if(count($vrdata) > 0){
			
//			$this->mongo_db->switch_db($this->database);
			if(array_search("flag", $fields)){
				$flag = $fields['flag'];
			}else{
				$flag = "";
			}
			foreach($vrdata as $vr){
				
				if($vr["status"] == "on"){
					
					if($fields[$vr['field']]){

						$conditions = json_decode(json_encode($vr['conditions']),true);
						$cdata = [];		
						foreach($conditions as $tdata){
                            if(count($tdata["cond_column"]) > '1'){
								$cdata[] = implode("||",$this->where($tdata["cond_column"],$tdata,$fields,$vr['field'],$flag));
							}else{
								$cdata[] = implode("&&",$this->where($tdata["cond_column"],$tdata,$fields,$vr['field'],$flag));
							}
							//$cdata[] = implode("&&",$this->where($tdata["cond_column"],$tdata,$fields,$vr['field']));

						}
                        //echo '<pre>';print_r($cdata);exit;
						foreach($cdata as $k => $cval){

//							return $cval;
							$cond = eval("return $cval;");

							if($cond){

								return $conditions[$k]["alertMessage"][0];
	//							return $cval;

							}
							
						}

					}
				
				}
			}
			
		}
		
		
	}
	
	public function where($wheres,$tdata,$fValue,$column,$flag=""){
		
		$where = [];
		$ii = 0;
		
		$cond_value = "";
		$value = "";
		
		foreach($wheres as $kk => $wh){

			$column = explode("-",$wh)[0];
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
				    if($flag == "excel" && $tdata["cond_column"][$kk] == "import_date-date"){
						$where[] = '('.strtotime($field_val) .' == '. strtotime($tdata["cond_value"][$kk]) .')';
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
							$where[] = '('.'"'.$fValue[$column] .'"'.' == '. '"'.$tdata["cond_value"][$kk].'"'.')';
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
                    if($flag == "excel" && $tdata["cond_column"][$kk] == "import_date-date"){
						$where[] = '('.strtotime($field_val) .' != '. strtotime($tdata["cond_value"][$kk]) .')';
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
							$where[] = '('.'"'.$fValue[$column] .'"'.'!='. '"'.$tdata["cond_value"][$kk].'"'.')';
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
				if($fValue[$column] == ""){
					$bool = "true";
				}else{
					$bool = "false";
				}
				
				$where[] = '('.$bool.')';
				//$where[] = '('."'".$fValue[$column]."'" .' == '. "".')';

			}elseif($tdata["condition"][$kk] == "is not blank"){

				/*if($fValue != ""){
					
					$where[] = ["column"=>$column,"value"=>""];
					
				}*/
                $bool = "";
				if($fValue[$column] != ""){
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
				
				$where[] = '('."'".$fValue[$column]."'" .' > '. "'".intval($tdata["cond_value"][$kk])."'".')';
				
			}elseif($tdata["condition"][$kk] == "lower than"){

				/*if($fValue < intval($tdata["cond_value"][$kk])){
					
					$where[] = ["column"=>$column,"value"=>$tdata["cond_value"][$kk]];
					
				}*/
				
				$where[] = '('."'".intval($fValue[$column])."'" .' < '. "'".intval($tdata["cond_value"][$kk])."'".')';

				
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
					$where[] = '('.$bool.')';


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
					} $bool = "false";
					
					$where[] = '('.$bool.')';

				}elseif($tdata["cond_value"][$kk] == "quarter"){

					$dates = $this->getDays("quarter");
					$start = $dates["start"];
					$end = $dates["end"];

					/*if((strtotime($fValue) >= strtotime($start)) && (strtotime($fValue) <= strtotime($end))){
					
						$where[] = ["column"=>$column,"value"=>$tdata["cond_value"][$kk]];

					}*/
					
					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}
	
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
					$where[] = '('.$bool.')';

				}

			}elseif($tdata["condition"][$kk] == "is during the previous"){

				if($tdata["cond_value"][$kk] == "days"){

					$dates = $this->getDayscount("days","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];
					
					/*if((strtotime($fValue) >= strtotime($start)) && (strtotime($fValue) <= strtotime($end))){
					
						$where[] = ["column"=>$column,"value"=>$tdata["cond_value"][$kk],"cond_days"=>$tdata["cond_days"][$kk]];

					}*/
					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}

				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					/*if((strtotime($fValue) >= strtotime($start)) && (strtotime($fValue) <= strtotime($end))){
					
						$where[] = ["column"=>$column,"value"=>$tdata["cond_value"][$kk],"cond_days"=>$tdata["cond_days"][$kk]];

					}*/
					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}

				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					/*if((strtotime($fValue) >= strtotime($start)) && (strtotime($fValue) <= strtotime($end))){
					
						$where[] = ["column"=>$column,"value"=>$tdata["cond_value"][$kk],"cond_days"=>$tdata["cond_days"][$kk]];

					}*/
					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}

				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					/*if((strtotime($fValue) >= strtotime($start)) && (strtotime($fValue) <= strtotime($end))){
					
						$where[] = ["column"=>$column,"value"=>$tdata["cond_value"][$kk],"cond_days"=>$tdata["cond_days"][$kk]];

					}*/
					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}
					
				}

			}elseif($tdata["condition"][$kk] == "is before the previous"){
               
				if($tdata["cond_value"][$kk] == "days"){
             
					$dates = $this->getDayscount("days","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];
					
					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' < '. strtotime($start) .')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($start) .')';
					}
					
                      
				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];
                    if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' < '. strtotime($start) .')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($start) .')';
					}
					

				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' < '. strtotime($start) .')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($start) .')';
					}
					

				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"minus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' < '. strtotime($start) .')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($start) .')';
					}
					
					
				}

			}elseif($tdata["condition"][$kk] == "is during the next" || $tdata["condition"][$kk] == "is after the next"){

				if($tdata["cond_value"][$kk] == "days"){

					$dates = $this->getDayscount("days","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}
					
					
					

				}elseif($tdata["cond_value"][$kk] == "weeks"){

					$dates = $this->getDayscount("weeks","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}
					
					

				}elseif($tdata["cond_value"][$kk] == "months"){

					$dates = $this->getDayscount("months","plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}
					
					

				}elseif($tdata["cond_value"][$kk] == "years" || $tdata["cond_value"][$kk] == "rolling years"){

					$dates = $this->getDayscount($tdata["cond_value"][$kk],"plus",$tdata["cond_days"][$kk]);
					$start = $dates["start"];
					$end = $dates["end"];

					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($start) .' && '. strtotime($field_val) .' <= '. strtotime($end).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($start) .' && '. strtotime($fValue[$column]) .' <= '. strtotime($end).')';
					}
					
					

				}

			}elseif($tdata["condition"][$kk] == "is before" || $tdata["condition"][$kk] == "is after"){

				$date = $tdata["cond_value"][$kk];

				if($tdata["condition"][$kk] == "is before"){

					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' < '. strtotime($date).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($date).')';
					}
					
					
					

				}elseif($tdata["condition"][$kk] == "is after"){

					if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' > '. strtotime($date).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' > '. strtotime($date).')';
					}
					
					

				}

			}elseif($tdata["condition"][$kk] == "is today or before" || $tdata["condition"][$kk] == "is today or after" || $tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time" || $tdata["condition"][$kk] == "is before current time"){

				$date = date("Y-m-d");

				if($tdata["condition"][$kk] == "is today or before" || $tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is before current time"){

					if($tdata["condition"][$kk] == "is before today" || $tdata["condition"][$kk] == "is before current time"){
						if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' < '. strtotime($date).')';
						}else{
							$where[] = '('.strtotime($fValue[$column]) .' < '. strtotime($date).')';
						}
					}else{
						if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' <= '. strtotime($date).')';
						}else{
							$where[] = '('.strtotime($fValue[$column]) .' <= '. strtotime($date).')';
						}
					}
					

					
				}elseif($tdata["condition"][$kk] == "is today or after" || $tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time"){

					if($tdata["condition"][$kk] == "is after today" || $tdata["condition"][$kk] == "is after current time"){
						if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' > '. strtotime($date).')';
						}else{
							$where[] = '('.strtotime($fValue[$column]) .' > '. strtotime($date).')';
						}
					}else{
						if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' >= '. strtotime($date).')';
						}else{
							$where[] = '('.strtotime($fValue[$column]) .' >= '. strtotime($date).')';
						}
					}
					

				}

			}elseif($tdata["condition"][$kk] == "is today"){

				$date = date("Y-m-d");

				if($flag == "excel"){
						$where[] = '('.strtotime($field_val) .' == '. strtotime($date).')';
					}else{
						$where[] = '('.strtotime($fValue[$column]) .' == '. strtotime($date).')';
					}
				  

			}

		}
		
			return ($where);
		
	}
	
	
	function startsWith($string,$startString) 
	{ 
		$len = strlen($startString); 
		return (substr($string, 0, $len) === $startString); 
	} 
	
	function endsWith($string,$endString) 
	{ 
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
    public function getUpdatedFieldsOperatorsUpdated($col="",$dType="",$tbl="",$soperator="",$sfield="",$refopid="",$cnameref=""){
		
		$this->mongo_db->switch_db($this->database);
		
		$column = $col;
		$datatype = $dType;
		$table = $tbl;
		
		$accounts = "";
		$locnames = "";
		$status = "";
		$loctype = "";
		$import_date = "";
		$common = "";
		
		if($column == "locname" && $table != "tbl_locations"){
			
			$locations = $this->mongo_db->get_where("tbl_locations",["status"=>"Active"]);
			
			$accounts = "";
			$accounts .= '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$cnameref.'[]" required>';
			
			
			foreach($locations as $loc){
				
				$lsel = ($loc['locname'] == $sfield) ? "selected" : "";
				$accounts .= '<option value="'.$loc['locname'].'" '.$lsel.'>'.$loc['locname'].'</option>';

			}
			
			$accounts .= '</select>';
			
		}elseif($column == "status"){
			
			$sasel = ($sfield == "Active") ? 'selected' : '';
			$sinsel = ($sfield == "Inactive") ? 'selected' : '';
			
			$status = '<select class="form-control" name="cond_value'.$cnameref.'[]" required=""><option value="Active" '.$sasel.'>Active</option><option value="Inactive" '.$sinsel.'>Inactive</option></select>';
			
		}elseif($column == "Type" || $column == "loctype"){
			
			$ltint = ($sfield == 'Internal') ? 'selected' : '';
			$ltext = ($sfield == 'External') ? 'selected' : '';
			
			$loctype = '<select class="form-control" name="cond_value'.$cnameref.'[]" required><option value="External" '.$ltext.'>External</option><option value="Internal" '.$ltint.'>Internal</option></select>';
			
		}elseif($column == "reasonforhold"){
			
			$ric = ($sfield == 'Reversed in Customer') ? 'selected' : '';
			$sdcu = ($sfield == 'Suspended During Customer Upload') ? 'selected' : '';
			$rdcu = ($sfield == 'Rejected During Customer Upload') ? 'selected' : '';
			$edcu = ($sfield == 'Error During Customer Upload') ? 'selected' : '';
			$nci = ($sfield == 'Need Customer ID') ? 'selected' : '';
			$dt = ($sfield == 'Duplicate Transaction') ? 'selected' : '';
			$is = ($sfield == 'International Shipment') ? 'selected' : '';
			$deost = ($sfield == 'Data Error on Submission to') ? 'selected' : '';
			
			$loctype = '<select class="form-control" name="cond_value'.$cnameref.'[]" required><option value="Reversed in Customer" '.$ric.'>Reversed in Customer</option><option value="Suspended During Customer Upload" '.$sdcu.'>Suspended During Customer Upload</option><option value="Rejected During Customer Upload" '.$rdcu.'>Rejected During Customer Upload</option><option value="Error During Customer Upload" '.$edcu.'>Error During Customer Upload</option><option value="Need Customer ID" '.$nci.'>Need Customer ID</option><option value="Duplicate Transaction" '.$dt.'>Duplicate Transaction</option><option value="International Shipment" '.$is.'>International Shipment</option><option value="Data Error on Submission to" '.$deost.'>Data Error on Submission to</option></select>';
			
		}elseif($column == "uploadedetochep"){
			
			$upyes = ($sfield == 'Yes') ? 'selected' : '';
			$uphold = ($sfield == 'Hold') ? 'selected' : '';
			$upfcust = ($sfield == 'From Customer') ? 'selected' : '';
			$upno = ($sfield == 'No') ? 'selected' : '';
			
			$loctype = '<select class="form-control" name="cond_value'.$cnameref.'[]" required><option value="Yes" '.$upyes.'>Yes</option><option value="Hold" '.$uphold.'>Hold</option><option value="From Customer" '.$upfcust.'>From Customer</option><option value="No" '.$upno.'>No</option></select>';
			
		}elseif($column == "import_date" || $column == "shippmentdate" || $column == "reportdate" || $column == "processdate" || $column == "chepprocessdate" || $column == "last_report_date" || $column == "audit_date2019"){
			
			$import_date = '<input type="date" class="form-control" name="cond_value'.$cnameref.'[]" value="'.$sfield.'">';
			
		}elseif($column == "accounts" || $column == "user"){
			
			$accounts = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$cnameref.'[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active',"appid"=>$_SESSION['appid']],[],"$this->mdb.tbl_auths");
			
			 foreach($users as $u){
				 
				 $usel = ($sfield == $u->uname) ? 'selected' : '';
				 
				 $accounts .= '<option value="'.$u->uname.'" '.$usel.'>'.$u->uname.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "item"){
			
			$items = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$cnameref.'[]" required>';
			
			$items_res = $this->admin->getArray("",["status"=>'Active'],[],"$this->database.tbl_items");
			
			 foreach($items_res as $u){
				 $item_select = ($sfield == $u->item_name) ? 'selected' : '';
				 $items .= '<option value="'.$u->item_name.'" '.$item_select.'>'.$u->item_name.'</option>';
				 
			 }
			
			$items .= '</select>';
			
		}elseif($column == "flocation" || $column == "tlcoation" || $column == "location" || $column == "tlocation" && $table != "tbl_locations"){
			
			$loc_tbl = $this->mongo_db->where(["status"=>'Active'])->get("tbl_locations");
			$flocation = "";			
			$flocation = '<select class="select2 form-control" style="height: 35px !important;" data-placeholder="Choose ..." name="cond_value'.$cnameref.'[]" required>';			
			foreach($loc_tbl as $loc){
                $floc_select = ($sfield == $loc['locname']) ? 'selected' : 'none';
				$flocation .= '<option value="'.$loc['locname'].'" '.$floc_select.'>'.$loc['locname'].'</option>';
			}
			$flocation .= '</select>';
		
		}else{
			
			$common = '<input type="text" name="cond_value'.$cnameref.'[]" class="form-control" value="'.$sfield.'">';
			
		}
		
		$operators = $this->common->getConditionbydatatype($datatype);
		
		$oper = '<select name="condition'.$cnameref.'[]" class="form-control updateonchangeConditionUpdated" rCount="'.$cnameref.'" opid="'.$refopid.'">';
		
		foreach($operators as $op){
			
			$selected = ($soperator == $op) ? 'selected' : '';
			
			$oper .= '<option value="'.$op.'" '.$selected.'>'.$op.'</option>';
			
		}
		
		$oper .= '</select>';
		
		$fields = array("locnames"=>$locnames,"status"=>$status,"location_type"=>$loctype,"import_date"=>$import_date,"accounts"=>$accounts,"items"=>$items,"flocation"=>$flocation,"common"=>$common);
		
		$outFields = '';
		
			if($fields['locnames'] != null){
				
				$outFields = $fields['locnames']; 
				
			}elseif($fields['status'] != null){
				
				$outFields = $fields['status']; 
				
			}elseif($fields['location_type'] != null){
				
				$outFields = $fields['location_type']; 
				
			}elseif($fields['import_date'] != null){
				
				$outFields = $fields['import_date']; 
				
			}elseif($fields['accounts'] != null){
				
				$outFields = $fields['accounts']; 
				
			}elseif($fields['items'] != null){
				
				$outFields = $fields['items']; 
				
			}elseif($fields['flocation'] != null){
				
				$outFields = $fields['flocation']; 
				
			}elseif($fields['common'] != null){
				
				$outFields = $fields['common']; 
				
			}
		
		return ["fields"=>$outFields,"operators"=>$oper];
	}
	
	
	public function exportAllfilestoexcel($table,$filename){
		
	   $this->mongo_db->switch_db($this->database);
		
	   	if($table == "tbl_touts"){
			$data = $this->mongo_db->aggregate($table,[
				['$sort'=>["_id"=>-1]],
				['$match' => ['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active"]],
				/*['$match' => ['$or' => 
							  	[['tlcoation.status'=>"Active"],
							 	['tlcoation.locname'=>""],
							 	['tlcoation.locname'=>null]]
							 ]],*/
//				['$count'=>"total"],

			]);
			
		}elseif($table == "tbl_inventory"){
				
 		    $item = urldecode($this->uri->segment(6));
			$data = $this->mongo_db->aggregate("$table",[
				['$sort'=>["_id"=>-1]],
				['$match' => ['item.item_name'=>"$item"]],
				['$match' => ['item.status'=>"Active","locname.status"=>"Active"]],
//				['$count'=>"total"],
			]);

		}elseif($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){

			$data = $this->mongo_db->aggregate($table,[
				['$sort'=>["_id"=>-1]],
				['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
//				['$count'=>"total"],
			]);
		}else{
			
			$data = $this->mongo_db->order_by(["_id"=>-1])->get($table);

		}
		

		
//		echo count($data);
//	   exit();	
		
		
	   // file name 
	   $filename = $filename.'.csv'; 
	   header("Content-Description: Export Excel"); 
	   header("Content-Disposition: attachment; filename=$filename"); 
	   header("Content-Type: application/csv; ");

	   // get data 
		
	   // file creation 
	   $file = fopen('php://output', 'w');

	   $header = $this->mongo_db->get_where("settings",array("table"=>$table))[0]; 
		
	   $headers = $this->getexcellabels($header['labels'],$table); 	
	   $columns = $this->getexcelcolumns($header['columns'],$table);
		
//	   print_r($columns);
//	   exit();	
		
		
	   fputcsv($file, $headers);
		
	   $fdata = [];	
	
	   foreach ($data as $key=>$line){ 
		 
		 unset($line["deleted"]);
		 unset($line["appId"]);
		 unset($line["cdate"]);
		 unset($line["created_date"]);
		   
		   	$ndata = [];
		   
		    foreach($columns as $label){
				
				if($label == "accounts"){
					
					if(is_array($line[$label])){
						$ndata[$label] = implode(",",$line[$label]);
					}else{
						$ndata[$label] = $line[$label];
					}
					
				}else{
					
					if($label == "tlocation" || ($label == "locname" && $table == "tbl_inventory") || $label == "flocation" || $label == "tlcoation"){

						$ndata[$label] = $line[$label]->locname;	
						
					}elseif($label == "item"){
						
						$ndata[$label] = $line[$label]->item_name;
						
					}else{
						
						$ndata[$label] = $line[$label];
						
					}
				
				
				}
				
			}
		    
		 	fputcsv($file,$ndata);	
//		print_r($ndata);
	   }
		
		
		
		
	   fclose($file); 
	   exit; 
		
	}

	
	
	public function exportAllfilestoexcel_Filter($ids,$table,$filename){
		
	   $this->mongo_db->switch_db($this->database);
	   // file name 
	   $filename = $filename.'.csv'; 
	   header("Content-Description: Export Excel"); 
	   header("Content-Disposition: attachment; filename=$filename"); 
	   header("Content-Type: application/csv; ");

	   // get data 
	   $data = $this->mongo_db->where_in("_id", $ids)->get($table);
	   
	   // file creation 
	   $file = fopen('php://output', 'w');

	   $header = $this->mongo_db->get_where("settings",array("table"=>$table))[0]; 

	   $headers = $this->getexcellabels($header['labels'],$table); 	
	   $columns = $this->getexcelcolumns($header['columns'],$table);
		
	   fputcsv($file, $headers);
		
	   $fdata = [];	
	   foreach ($data as $key=>$line){ 
		 
		 unset($line["deleted"]);
		 unset($line["appId"]);
		 unset($line["cdate"]);
		 unset($line["created_date"]);
		   
		   	$ndata = [];
		   
		    foreach($columns as $label){
				
				if($label == "accounts"){
					
					if(is_array($line[$label])){
						$ndata[$label] = implode(",",$line[$label]);
					}else{
						$ndata[$label] = $line[$label];
					}
					
				}else{
				
//					$ndata[$label] = $line[$label];
					
					if($label == "tlocation" || ($label == "locname" && $table == "tbl_inventory") || $label == "flocation" || $label == "tlcoation"){

						$ndata[$label] = $line[$label]->locname;	
						
					}elseif($label == "item"){
						
						$ndata[$label] = $line[$label]->item_name;
						
					}else{
						
						$ndata[$label] = $line[$label];
						
					}
				
				}
			}
		    
		 	fputcsv($file,$ndata);	
		
	   }
		
	   fclose($file); 
	   exit; 
		
	}
}