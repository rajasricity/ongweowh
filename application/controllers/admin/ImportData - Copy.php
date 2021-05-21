<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require FCPATH.'vendor/autoload.php';
class ImportData extends CI_Controller {
	
	public function __construct(){
		
		parent::__construct();
		
		if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}
		
		$appId = $_SESSION['appid'];
		$this->database = $this->admin->getAppdb();
		$this->mdb = mongodb;
		
	}
	
	public function uploadFile(){
		$ext = pathinfo($_FILES['ldata']['name'], PATHINFO_EXTENSION);
		if($ext == "xlsx"){
			$target = "uploads/exceldata/";
			$file = date("Ymdhis", time()).".".$ext;
			$n=move_uploaded_file($_FILES['ldata']['tmp_name'], $target.$file);
			if($n){
				$path = FCPATH.'uploads/exceldata/'.$file;

				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			 	$spreadsheet = $reader->load($path)->getSheet(0);
			 	$worksheet = $spreadsheet;
			 	$highestRow = $worksheet->getHighestRow();
			 	$highestColumn = $worksheet->getHighestColumn();
			 	$headings = $worksheet->rangeToArray('A1:' . $highestColumn . 1,NULL,TRUE,FALSE)[0];
			 	$records = [];
			 	if($highestRow > 12){
			 		$m=12;
			 	}else{
			 		$m = $highestRow;
			 	}
			 	for($i=2;$i <= $m;$i++){
			 		$data=[];
			 		foreach($headings as $key=>$value){
			 			$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();
			 			array_push($data, $new_val);
			 		}
			 		array_push($records, $data);
			 	}
				echo json_encode(array("Headings"=>$headings,"Records"=>$records,"Status"=>"Success","appId"=>$this->input->post("appId"),"headers"=>$this->input->post("headers"),"field"=>$this->input->post("field"),"File"=>$file));
			}else{
				echo json_encode(array("Stauts"=>"Wrong","Message"=>"Failed to upload excel document"));
			}
			
		}else{
			echo json_encode(array("Stauts"=>"Wrong","Message"=>"Please upload xlsx file"));
		}
	}

	public function submitStep2(){
//		
//		print_r($this->input->post());
//			exit();
//		
		$table = $this->input->post("table");
		$appId = $this->input->post("app");
		$field = $this->input->post("field");
		$column = $this->input->post("column");
		$file = $this->input->post("file");
		$row = $this->input->post("row");
		
		if($table == 'tbl_items'){
			$cols = ["item_code","item_name","status","appId","deleted","created_date"];
			$fields = ["item_code","item_name","status","appId","deleted","created_date"];
			$fields1 = ["item_code","item_name","status"];
		}else if($table == 'tbl_locations'){
			$cols = ["nameid","locname","loccode","address","city","state","zip","country","status","Type","import_date","accounts","notes","appId","deleted","cdate"];
			$fields = ["nameid","locname","loccode","address","city","state","zip","country","status","Type","import_date","accounts","notes","appId","deleted","cdate"];
			$fields1 = ["nameid","locname","loccode","address","city","state","zip","country","status","Type","import_date","accounts","notes"];
		}else if($table == 'tbl_touts'){
			$cols = ["shipperpo","shippmentdate","pronum","reference","item","flocation","flcoationcode","tlcoation","tlocationcode","quantity","reportdate","user","processdate","chepprocessdate","chepumi","uploadedetochep","reasonforhold","appId","deleted","cdate"];
			$fields = ["shipperpo","shippmentdate","pronum","reference","item","flocation","flcoationcode","tlcoation","tlocationcode","quantity","reportdate","user","processdate","chepprocessdate","chepumi","uploadedetochep","reasonforhold","appId","deleted","cdate"];
			$fields1 =["shipperpo","shippmentdate","pronum","reference","item","flocation","flcoationcode","tlcoation","tlocationcode","quantity","item","reportdate","user","processdate","chepprocessdate","chepumi","uploadedetochep","reasonforhold"];
		}else if($table == 'tbl_issues' || $table == 'tbl_returns'){
			$cols = ["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","umi","appId","deleted","cdate"];
			$fields = ["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","umi","appId","deleted","cdate"];
			$fields1 =["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","umi"];
		}
		else if($table == 'tbl_adjustments'){
			$cols = ["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","adjdirection","umi","appId","deleted","cdate"];
			$fields = ["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","adjdirection","umi","appId","deleted","cdate"];
			$fields1 =["chepreference","ongreference","shippmentdate","quantity","item","tlocation","tlcoationcode","chepprocessdate","adjdirection","umi"];
		}else if($table == 'tbl_inventory'){
			$cols = ["location","locname","loccode","loctype","notes","last_report_date","starting_balance","issues","returns","transfer_ins","transfer_outs","adjustments","ending_balance","audit_date2019","audit_count2019","item","appId","deleted","cdate"];
			$fields = ["location","locname","loccode","loctype","notes","last_report_date","starting_balance","issues","returns","transfer_ins","transfer_outs","adjustments","ending_balance","audit_date2019","audit_count2019","item","appId","deleted","cdate"];
			$fields1 =["location","locname","loccode","loctype","notes","last_report_date","starting_balance","issues","returns","transfer_ins","transfer_outs","adjustments","ending_balance","audit_date2019","audit_count2019","item"];
		}

		if(count(array_keys($column, "0")) == count($column)){
			$cnt = 0;
		}else{
			$cnt = 1;
		}


		$path = FCPATH.'uploads/exceldata/'.$file;
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$spreadsheet = $reader->load($path)->getSheet(0);
		$worksheet = $spreadsheet;
		$highestRow = $worksheet->getHighestRow();
		$highestColumn = $worksheet->getHighestColumn();
		$headings = $worksheet->rangeToArray('A1:' . $highestColumn . 1,NULL,TRUE,FALSE)[0];
		$mng = $this->admin->Mconfig();
		$database=mongodb."_".$appId.".".$table;
		$dataitem=mongodb."_".$appId.".tbl_items";
		$locdatabase = mongodb."_".$appId.".tbl_locations";


		if($cnt == "0"){ //Cnt Open
			 	$records = [];
			 	$error=[];
			 	$warnings = [];
			 	if($row == 0){ $ro=1; }else{ $ro = $row+1; }

			 	$aid = 0;
			
			if($table == "tbl_locations"){
				
				$autoid = explode("_",$this->admin->insert_id($table,$this->database,"locid"))[1];	
				
			}else{
				
				$autoid = explode("_",$this->admin->insert_id($table,$this->database))[1];
				
			}
			
			$starting_id = $this->admin->insert_id($table,$this->database);
			 	
			 	for($i=$ro;$i <= $highestRow;$i++){ //Row wise Start
			 		$data=[];
			 		
			 		if($table == 'tbl_inventory'){
						
						array_push($data,"location");

			 			foreach($headings as $key=>$value){
			 			$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();
			 			array_push($data, (string)$new_val);
			 			if($key == "6"){
			 			array_push($data, "0");
			 			array_push($data, "0");	
			 			array_push($data, "0");	
			 			array_push($data, "0");	
			 			array_push($data, "0");	
			 			array_push($data, "0");		
			 				}
			 			}

			 		}else{
						
						if($table == "tbl_locations"){
						
							array_push($data,"nameid");

						}
						
			 			foreach($headings as $key=>$value){
							$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();
							array_push($data, (string)$new_val);
						}

			 		}

			 		if($table == 'tbl_issues' || $table == 'tbl_returns' || $table == 'tbl_adjustments' ){
			 			if(strpos($data[2],"-")){
							$mp=explode("-",$data[2]);
							$ndate = $mp[1]."-".$mp[0]."-".$mp[2];
						}else if(strpos($data[2],"/")){
							$mp=explode("/",$data[2]);
							$ndate = $mp[1]."-".$mp[0]."-".$mp[2];
						}
			 		
			 				$vd=date("Y", strtotime($ndate));

				 		if($vd < 2015){
				 			array_push($error,array("Msg"=>"Shipment Date ".$data[2]." for Vendor Reference ".$data[0]." must be Greater than year 2015","Error"=>$data[2]));
				 		}
				 		/*if($this->admin->getCount($mng,$database,['chepreference'=>$data[0]],[])>0){
				 			array_push($error,array("Msg"=>"Duplicate Customer Reference","Error"=>$data[0]));	
				 		}
				 		if($this->admin->getCount($mng,$database,['ongreference'=>$data[1]],[])>0){
				 			array_push($error,array("Msg"=>"Duplicate Ongweoweh Reference","Error"=>$data[1]));	
				 		}*/
						
						if(trim($data[0]) == ""){
							array_push($error,array("Msg"=>"Customer Reference should not be blank for To Location $data[5]","Error"=>$data[0]));	
						}
						
						if(trim($data[1]) == ""){
							array_push($error,array("Msg"=>"Ongweoweh Reference should not be blank for To Location $data[5]","Error"=>$data[1]));	
						}
						
				 		if($this->admin->getCount($mng,$dataitem,['item_name'=>$data[4]],[]) == 0){
				 			array_push($error,array("Msg"=>"The item ".$data[4]." for Vendor Referene ".$data[0]." does not exist","Error"=>$data[4]));	
				 		}
						
				 		if(is_numeric($data[3]) && $data[3] != 0){ }else{
				 			array_push($error,array("Msg"=>"quantity ".$data[3]." cannot be a Zero (or) Text, it must be > 1 (or) < 1","Error"=>$data[3]));	
				 		}

				 		/*if($this->admin->getCount($mng,$locdatabase,['locname'=>$data[5]],[]) == 0 || $data[5] == ''){
				 			array_push($error,array("Msg"=>"To Location ".$data[5]." for Vendor Reference ".$data[0]." does not exist","Error"=>$data[5]));	
				 		}*/

				 		if($this->admin->getCount($mng,$locdatabase,['loccode'=>$data[6]],[]) == 0 || $data[6] == ''){
				 			array_push($error,array("Msg"=>"To Location Code".$data[6]." for Vendor Reference ".$data[0]." does not exist","Error"=>$data[5]));	
				 		}
						
						if($table == 'tbl_issues' || $table == 'tbl_returns'){
							
							if($data[8] != ''){
								if($this->admin->getCount($mng,$database,['umi'=>$data[8]],[]) > 0){
									array_push($error,array("Msg"=>"Duplicate UMI ".$data[8]." for To Location ".$data[5]."","Error"=>$data[8]));	
								}
							}
							
						}else{
							if($data[9] != ''){
								if($this->admin->getCount($mng,$database,['umi'=>$data[9]],[]) > 0){
									array_push($error,array("Msg"=>"Duplicate UMI ".$data[9]." for To Location ".$data[5]."","Error"=>$data[9]));	
								}
							}
							
						}

			 		}

			 		if($table == 'tbl_touts'){
			 		 	if(strpos($data[1],"-")){
			 			$mp=explode("-",$data[1]);
			 			$ndate = $mp[1]."-".$mp[0]."-".$mp[2];
					 		}else if(strpos($data[1],"/")){
					 			$mp=explode("/",$data[1]);
					 			$ndate = $mp[1]."-".$mp[0]."-".$mp[2];
					 		}
			 		
			 			$vd=date("Y", strtotime($ndate));
			 			if($vd < 2015){
				 			array_push($error,array("Msg"=>"Shipment Date ".$data[1]." for Shipper PO ".$data[0]." must be Greater than year 2015","Error"=>$data[1]));
				 		}
				 		if($this->admin->getCount($mng,$database,['shipperpo'=>$data[0]],[])>0){
				 			array_push($warnings,array("Msg"=>"Shipper PO ".$data[0]." exists for From Location $data[5]","Error"=>$data[0]));	
				 		}
						
						 if($data[14] != ''){
							if($this->admin->getCount($mng,$database,['chepumi'=>$data[14]],[]) > 0){
								array_push($error,array("Msg"=>"Duplicate UMI ".$data[14]." for From Location ".$data[5]."","Error"=>$data[14]));	
							}
						 }
						
						if(trim($data[0]) == ""){
							array_push($error,array("Msg"=>"Shipper PO Cannot be blank","Error"=>$data[0]));	
						}
				 		if($this->admin->getCount($mng,$dataitem,['item_name'=>$data[4]],[]) == 0){
				 			array_push($error,array("Msg"=>"The Item ".$data[4]." for Shipper PO ".$data[0]." does not exist","Error"=>$data[4]));	
				 		}

				 		/*if($this->admin->getCount($mng,$locdatabase,['locname'=>$data[5]],[]) == 0){
				 			array_push($error,array("Msg"=>"From Location ".$data[5]." for Shipper PO ".$data[0]." does not exist","Error"=>$data[4]));	
				 		}*/

				 		/*if($this->admin->getCount($mng,$locdatabase,['loccode'=>$data[6],'locname'=>$data[5]],[]) == 0){
				 			array_push($error,array("Msg"=>"From Location ".$data[5]." for From Location Code ".$data[6]." does not exist","Error"=>$data[4]));	
				 		}*/
						
						if($this->admin->getCount($mng,$locdatabase,['loccode'=>$data[6]],[]) == 0){
				 			array_push($error,array("Msg"=>"From Location Code ".$data[6]." for Shipper PO ".$data[0]." does not exist","Error"=>$data[6]));	
				 		}

				 		/*if($data[7] == '' || $data[7] == 'null'){
				 			array_push($warnings,array("Msg"=>"To Location ".$data[7]." for Shipper PO ".$data[0]." does not exist"));	
				 		}*/

						if(trim($data[8]) == ""){
							
							array_push($error,array("Msg"=>"To Location Code is blank for Shipper PO ".$data[0]."","Error"=>""));
							
						}else{
						
							if($this->admin->getCount($mng,$locdatabase,['loccode'=>$data[8]],[]) == 0){
								array_push($warnings,array("Msg"=>"To Location Code ".$data[8]." for Shipper PO ".$data[0]." does not exist","Error"=>$data[8]));	
							}

						}

				 		if(is_numeric($data[9]) && $data[9] != 0){ }else{
				 			array_push($error,array("Msg"=>"Quantity ".$data[9]." cannot be a Zero (or) Text, it must be > 1 (or) < 1","Error"=>$data[9]));	
				 		}
			 				
			 		}

			 		if($table == 'tbl_items'){
				 		if($this->admin->getCount($mng,$database,['item_code'=>$data[0]],[])>0){
				 			array_push($error,array("Msg"=>"The Item code ".$data[0]." exists","Error"=>$data[0]));	
				 		}
			 		}

			 		if($table == 'tbl_inventory'){
		
				 		if($this->admin->getCount($mng,$dataitem,['item_name'=>$data[15]],[['projection' => ['_id' => 1]]]) == 0){
				 			array_push($error,array("Msg"=>"The item ".$data[15]." for Location Code ".$data[2]." does not exist","Error"=>$data[15]));	
				 		}


				 		/*if($this->admin->getCount($mng,$locdatabase,['locname'=>$data[1]],['projection' => ['_id' => 1]]) == 0){
				 			array_push($error,array("Msg"=>"Location Name ".$data[1]." does not exist","Error"=>$data[1]));	
				 		}*/

				 		if($this->admin->getCount($mng,$locdatabase,['loccode'=>$data[2]],['projection' => ['_id' => 1]]) == 0){
				 			array_push($error,array("Msg"=>"Location Code ".$data[2]." does not exist","Error"=>$data[1]));	
				 		}

				 		if(is_numeric($data[6])){ 
						
							/*if(intval($data[6]) < 0){
								
								array_push($error,array("Msg"=>"Starting Balance ".$data[6]." cannot be a Negative value (or) Text, it must be 0 (or) >= 1","Error"=>$data[6]));	
								
							}*/
						
						}else{
				 			array_push($error,array("Msg"=>"Starting Balance ".$data[6]." cannot be a Text, it must be 0 (or) >= 1 (or) <= 1","Error"=>$data[6]));	
				 		}
			 				
			 		}

			 		if($table == 'tbl_locations'){
						
			 			if($this->admin->getCount($mng,$database,['loccode'=>$data[2]],[])>0){
							array_push($error,array("Msg"=>"The Location code ".$data[2]." exists.","Error"=>$data[2]));	
				 		}
						
						if(trim($data[2]) == ""){
							array_push($error,array("Msg"=>"Location Code should not be blank","Error"=>$data[2]));	
						}
						
						/*if(trim($data[1]) == ""){
							array_push($error,array("Msg"=>"Location Name should not be blank","Error"=>$data[1]));	
						}*/
				 		
			 		}
					
			 		array_push($data, $appId);
			 		array_push($data, "0");
			 		array_push($data, date("Y-m-d H:i:s A"));
			 		$ff = array_combine($fields, $data);
			 		
					
					$ff['flag']="excel";
					
					
					if($table == 'tbl_inventory'){
			 			$ff['ending_balance'] = $ff['starting_balance']+$ff['issues']+$ff['returns']+$ff['transfer_ins']-$ff['transfer_outs']+$ff['adjustments'];
			 		}
					
					if($table == "tbl_locations"){
						
						$ff["nameid"] = $ff["locname"]." - ".$ff["loccode"];
						
					}
					
					if($table == "tbl_inventory"){
						
						$ff["location"] = $ff["locname"]." - ".$ff["loccode"];
						
					}
					
					if($table == 'tbl_issues' || $table == 'tbl_returns' || $table == 'tbl_adjustments'){
						
						if(strpos($data[7],"-")){
							$mp=explode("-",$data[7]);
							$ndate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[7],"/")){
							$mp=explode("/",$data[7]);
							$ndate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							$ndate = intval($data[7]);
						}
						
						$ff['chepprocessdate'] = ((gettype($ndate) == "integer") || ($data[7] == "") || ($data[7] == " ")) ? "" : $ndate;
						$ff['quantity'] = ($ff['quantity'] != "") ? intval($ff['quantity']) : intval();

						if($ff['shippmentdate'] != ""){
							$ff['shippmentdate'] = $this->common->getConverteddate($ff['shippmentdate']);
						}else{
							$ff['shippmentdate'] = "";
						}
						
						if($ff['chepprocessdate'] != ""){
							$ff['chepprocessdate'] = $this->common->getConverteddate($ff['chepprocessdate']);
						}else{
							$ff['chepprocessdate'] = "";
						}
						
//						$tlocdata = $this->admin->getRow("",["locname"=>$ff["tlocation"]],[],"$this->database.tbl_locations");
//						$itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");
						
//						$ff["tlocation"] = (string) $tlocdata->_id;
//						$ff["item"] = (string) $itemdata->_id;

						
					}
					
					if($table == 'tbl_locations'){
					    
						if(strpos($data[10],"-")){
							$mp=explode("-",$data[10]);
							$ndate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[10],"/")){
							$mp=explode("/",$data[10]);
							$ndate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$ndate = intval($data[10]);
						}
						
						$ff['import_date'] = ((gettype($ndate) == "integer") || ($data[10] == "") || ($data[10] == " ")) ? "" : $ndate;
						$ff['accounts'] = explode(",",$data[11]);
						if($data[10] != ""){
							$ff['import_time'] = explode(" ",$ff["import_date"])[1];
						    $ff['import_date'] = $this->common->getConverteddate(explode(" ",$ff["import_date"])[0]);
						}else{
							$ff['import_time'] = "";
						    $ff['import_date'] = "";
						}
						
						
					}
					
					if($table == 'tbl_touts'){
					
						if(strpos($data[10],"-")){
							$mp=explode("-",$data[10]);
							$rdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[10],"/")){
							$mp=explode("/",$data[10]);
							$rdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$rdate = intval($data[10]);
						}
						
						if(strpos($data[12],"-")){
							$mp=explode("-",$data[12]);
							$pdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[12],"/")){
							$mp=explode("/",$data[12]);
							$pdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$pdate = intval($data[12]);
						}
						
						if(strpos($data[13],"-")){
							$mp=explode("-",$data[13]);
							$cpdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[13],"/")){
							$mp=explode("/",$data[13]);
							$cpdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$cpdate = intval($data[13]);
						}
						
						$ff['reportdate'] = ((gettype($rdate) == "integer") || ($data[10] == "") || ($data[10] == " ")) ? date("Y-m-d") : $rdate;
						$ff['processdate'] = ((gettype($pdate) == "integer") || ($data[12] == "") || ($data[12] == " ")) ? "" : $pdate;
						$ff['chepprocessdate'] = ((gettype($cpdate) == "integer") || ($data[13] == "") || ($data[13] == " ")) ? "" : $cpdate;
						$ff['quantity'] = ($ff['quantity'] != "") ? intval($ff['quantity']) : intval();

						if($ff['shippmentdate'] != ""){
							$ff['shippmentdate'] = $this->common->getConverteddate($ff['shippmentdate']);
						}else{
							$ff['shippmentdate'] = "";
						}
						
						if($ff['reportdate'] != ""){
							$ff['reportdate'] = $this->common->getConverteddate($ff['reportdate']);
						}else{
							$ff['reportdate'] = "";
						}
						
						if($ff['processdate'] != ""){
							$ff['processdate'] = $this->common->getConverteddate($ff['processdate']);
						}else{
							$ff['processdate'] = "";
						}
						
						if($ff['chepprocessdate'] != ""){
							$ff['chepprocessdate'] = $this->common->getConverteddate($ff['chepprocessdate']);
						}else{
							$ff['chepprocessdate'] = "";
						}
						
						$ff["uploadedetochep"] = (trim($ff["uploadedetochep"]) == "") ? "No" : $ff["uploadedetochep"];
						
//						$flocdata = $this->admin->getRow("",["locname"=>$ff["flocation"]],[],"$this->database.tbl_locations");
//						$tlocdata = $this->admin->getRow("",["locname"=>$ff["tlcoation"]],[],"$this->database.tbl_locations");
//						$itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");
//						
//						$ff["flocation"] = (string) $flocdata->_id;
//						$ff["tlcoation"] = (string) $tlocdata->_id;
//						$ff["item"] = (string) $itemdata->_id;
						
						
					}
					
					if($table == 'tbl_inventory'){
					
						if(strpos($data[5],"-")){
							$mp=explode("-",$data[5]);
							$lrdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[5],"/")){
							$mp=explode("/",$data[5]);
							$lrdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$lrdate = intval($data[5]);
						}
						
						if(strpos($data[7],"-")){
							$mp=explode("-",$data[7]);
							$addate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[7],"/")){
							$mp=explode("/",$data[7]);
							$addate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$addate = intval($data[7]);
						}
						
						$ff['last_report_date'] = ((gettype($lrdate) == "integer") || ($data[5] == "") || ($data[5] == " ")) ? "" : $lrdate;
						$ff['audit_date2019'] = ((gettype($addate) == "integer") || ($data[7] == "") || ($data[7] == " ")) ? "" : $addate;
						
						if($ff['last_report_date'] != ""){
							$ff['last_report_date'] = $this->common->getConverteddate($ff['last_report_date']);
						}else{
							$ff['last_report_date'] = "";
						}
						
						if($ff['audit_date2019'] != ""){
							$ff['audit_date2019'] = $this->common->getConverteddate($ff['audit_date2019']);
						}else{
							$ff['audit_date2019'] = "";
						}

						$ff['starting_balance'] = ($ff['starting_balance'] != "") ? intval($ff['starting_balance']) : intval();
						$ff['issues'] = 0;
						$ff['returns'] = 0;
						$ff['transfer_ins'] = 0;
						$ff['transfer_outs'] = 0;
						$ff['adjustments'] = 0;						
						$ff['ending_balance'] = ($ff['starting_balance'] != "") ? intval($ff['starting_balance']) : intval();;
						$ff['audit_count2019'] = ($ff['audit_count2019'] != "") ? intval($ff['audit_count2019']) : intval();
						
//						$tlocdata = $this->admin->getRow("",["locname"=>$ff["locname"]],[],"$this->database.tbl_locations");
//						$itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");
//						
//						$ff["locname"] = (string) $tlocdata->_id;
//						$ff["item"] = (string) $itemdata->_id;

						
					}

			 		/*$valRulescheck = $this->common->checkValidationrules($table,$ff,$appId,"");
					if($valRulescheck){
						array_push($error,array("Msg"=>$valRulescheck,"Error"=>""));	
					}*/

			
					$conRulescheck = $this->conditions_model->checkConditionrules($table,$ff,$appId,"");
					
					if(count($conRulescheck) > 0){

						foreach($conRulescheck as $con){

							$ff[$con['column']] = $con['value'];

						}

					} 
					
					foreach($ff as $fk => $fval){
						
						if(($fk != "quantity") && ($fk != "accounts") && ($fk != "chepprocessdate") && ($fk != "shippmentdate") && ($fk != "import_date") && ($fk != "reportdate") && ($fk != "processdate") && ($fk != "last_report_date") && ($fk != "audit_date2019") && ($fk != "flocation") && ($fk != "tlcoation") && ($fk != "item") && ($fk != "tlocation") && ($fk != "locname")){
							
							$ff[$fk] = trim($fval);
							
						}
						
					}

					

// updating location and item values					
					
					if($table == "tbl_touts"){
						
						// $flocdata = $this->admin->getRow("",["loccode"=>$ff["flcoationcode"]],[],"$this->database.tbl_locations");
						// $tlocdata = $this->admin->getRow("",["loccode"=>$ff["tlocationcode"]],[],"$this->database.tbl_locations");
						// $itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");

						
						// $ff["flocation"] = ["id"=>(string) $flocdata->_id,"locname"=>$flocdata->locname,"loccode"=>$flocdata->loccode,"status"=>$flocdata->status];
						
						// $ff["tlcoation"] = ["id"=>(string) $tlocdata->_id,"locname"=>$tlocdata->locname,"loccode"=>$tlocdata->loccode,"status"=>$tlocdata->status];

						$tlocdata = [];
						$flocdata = [];
						
						/*if($ff["tlcoation"] != ''){
							$tlocd = $this->admin->getRow("",["locname"=>$ff["tlcoation"]],[],"$this->database.tbl_locations");	
							
							if($locd){
								
								$tlocdata[] = $tlocd;
								
							}else{
								
								$tlocdata[] = $this->admin->getRow("",["loccode"=>$ff["tlocationcode"]],[],"$this->database.tbl_locations");
								
							}
							
						}else */
						if(trim($ff["tlocationcode"]) != ''){
							$tlocation = $this->admin->getRow("",["loccode"=>$ff["tlocationcode"]],['projection' => ['locname' => 1,'loccode' => 1,'status' => 1,'Type' => 1]],"$this->database.tbl_locations");
							$tlocdata[] = $tlocation;
						}
						
						/*if($ff["flocation"] != ''){
							$flocdata[] = $this->admin->getRow("",["locname"=>$ff["flocation"]],[],"$this->database.tbl_locations");	
						}else */
						if(trim($ff["flcoationcode"]) != ''){
							$flocdata[] = $this->admin->getRow("",["loccode"=>$ff["flcoationcode"]],['projection' => ['locname' => 1,'loccode' => 1,'status' => 1,'Type' => 1]],"$this->database.tbl_locations");
						}
				
						$itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");

						
//						$ff["flocation"] = ["id"=>(string) $flocdata->_id,"locname"=>$flocdata->locname,"loccode"=>$flocdata->loccode,"status"=>$flocdata->status];

						if(count($flocdata) > 0){
							$ff["flocation"] = ["id"=>(string) $flocdata[0]->_id,"locname"=>$flocdata[0]->locname,"loccode"=>$flocdata[0]->loccode,"status"=>$flocdata[0]->status];
							$ff['flcoationcode'] = $flocdata[0]->loccode;
						}else{
							$ff["flocation"] = ["id"=>"","locname"=>"","loccode"=>"","status"=>""];
						}
						
						if(count($tlocdata[0]) > 0){
							$ff["tlcoation"] = ["id"=>(string) $tlocdata[0]->_id,"locname"=>$tlocdata[0]->locname,"loccode"=>$tlocdata[0]->loccode,"status"=>$tlocdata[0]->status];
							$ff['tlocationcode'] = $tlocdata[0]->loccode;
						}else{
							$ff["tlcoation"] = ["id"=>"","locname"=>$ff["tlcoation"],"loccode"=>$ff["tlocationcode"],"status"=>"Active"];
							$ff['tlocationcode'] = $ff['tlocationcode'];
						}
						
						
						$ff["item"] = ["id"=>(string) $itemdata->_id,"item_name"=>$itemdata->item_name,"status"=>$itemdata->status];

					}elseif($table == "tbl_issues" || $table == "tbl_returns" || $table == "tbl_adjustments"){
						
						$tlocdata = $this->admin->getRow("",["loccode"=>$ff["tlcoationcode"]],['projection' => ['locname' => 1,'loccode' => 1,'status' => 1,'Type' => 1]],"$this->database.tbl_locations");
						$itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");

						
						$ff["tlocation"] = ["id"=>(string) $tlocdata->_id,"locname"=>$tlocdata->locname,"loccode"=>$tlocdata->loccode,"status"=>$tlocdata->status];
						
						$ff["item"] = ["id"=>(string) $itemdata->_id,"item_name"=>$itemdata->item_name,"status"=>$itemdata->status];
						
					}elseif($table == "tbl_inventory"){
						
						$tlocdata = $this->admin->getRow("",["loccode"=>$ff["loccode"]],['projection' => ['locname' => 1,'loccode' => 1,'status' => 1,'Type' => 1]],"$this->database.tbl_locations");
						$itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");

						$ff["locname"] = ["id"=>(string) $tlocdata->_id,"locname"=>$tlocdata->locname,"loccode"=>$tlocdata->loccode,"status"=>$tlocdata->status];
						$ff["item"] = ["id"=>(string) $itemdata->_id,"item_name"=>$itemdata->item_name,"status"=>$itemdata->status];
						$ff["location"] = $tlocdata->locname." - ".$tlocdata->loccode;
						$ff["loccode"] = $tlocdata->loccode;
						$ff["loctype"] = $tlocdata->Type;
//						$ff["notes"] = $tlocdata->notes;
						
						
					}
					
					$prefix = $this->admin->getPrefix($table);
					if($table == "tbl_locations"){
					
						if($aid == 0){

							$ff["locid"] = $prefix.$autoid;

						}else{

							$ff["locid"] = $prefix.(intval($autoid++) + 1);

						}
						
					}else{
						
						if($aid == 0){

							$ff["id"] = $prefix.$autoid;

						}else{

							$ff["id"] = $prefix.(intval($autoid++) + 1);

						}
						
					}
					
					$aid++;

					
//					print_r($error);
//					print_r($ff);
			 		array_push($records, $ff);
		 		
			 	} //Row wise Closing
			
			$ending_id = $ff["id"];
			
			 if((count($error)>0 || count($warnings)>0) && ($this->input->post("usubmit") != "Upload")){
			 		echo json_encode(array("Status"=>"Dups","Message"=>$error,"WarCount"=>count($warnings),"WarMsg"=>$warnings));	
			 }else{
				 
			 if($table == 'tbl_inventory'){
				 
			 	foreach ($records as $key => $value) {
//			 		$value['issues'] = $this->common->getInventorycount(mongodb."_".$value['appId'],"tbl_issues",$value['appId'],$value['loccode'],"tlcoationcode",$value['item']["item_name"]);
//
//			 		$value['returns'] = $this->common->getInventorycount(mongodb."_".$value['appId'],"tbl_returns",$value['appId'],$value['loccode'],"tlcoationcode",$value['item']["item_name"]);
//
//			 		$value['transfer_ins'] = $this->common->getInventorycount(mongodb."_".$value['appId'],"tbl_touts",$value['appId'],$value['loccode'],"tlocationcode",$value['item']["item_name"]);
//
//			 		$value['transfer_outs'] = $this->common->getInventorycount(mongodb."_".$value['appId'],"tbl_touts",$value['appId'],$value['loccode'],"flcoationcode",$value['item']["item_name"]);
//
//			 		$value['adjustments'] = $this->common->getInventorycount(mongodb."_".$value['appId'],"tbl_adjustments",$value['appId'],$value['loccode'],"tlcoationcode",$value['item']["item_name"]);
			 		
//			 		$value['issues'] = 0; 
			 		if($this->admin->getCount($mng,$database,["loccode"=>$value['loccode'],"item.item_name"=>$value['item']["item_name"]],[['projection' => ['_id' => 1]]])>0){
			 		
						$starting_balance = $this->admin->getReturn($mng,$database,["loccode"=>$value['loccode'],"item.item_name"=>$value['item']["item_name"]],[],"starting_balance");
						
						$ending_balance = $this->admin->getReturn($mng,$database,["loccode"=>$value['loccode'],"item.item_name"=>$value['item']["item_name"]],[],"ending_balance");
						
						$value['ending_balance'] = (int) ($ending_balance+$value['starting_balance']);						
						$value['starting_balance'] = (int) ($starting_balance + $value['starting_balance']);
//						$value['ending_balance'] = $value['starting_balance']+$value['issues']+$value['returns']+$value['transfer_ins']-$value['transfer_outs']+$value['adjustments'];
						
						unset($value['issues']);
						unset($value['returns']);
						unset($value['transfer_ins']);
						unset($value['transfer_outs']);
						unset($value['adjustments']);
//						unset($value['ending_balance']);
						
						$options = ['multi' => true, 'upsert' => true];
						$where = ["loccode"=>$value['loccode'],"item.item_name"=>$value['item']["item_name"]];
						
						$result = $this->admin->mongoUpdate($database,$where,$value,$options);
			 			// print_r($result);
			 		}else{
//			 			$value['ending_balance'] = $value['starting_balance']+$value['issues']+$value['returns']+$value['transfer_ins']-$value['transfer_outs']+$value['adjustments'];
						
						$value['starting_balance'] = (int) $value['starting_balance'];
						$value['ending_balance'] = (int) ($value['ending_balance']);
//						$value['ending_balance'] = $value['starting_balance']+$value['issues']+$value['returns']+$value['transfer_ins']-$value['transfer_outs']+$value['adjustments'];
						
						$value['issues'] = (int) $value['issues'];
						$value['returns'] = (int) $value['returns'];
						$value['transfer_ins'] = (int) $value['transfer_ins'];
						$value['transfer_outs'] = (int) $value['transfer_outs'];
						$value['adjustments'] = (int) $value['adjustments']; 
						
			 			$result = $this->admin->mongoInsert($database,$value,"");
			 		}
					
					
//						print_r($value);
			 	}
				 
//				exit(); 

			 		if($result){
			 			echo json_encode(array("Status"=>"Success","Message"=>"Successfully imported"));
			 		}else{
			 			echo json_encode(array("Status"=>"Wrong","Message"=>"Something went wrong! Contact Admin."));
			 		}

			 }else{

			 	// echo "<pre>";
			 	// print_r($records);
			 	// exit;
			 	try{
			 		$result = $this->admin->mongoInsert($database,$records,"bulk");
			 		if($result){
			 			echo json_encode(array("Status"=>"Success","Message"=>"Successfully imported"));
			 		}else{
			 			echo json_encode(array("Status"=>"Wrong","Message"=>"Something went wrong! Contact Admin."));
			 		}
			 		
			 	}catch(Exception $e){

			 	}

			 }
			 
			 if($table == "tbl_issues" || $table == "tbl_returns" || $table == "tbl_adjustments" || $table == "tbl_inventory" || $table == "tbl_touts"){	 
				 
				 $start = $this->admin->getRow("",["id"=>$starting_id],[],"$this->database.$table");	 
				 $end = $this->admin->getRow("",["id"=>$ending_id],[],"$this->database.$table");	 

				 $this->admin->mongoInsert("ongpool.tbl_import_data",["appId"=>$appId,"table"=>$table,"start"=>(string) $start->_id,"end"=>(string) $end->_id,"records"=>count($records),"status"=>"Queue","imported_user"=>"Internal User"],"");
	 
			 }
				 
		}
				
			}else{ //Cnt else part
				$records = [];
				$error = [];
				$warnings = [];
				if($row == 0){ $ro=1; }else{ $ro = $row+1; }
				
				for($i=$ro;$i <= $highestRow;$i++){   //For row start
			 		$data=[];
			 		$predata=[];

					if($table == "tbl_locations"){
						array_push($predata,"nameid");
					}
					if($table == "tbl_inventory"){
						array_push($predata,"location");
					}
			 		foreach($fields1 as $key=>$value){ // For Column wise Data Collection
			 			$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();
			 			array_push($predata, (string)$new_val);
			 		} // For Column wise Data Collection Close 
			 		
			 		foreach($fields1 as $key=>$value){
			 			if($column[$key] != "0"){
			 				$index=array_search($column[$key], $fields1);
			 				$data[$key] = $predata[$index];
			 			}else{
			 				$data[$key]=$predata[$key];
			 			}	
			 		}

			 		if($table == 'tbl_issues' || $table == 'tbl_returns' || $table == 'tbl_adjustments' ){
			 			if(strpos($data[2],"-")){
			 			$mp=explode("-",$data[2]);
			 			$ndate = $mp[1]."-".$mp[0]."-".$mp[2];
					 		}else if(strpos($data[2],"/")){
					 			$mp=explode("/",$data[2]);
					 			$ndate = $mp[1]."-".$mp[0]."-".$mp[2];
					 		}
			 		
			 				$vd=date("Y", strtotime($ndate));

				 		if($vd < 2015){
				 			array_push($error,array("Msg"=>"Shipment Date ".$data[2]." for Vendor Reference ".$data[0]." must be Greater than year 2015","Error"=>$data[2]));
				 		}
				 		/*if($this->admin->getCount($mng,$database,['chepreference'=>$data[0]],[])>0){
				 			array_push($error,array("Msg"=>"Duplicate Customer Reference","Error"=>$data[0]));	
				 		}
				 		if($this->admin->getCount($mng,$database,['ongreference'=>$data[1]],[])>0){
				 			array_push($error,array("Msg"=>"Duplicate Ongweoweh Reference","Error"=>$data[1]));	
				 		}*/
				 		if($this->admin->getCount($mng,$dataitem,['item_name'=>$data[4]],[]) == 0){
				 			array_push($error,array("Msg"=>"The item ".$data[4]." for Vendor Referene ".$data[0]." does not exist","Error"=>$data[4]));	
				 		}

				 		if(is_numeric($data[3]) && $data[3] != 0){ }else{
				 			array_push($error,array("Msg"=>"quantity ".$data[3]." cannot be a Zero (or) Text, it must be > 1 (or) < 1","Error"=>$data[3]));	
				 		}

				 		/*if($this->admin->getCount($mng,$locdatabase,['locname'=>$data[5]],[]) == 0){
				 			array_push($error,array("Msg"=>"To Location ".$data[5]." for Vendor Reference ".$data[0]." does not exist","Error"=>$data[5]));	
				 		}*/

				 		if($this->admin->getCount($mng,$locdatabase,['loccode'=>$data[6]],[]) == 0){
				 			array_push($error,array("Msg"=>"To Location Code".$data[6]." for Vendor Reference ".$data[0]." does not exist","Error"=>$data[5]));	
				 		}

			 		}

			 		if($table == 'tbl_locations'){

			 			if($this->admin->getCount($mng,$database,['loccode'=>$data[2]],[])>0){
							array_push($error,array("Msg"=>"The Location code ".$data[2]." exists.","Error"=>$data[2]));	
				 		}
				 		
			 		}

			 		if($table == 'tbl_touts'){
			 		 	if(strpos($data[1],"-")){
			 			$mp=explode("-",$data[1]);
			 			$ndate = $mp[1]."-".$mp[0]."-".$mp[2];
					 		}else if(strpos($data[1],"/")){
					 			$mp=explode("/",$data[1]);
					 			$ndate = $mp[1]."-".$mp[0]."-".$mp[2];
					 		}
			 		
			 			$vd=date("Y", strtotime($ndate));
			 			if($vd < 2015){
				 			array_push($error,array("Msg"=>"Shipment Date ".$data[1]." for Shipper PO ".$data[0]." must be Greater than year 2015","Error"=>$data[1]));
				 		}
				 		if($this->admin->getCount($mng,$database,['shipperpo'=>$data[0]],[])>0){
				 			array_push($error,array("Msg"=>"Shipper PO ".$data[0]." exists","Error"=>$data[0]));	
				 		}
				 		if($this->admin->getCount($mng,$dataitem,['item_name'=>$data[4]],[]) == 0){
				 			array_push($error,array("Msg"=>"The Item ".$data[4]." for Shipper PO ".$data[0]." does not exist","Error"=>$data[4]));	
				 		}

				 		/*if($this->admin->getCount($mng,$locdatabase,['locname'=>$data[5]],[]) == 0){
				 			array_push($error,array("Msg"=>"From Location ".$data[5]." for Shipper PO ".$data[0]." does not exist","Error"=>$data[4]));	
				 		}*/

				 		if($this->admin->getCount($mng,$locdatabase,['loccode'=>$data[6]],[]) == 0){
				 			array_push($error,array("Msg"=>"From Location Code ".$data[6]." for Shipper PO ".$data[0]." does not exist","Error"=>$data[4]));	
				 		}

				 		/*if($this->admin->getCount($mng,$locdatabase,['locname'=>$data[7]],[]) == 0 && $data[7] != ''){
				 			array_push($error,array("Msg"=>"To Location ".$data[7]." for Shipper PO ".$data[0]." does not exist","Error"=>$data[4]));	
				 		}*/

				 		if($this->admin->getCount($mng,$locdatabase,['loccode'=>$data[8]],[]) == 0 && $data[8] != ''){
				 			array_push($error,array("Msg"=>"To Location Code ".$data[8]." for Shipper PO ".$data[0]." does not exist","Error"=>$data[4]));	
				 		}

				 		if(is_numeric($data[9]) && $data[9] != 0){ }else{
				 			array_push($error,array("Msg"=>"Quantity ".$data[9]." cannot be a Zero (or) Text, it must be > 1 (or) < 1","Error"=>$data[9]));	
				 		}

				 		/*if($data[7] == '' || $data[7] == 'null'){
				 			array_push($warnings,array("Msg"=>"To Location ".$data[8]." for Shipper PO ".$data[0]." does not exist"));		
				 		}*/

				 		if($data[8] == '' || $data[8] == 'null'){
				 			array_push($warnings,array("Msg"=>"To Location Code ".$data[8]." for Shipper PO ".$data[0]." does not exist"));		
				 		}
			 				
			 		}

			 		if($table == 'tbl_inventory'){
		
				 		if($this->admin->getCount($mng,$dataitem,['item_name'=>$data[15]],[]) == 0){
				 			array_push($error,array("Msg"=>"The item ".$data[15]." for Location Code ".$data[2]." does not exist","Error"=>$data[15]));	
				 		}


				 		/*if($this->admin->getCount($mng,$locdatabase,['locname'=>$data[1]],[]) == 0){
				 			array_push($error,array("Msg"=>"From Location ".$data[1]." does not exist","Error"=>$data[1]));	
				 		}*/

				 		if($this->admin->getCount($mng,$locdatabase,['loccode'=>$data[2]],[]) == 0){
				 			array_push($error,array("Msg"=>"From Location ".$data[2]." does not exist","Error"=>$data[1]));	
				 		}

				 		if(is_numeric($data[6])){ }else{
				 			array_push($error,array("Msg"=>"Starting Balance ".$data[6]." cannot be a Negative value (or) Text, it must be 0 (or) <= 1","Error"=>$data[6]));	
				 		}
			 				
			 		}

			 		if($table == 'tbl_items'){
				 		if($this->admin->getCount($mng,$database,['item_code'=>$data[0]],[])>0){
				 			array_push($error,array("Msg"=>"The Item code ".$data[0]." exists","Error"=>$data[0]));	
				 		}
			 		}
			 		
			 		array_push($data, $appId);
			 		array_push($data, 0);
			 		array_push($data, date("Y-m-d H:i:s A"));
			 		$ff = array_combine($fields, $data);
			 		$ff['flag'] = "excel";
			 		$valRulescheck = $this->common->checkValidationrules($table,$ff,$appId,"");
					if($valRulescheck){
						array_push($error,array("Msg"=>$valRulescheck,"Error"=>""));	
					}
					
					if($table == "tbl_locations"){
						
						$ff["nameid"] = $ff["locname"]." - ".$ff["loccode"];
						
					}
					if($table == "tbl_inventory"){
						
						$ff["location"] = $ff["locname"]." - ".$ff["loccode"];
						
					}
					
					
					if($table == 'tbl_issues' || $table == 'tbl_returns' || $table == 'tbl_adjustments'){
						
						if(strpos($data[7],"-")){
							$mp=explode("-",$data[7]);
							$ndate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[7],"/")){
							$mp=explode("/",$data[7]);
							$ndate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$ndate = intval($data[7]);
						}
						
						$ff['chepprocessdate'] = ((gettype($ndate) == "integer") || ($data[7] == "") || ($data[7] == " ")) ? "" : $ndate;
						
					}
					
					if($table == 'tbl_locations'){
					
						if(strpos($data[10],"-")){
							$mp=explode("-",$data[10]);
							$ndate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[10],"/")){
							$mp=explode("/",$data[10]);
							$ndate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$ndate = intval($data[10]);
						}
						
						$ff['import_date'] = ((gettype($ndate) == "integer") || ($data[10] == "") || ($data[10] == " ")) ? "" : $ndate;
						
					}
					
					if($table == 'tbl_touts'){
					
						if(strpos($data[10],"-")){
							$mp=explode("-",$data[10]);
							$rdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[10],"/")){
							$mp=explode("/",$data[10]);
							$rdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$rdate = intval($data[10]);
						}
						
						if(strpos($data[12],"-")){
							$mp=explode("-",$data[12]);
							$pdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[12],"/")){
							$mp=explode("/",$data[12]);
							$pdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$pdate = intval($data[12]);
						}
						
						if(strpos($data[13],"-")){
							$mp=explode("-",$data[13]);
							$cpdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[13],"/")){
							$mp=explode("/",$data[13]);
							$cpdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$cpdate = intval($data[13]);
						}
						
						$ff['reportdate'] = ((gettype($rdate) == "integer") || ($data[10] == "") || ($data[10] == " ")) ? "" : $rdate;
						$ff['processdate'] = ((gettype($pdate) == "integer") || ($data[12] == "") || ($data[12] == " ")) ? "" : $pdate;
						$ff['chepprocessdate'] = ((gettype($cpdate) == "integer") || ($data[13] == "") || ($data[13] == " ")) ? "" : $cpdate;
						
					}
					
					if($table == 'tbl_inventory'){
					
						if(strpos($data[5],"-")){
							$mp=explode("-",$data[5]);
							$lrdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[5],"/")){
							$mp=explode("/",$data[5]);
							$lrdate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$lrdate = intval($data[5]);
						}
						
						if(strpos($data[13],"-")){
							$mp=explode("-",$data[13]);
							$addate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else if(strpos($data[13],"/")){
							$mp=explode("/",$data[13]);
							$addate = $mp[0]."-".$mp[1]."-".$mp[2];
						}else{
							
							$addate = intval($data[13]);
						}
						
						$ff['last_report_date'] = ((gettype($lrdate) == "integer") || ($data[5] == "") || ($data[5] == " ")) ? "" : $lrdate;
						$ff['audit_date2019'] = ((gettype($addate) == "integer") || ($data[13] == "") || ($data[13] == " ")) ? "" : $addate;
						
					}

					foreach($ff as $fk => $fval){
						
						if(($fk != "quantity") && ($fk != "accounts") && ($fk != "chepprocessdate") && ($fk != "shippmentdate") && ($fk != "import_date") && ($fk != "reportdate") && ($fk != "processdate") && ($fk != "last_report_date") && ($fk != "audit_date2019")){
							
							$ff[$fk] = trim($fval);
							
						}
						
					}


								// updating location and item values					
					
					if($table == "tbl_touts"){
						
						$flocdata = $this->admin->getRow("",["loccode"=>$ff["flcoationcode"]],[],"$this->database.tbl_locations");
						$tlocdata = $this->admin->getRow("",["loccode"=>$ff["tlocationcode"]],[],"$this->database.tbl_locations");
						$itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");

						
						$ff["flocation"] = ["id"=>(string) $flocdata->_id,"locname"=>$flocdata->locname,"loccode"=>$flocdata->loccode,"status"=>$flocdata->status];
						
						$ff["tlcoation"] = ["id"=>(string) $tlocdata->_id,"locname"=>$tlocdata->locname,"loccode"=>$tlocdata->loccode,"status"=>$tlocdata->status];
						
						$ff["item"] = ["id"=>(string) $itemdata->_id,"item_name"=>$itemdata->item_name,"status"=>$itemdata->status];

					}elseif($table == "tbl_issues" || $table == "tbl_returns" || $table == "tbl_adjustments"){
						
						$tlocdata = $this->admin->getRow("",["loccode"=>$ff["tlcoationcode"]],[],"$this->database.tbl_locations");
						$itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");

						
						$ff["tlocation"] = ["id"=>(string) $tlocdata->_id,"locname"=>$tlocdata->locname,"loccode"=>$tlocdata->loccode,"status"=>$tlocdata->status];
						
						$ff["item"] = ["id"=>(string) $itemdata->_id,"item_name"=>$itemdata->item_name,"status"=>$itemdata->status];
						
					}elseif($table == "tbl_inventory"){
						
						$tlocdata = $this->admin->getRow("",["loccode"=>$ff["loccode"]],[],"$this->database.tbl_locations");
						$itemdata = $this->admin->getRow("",["item_name"=>$ff["item"]],[],"$this->database.tbl_items");

						$ff["locname"] = ["id"=>(string) $tlocdata->_id,"locname"=>$tlocdata->locname,"loccode"=>$tlocdata->loccode,"status"=>$tlocdata->status];
						$ff["item"] = ["id"=>(string) $itemdata->_id,"item_name"=>$itemdata->item_name,"status"=>$itemdata->status];
						$ff["location"] = $tlocdata->locname." - ".$tlocdata->loccode;
						$ff["loccode"] = $tlocdata->loccode;
						$ff["loctype"] = $tlocdata->Type;
						$ff["notes"] = $tlocdata->notes;
						
						
					}

					
			 		array_push($records, $ff);
				} ////For row close

				$database=mongodb."_".$appId.".".$table;
				
				if($field != "0"){
					$options = ['multi' => false, 'upsert' => true];
			 		$where = [$field=>$records[0][$field]];

						try{
							// print_r($records[0]);
							$result = $this->admin->mongoUpdate($database,$where,$records[0],$options);
							if($result){
							echo json_encode(array("Status"=>"Success","Message"=>"Successfully imported"));
							}else{
							echo json_encode(array("Status"=>"Wrong","Message"=>"Something went wrong! Contact Admin."));
							}
						}

						catch(Exception $e){
							
						}
				}else{
					if(count($error) > 0){
					echo json_encode(array("Status"=>"Dups","Message"=>$error,"WarCount"=>count($warnings),"WarMsg"=>$warnings));
				}else{

					if($table == 'tbl_inventory'){

						foreach ($records as $key => $value) {
//			 		$value['issues'] = $this->common->getInventorycount(mongodb."_".$value['appId'],"tbl_issues",$value['appId'],$value['loccode'],"tlcoationcode",$value['item']["item_name"]);
//
//			 		$value['returns'] = $this->common->getInventorycount(mongodb."_".$value['appId'],"tbl_returns",$value['appId'],$value['loccode'],"tlcoationcode",$value['item']["item_name"]);
//
//			 		$value['transfer_ins'] = $this->common->getInventorycount(mongodb."_".$value['appId'],"tbl_touts",$value['appId'],$value['loccode'],"tlocationcode",$value['item']["item_name"]);
//
//			 		$value['transfer_outs'] = $this->common->getInventorycount(mongodb."_".$value['appId'],"tbl_touts",$value['appId'],$value['loccode'],"tlocationcode",$value['item']["item_name"]);
//
//			 		$value['adjustments'] = $this->common->getInventorycount(mongodb."_".$value['appId'],"tbl_adjustments",$value['appId'],$value['loccode'],"tlcoationcode",$value['item']["item_name"]);
			 		
			 		// print_r($value);
			 		if($this->admin->getCount($mng,$database,["loccode"=>$value['loccode'],"item"=>$value['item']["item_name"]],[])>0){
			 		
			 		$starting_balance = $this->admin->getReturn($mng,$database,["loccode"=>$value['loccode'],"item"=>$value['item']["item_name"]],[],"starting_balance");
			 		$value['starting_balance'] += $starting_balance;
//			 		$value['ending_balance'] = $value['starting_balance']+$value['issues']+$value['returns']+$value['transfer_ins']-$value['transfer_outs']+$value['adjustments'];
			 		$options = ['multi' => true, 'upsert' => true];
			 		$where = ["loccode"=>$value['loccode'],"item"=>$value['item']["item_name"]];
			 		$result = $this->admin->mongoUpdate($database,$where,$value,$options);
			 			// print_r($result);
			 		}else{
//			 			$value['ending_balance'] = $value['starting_balance']+$value['issues']+$value['returns']+$value['transfer_ins']-$value['transfer_outs']+$value['adjustments'];
			 			$result = $this->admin->mongoInsert($database,$value,"");
			 		}
			 	}

			 		if($result){
			 			echo json_encode(array("Status"=>"Success","Message"=>"Successfully imported"));
			 		}else{
			 			echo json_encode(array("Status"=>"Wrong","Message"=>"Something went wrong! Contact Admin."));
			 		}


					}else{

							try{
							$result = $this->admin->mongoInsert($database,$records,"bulk");
							if($result){
							echo json_encode(array("Status"=>"Success","Message"=>"Successfully imported","WarCount"=>count($warnings),"WarMsg"=>$warnings));
							}else{
							echo json_encode(array("Status"=>"Wrong","Message"=>"Something went wrong! Contact Admin."));
							}

							}
							catch(Exception $e){
					
							}
					}

			 		

				}
				}
				
			}



}


	public function gettestupload(){
				$path = FCPATH.'uploads/exceldata/20200318111300.xlsx';
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			 	$spreadsheet = $reader->load($path)->getSheet(0);
			 	$worksheet = $spreadsheet;
			 	$highestRow = $worksheet->getHighestRow();
			 	$highestColumn = $worksheet->getHighestColumn();
			 	$headings = $worksheet->rangeToArray('A1:' . $highestColumn . 1,NULL,TRUE,FALSE)[0];
			 	$records = [];
			 	if($highestRow > 12){
			 		$m=12;
			 	}else{
			 		$m = $highestRow;
			 	}
			 	for($i=2;$i < $m;$i++){
			 		$data=[];
			 		foreach($headings as $key=>$value){
			 			$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();
			 			array_push($data, $new_val);
			 		}
			 		array_push($records, $data);
			 	}
				echo json_encode(array("Headings"=>$headings,"Records"=>$records,"Stauts"=>"Success","appId"=>"OID001","headers"=>"0","field"=>"itemcode"));
	}
}