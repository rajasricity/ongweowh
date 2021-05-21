<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scripts extends CI_Controller {

	public function __construct(){
		
		parent::__construct();

		$this->mdb = mongodb;

	}
	
	function update_inv_script(){
		
		
		/*$this->mongo_db->switch_db("ongpool_OID0020");
		
		echo '<pre>';*/

//		$customers = $this->mongo_db->get_where("tbl_apps",["status"=>"Active","deleted"=>0]);
		
		echo '<pre>';
		
//			foreach($customers as $cust){

//				$appid = $cust["appId"];
				$database = "ongpool_OID00";
				
				$this->mongo_db->switch_db($database);

		// issues

				$issues = $this->mongo_db->aggregate("tbl_issues",[
						['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
//						['$limit' => 10]
				]);
				
//				print_r($issues);
		// pickups		

				$pickups = $this->mongo_db->aggregate("tbl_returns",[
						['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
				]);

		// adjustments		

				$adjustments = $this->mongo_db->aggregate("tbl_adjustments",[
						['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
				]);

		// Transfers Ins		

				$transferins = $this->mongo_db->aggregate("tbl_touts",[
						['$match' => ['item.status'=>"Active","flocation.status"=>"Active","tlcoation.status"=>"Active"]],
						['$sort' => ["_id"=>-1]]
				]);
				

		// end query
				
				
// update progress bar count
				
				
		// update issues count		
//
				if($issues){

					foreach($issues as $iss){
						
//						echo $iss["_id"]->{'$id'};

						$inv = $this->mongo_db->where(array('item.item_name'=>$iss["item"]->item_name,"loccode"=>$iss["tlcoationcode"]))->get('tbl_inventory')[0];
						
						if($inv){

							$cissues = ($inv["issues"]+$iss["quantity"]);
							
							$ending_balance = $inv["starting_balance"]+$cissues+$inv["returns"]+$inv["transfer_ins"]-$inv["transfer_outs"]+$inv["adjustments"];

							$data = array(
								"issues" => ($inv["issues"]+$iss["quantity"]),
								"ending_balance" => $ending_balance
								);
							
							$u = $this->mongo_db->where(['item.item_name'=>$iss["item"]->item_name,"loccode"=>$iss["tlcoationcode"]])->set($data)->update('tbl_inventory');
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($iss["_id"]->{'$id'})])->set(["flag"=>"uexcel"])->update("tbl_issues");		

						}else{
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($iss["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_issues");
							
						}
						

					}
					
					

				}

		// update pickups count		

				if($pickups){

					foreach($pickups as $pkk){

						$inv = $this->mongo_db->where(array('item.item_name'=>$pkk["item"]->item_name,"loccode"=>$pkk["tlcoationcode"]))->get('tbl_inventory')[0];

						if($inv){

							$creturns = ($inv["returns"]+$pkk["quantity"]);
							$ending_balance = $inv["starting_balance"]+$inv["issues"]+$creturns+$inv["transfer_ins"]-$inv["transfer_outs"]+$inv["adjustments"];

							$data = array(
								"returns" => ($inv["returns"]+$pkk["quantity"]),
								"ending_balance" => $ending_balance
								);

							$this->mongo_db->where(array('item.item_name'=>$pkk["item"]->item_name,"loccode"=>$pkk["tlcoationcode"]))->set($data)->update('tbl_inventory');
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($pkk["_id"]->{'$id'})])->set(["flag"=>"uexcel"])->update("tbl_returns");		

						}else{
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($pkk["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_returns");
							
						}
						

					}
					
				}

		// update adjustments count		

				if($adjustments){

					foreach($adjustments as $adj){

						$inv = $this->mongo_db->where(array('item.item_name'=>$adj["item"]->item_name,"loccode"=>$adj["tlcoationcode"]))->get('tbl_inventory')[0];

						if($inv){

							$cadjustments = ($inv["adjustments"]+$adj["quantity"]);
							$ending_balance = $inv["starting_balance"]+$inv["issues"]+$inv["returns"]+$inv["transfer_ins"]-$inv["transfer_outs"]+$cadjustments;

							$data = array(
								"adjustments" => ($inv["adjustments"]+$adj["quantity"]),
								"ending_balance" => $ending_balance
								);

							$this->mongo_db->where(array('item.item_name'=>$adj["item"]->item_name,"loccode"=>$adj["tlcoationcode"]))->set($data)->update('tbl_inventory');
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($adj["_id"]->{'$id'})])->set(["flag"=>"uexcel"])->update("tbl_adjustments");		

						}else{
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($adj["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_adjustments");
						}
						

					}
					
				}		

		// update transfer ins count		

				if($transferins){

					foreach($transferins as $tin){

						$inv = $this->mongo_db->where(array('item.item_name'=>$tin["item"]->item_name,"loccode"=>$tin["tlocationcode"]))->get('tbl_inventory')[0];
						
						$touinv = $this->mongo_db->where(array('item.item_name'=>$tin["item"]->item_name,"loccode"=>$tin["flcoationcode"]))->get('tbl_inventory')[0];

						
//						print_r($tin);
//						print_r($touinv);
						
						
						if($inv){

							$ctins = ($inv["transfer_ins"]+$tin["quantity"]);
							
							$ending_balance = $inv["starting_balance"]+$inv["issues"]+$inv["returns"]+$ctins-$inv["transfer_outs"]+$inv["adjustments"];

							$data = array(
								"transfer_ins" => ($inv["transfer_ins"]+$tin["quantity"]),
								"ending_balance" => $ending_balance
								);

							$this->mongo_db->where(array('item.item_name'=>$tin["item"]->item_name,"loccode"=>$tin["tlocationcode"]))->set($data)->update('tbl_inventory');
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($tin["_id"]->{'$id'})])->set(["flag"=>"uexcel"])->update("tbl_touts");		


						}else{
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($tin["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_touts");
							
						}
						
						if($touinv){

							$ctouts = ($touinv["transfer_outs"]+$tin["quantity"]);
							$ending_balance = $touinv["starting_balance"]+$touinv["issues"]+$touinv["returns"]+$touinv["transfer_ins"]-$ctouts+$touinv["adjustments"];

							$data = array(
								"transfer_outs" => ($touinv["transfer_outs"]+$tin["quantity"]),
								"ending_balance" => $ending_balance
								);
							
							$this->mongo_db->where(array('item.item_name'=>$tin["item"]->item_name,"loccode"=>$tin["flcoationcode"]))->set($data)->update('tbl_inventory');
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($tin["_id"]->{'$id'})])->set(["flag"=>"uexcel"])->update("tbl_touts");

//							print_r($d);
						}else{
							
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($tin["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_touts");
							
						}
					
					}
					
					/*if($progress == 10){
						
						exit();
						
					}*/
					

				}		

				$this->mongo_db->where(["flag"=>"excel"])->set(["flag"=>"uexcel"])->update_all("tbl_inventory");					
			
//			}
		
	}
	
	public function updateInventorycountzero(){
		
//		$customers = $this->mongo_db->get_where("tbl_apps",["status"=>"Active","deleted"=>0]);
		
		echo '<pre>';
		
//			foreach($customers as $cust){

				$appid = $cust["appId"];
				$database = "ongpool_".$appid;
				
				$this->mongo_db->switch_db($database);
				
				$inventories = $this->mongo_db->get_where("tbl_inventory",["locname.status"=>"Active","item.status"=>"Active"]);	
				
				foreach($inventories as $inv){
					
					$val['starting_balance'] = intval($inv["starting_balance"]);
					$val['issues'] = 0;
					$val['returns'] = 0;
					$val['transfer_ins'] = 0;
					$val['transfer_outs'] = 0;
					$val['adjustments'] = 0;
					$val['ending_balance'] = 0;
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($inv["_id"]->{'$id'})])->set($val)->update("tbl_inventory");					
				}
				
//				$this->mongo_db->switch_db(mongodb);
//				
//			}
		
	}

	function update_inventory_with_loccodes_old(){
		
		$appid = "OID0020";
		$database = mongodb."_".$appid;
		
		$this->mongo_db->switch_db(mongodb."_".$appid);
		
		$inventory = [
						["loccode"=>"B0190","item"=>"80x40 GWB Pallet 2"],
						["loccode"=>"B0010","item"=>"80x40 GWB Pallet 2"],
					 ];
			
		foreach($inventory as $inv){
			
			$invData = $this->mongo_db->get_where("tbl_inventory",["item.item_name"=>$inv["item"],"loccode"=>$inv["loccode"]])[0];

//			print_r($invData);
			
			$iss = $this->getInventorycount($database,"tbl_issues","$appid",$inv['loccode'],"tlcoationcode",$inv['item']);
			$ret = $this->getInventorycount($database,"tbl_returns","$appid",$inv['loccode'],"tlcoationcode",$inv['item']);
			$adj = $this->getInventorycount($database,"tbl_adjustments","$appid",$inv['loccode'],"tlcoationcode",$inv['item']);
			$tins = $this->getInventorycount($database,"tbl_touts","$appid",$inv["loccode"],"tlocationcode",$inv['item']);
			$touts = $this->getInventorycount($database,"tbl_touts","$appid",$inv["loccode"],"flcoationcode",$inv['item']);

			$ending_balance = $invData["starting_balance"]+$iss+$ret+$tins-$touts+$adj;

			$data = array(
			"issues" => intval($iss),
			"returns" => intval($ret),
			"adjustments" => intval($adj),
			"transfer_ins" => intval($tins),
			"transfer_outs" => intval($touts),
			"ending_balance" => intval($ending_balance)
			);
			$d = $this->mongo_db->where(array('_id'=>new MongoDB\BSON\ObjectID($invData["_id"]->{'$id'})))->set($data)->update('tbl_inventory');

		}
		
		echo "Inventory successfully updated.";

	}
	
	function update_inventory_with_loccodes(){
		
		$appid = "OID0020";
		$database = mongodb."_".$appid;
		
		$this->mongo_db->switch_db(mongodb."_".$appid);
		
		
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
		
			 $highestRow = $worksheet->getHighestRow();
			 $hrow = $worksheet->getHighestRow();
			 $highestColumn = 5;//$worksheet->getHighestColumn();
			 
			 $excel_fields = ["loccode","item"];
			 $inventory = array();
			 $data = [];

				for($i=2; $i<=$highestRow; $i++){

						foreach ($excel_fields as $key => $value){

							$new_val = $worksheet->getCellByColumnAndRow($key+1, $i)->getValue();
							$data[$value] = $final;
			

						}
					
					    $fields = ["loccode","item"];
						$inventory[] = array_combine($fields,$data);
					
					    print_r($inventory);
					
					  
					/*
						foreach($inventory as $inv){

							$invData = $this->mongo_db->get_where("tbl_inventory",["item.item_name"=>$inv["item"],"loccode"=>$inv["loccode"]])[0];

				//			print_r($invData);

							$iss = $this->getInventorycount($database,"tbl_issues","$appid",$inv['loccode'],"tlcoationcode",$inv['item']);
							$ret = $this->getInventorycount($database,"tbl_returns","$appid",$inv['loccode'],"tlcoationcode",$inv['item']);
							$adj = $this->getInventorycount($database,"tbl_adjustments","$appid",$inv['loccode'],"tlcoationcode",$inv['item']);
							$tins = $this->getInventorycount($database,"tbl_touts","$appid",$inv["loccode"],"tlocationcode",$inv['item']);
							$touts = $this->getInventorycount($database,"tbl_touts","$appid",$inv["loccode"],"flcoationcode",$inv['item']);

							$ending_balance = $invData["starting_balance"]+$iss+$ret+$tins-$touts+$adj;

							$data = array(
							"issues" => intval($iss),
							"returns" => intval($ret),
							"adjustments" => intval($adj),
							"transfer_ins" => intval($tins),
							"transfer_outs" => intval($touts),
							"ending_balance" => intval($ending_balance)
							);
							$d = $this->mongo_db->where(array('_id'=>new MongoDB\BSON\ObjectID($invData["_id"]->{'$id'})))->set($data)->update('tbl_inventory');

						}
					
						*/
						
				}
		}
		
		echo "Inventory successfully updated.";

	}
	
	public function getInventorycount($database,$table,$aid,$loccode,$column,$itemname){
		
		$this->mongo_db->switch_db($database);
		
		if($table == "tbl_touts"){
			
			$this->mongo_db->where(['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active"]);
			
		}elseif($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){

			$this->mongo_db->where(['item.status'=>"Active","tlocation.status"=>"Active"]);
								   
		}
		$data = $this->mongo_db->get_where($table,["appId"=>$aid,$column=>$loccode,"item.item_name"=>$itemname]);
		
		$sum = 0;
		
		if(count($data) > 0){
			
			foreach($data as $d){
				
				$inv = $this->mongo_db->where(array('item.item_name'=>$d["item"]->item_name,"loccode"=>$d[$column]))->get('tbl_inventory')[0];

				if($inv){
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($d["_id"]->{'$id'})])->set(["flag"=>"uexcel"])->update($table);		
				}else{
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($d["_id"]->{'$id'})])->set(["flag"=>"excel"])->update($table);
					
				}

				$sum += intval($d['quantity']);
				
				
			}
		}
		
		return $sum;
		
	}
	
	public function updateinvdata(){
		
		
		$appid = "OID001";
		$database = "ongpool_".$appid;
		$this->mongo_db->switch_db($database);

		$inventory = $this->mongo_db->get("tbl_inventory");

		foreach($inventory as $iinv){

			$iissues = 0;
			$ireturns = 0;
			$itransfer_ins = 0;
			$itransfer_outs = 0;
			$iadjustments = 0;
			$ending_balance = 0;

			$iissues = $this->getInventorycount(mongodb."_".$appid,"tbl_issues",$appid,$iinv['loccode'],"tlcoationcode",$iinv['item']->item_name);

			$ireturns = $this->getInventorycount(mongodb."_".$appid,"tbl_returns",$appid,$iinv['loccode'],"tlcoationcode",$iinv['item']->item_name);

			$iadjustments = $this->getInventorycount(mongodb."_".$appid,"tbl_adjustments",$appid,$iinv['loccode'],"tlcoationcode",$iinv['item']->item_name);

			$itransfer_ins = $this->getInventorycount(mongodb."_".$appid,"tbl_touts",$appid,$iinv['loccode'],"tlocationcode",$iinv['item']->item_name);

			$itransfer_outs = $this->getInventorycount(mongodb."_".$appid,"tbl_touts",$appid,$iinv['loccode'],"flcoationcode",$iinv['item']->item_name);

			$ending_balance = $iinv['starting_balance']+($iinv["issues"] + $iissues)+($iinv["returns"] + $ireturns)+($iinv["transfer_ins"] + $itransfer_ins)-($iinv["transfer_outs"] + $itransfer_outs)+($iinv["adjustments"] + $iadjustments);

			$data = [
						"issues"=>(int) ($iinv["issues"] + $iissues),
						"returns"=>(int) ($iinv["returns"] + $ireturns),
						"transfer_ins"=>(int) ($iinv["transfer_ins"] + $itransfer_ins),
						"transfer_outs"=>(int) ($iinv["transfer_outs"] + $itransfer_outs),
						"adjustments"=>(int) ($iinv["adjustments"] + $iadjustments),
						"ending_balance"=>(int) $ending_balance,
						"flag" => "uexcel"
					];


			$this->mongo_db->where(["flag"=>"excel","_id"=>new MongoDB\BSON\ObjectID($iinv["_id"]->{'$id'})])->set($data)->update("tbl_inventory");

		}


	}
	
	
	public function index(){
		
		$appid = "OID009";
		$this->mongo_db->switch_db("ongpool_$appid");
		
		// $table = "tbl_locations";
		// $table = "tbl_items";
		// $table = "tbl_touts";
		// $table = "tbl_issues";
		// $table = "tbl_returns";
		// $table = "tbl_adjustments";
		// $table = "tbl_inventory";
//		$table = "tbl_apps";
		$prefix = $this->admin->getPrefix($table);
		
		$data = $this->mongo_db->get($table);
		
		$id = 0;
		echo '<pre>';
		foreach($data as $d){
			
			$cid = $prefix.(++$id);
			
			$d = $this->mongo_db->where("_id",new MongoDB\BSON\ObjectID($d["_id"]->{'$id'}))->set(["id"=>$cid])->update($table);
			print_r($d);
//			exit();
//			echo ++$id."<br>";
			
		}	
		
	}
	
	public function removetid(){
		
		$appid = "OID009";
		$this->mongo_db->switch_db("ongpool_$appid");

		$data = $this->mongo_db->get_where("settings",["table"=>"tbl_touts"])[0];
		
		foreach($data['columns'] as $key => $col){
			
			if($col == "transactionid"){
				
				unset($data['columns'][$key]);
				unset($data['labels'][$key]);
				unset($data['dataType'][$key]);
				
			}	
			
		}
		
		$this->mongo_db->where("_id",new MongoDB\BSON\ObjectID($data["_id"]->{'$id'}))->set(["columns"=>$data['columns'],"labels"=>$data['labels'],"dataType"=>$data['dataType']])->update("settings");
		echo '<pre>';
		print_r($data);
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
					
					$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($lin['_id']->{'$id'})])->set(["location"=>$ul['new_name']." - ".$ul['code'],"locname"=>$tlocdata,"loccode"=>$ul['code'],"loctype"=>$ul['loctype'],"notes"=>$ul['notes']])->update("tbl_inventory");
					
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
	
	public function updateuserLocationstatus(){
		
		$this->mongo_db->switch_db($this->mdb);
			
		$users = $this->mongo_db->select(["locations","appid","uname"])->get("tbl_auths");
//				print_r($users);

		echo '<pre>';
		if(count($users) > 0){

			foreach($users as $au){
				
				$this->mongo_db->switch_db($this->mdb."_".$au['appid']);
				
				$locations1 = [];
				
				$locations =  json_decode(json_encode($au['locations']),true);
				foreach($locations as $key => $loc){
					
					$ldata = $this->mongo_db->get_where("tbl_locations",["_id"=>new MongoDB\BSON\ObjectID($loc["LocationId"])])[0];
					
					if($ldata){
					
						$locations1[] = ["Date"=>date("M-d-Y H:i:s"),"LocationId"=>$loc["LocationId"],"loccode"=>$ldata['loccode'],"LocationName"=>$ldata["locname"],"Type"=>$loc["Type"],"status"=>$ldata["status"]];
					
					}else{
						
						unset($locations1[$key]);
						
					}
				}
				
//				echo $au['uname']."<br>";
//				print_r($locations1);
				
				$this->mongo_db->switch_db($this->mdb);
						
				$this->mongo_db->where("_id",new MongoDB\BSON\ObjectID($au["_id"]->{'$id'}))->set(["locations"=>$locations1])->update("tbl_auths");
				
			}
		}
		
	}
	
	public function updateexLocations(){
		
		echo '<pre>';
		
		$this->mongo_db->switch_db($this->mdb."_"."OID0010");
				
		$ftransfers = $this->mongo_db->select(["flcoationcode"])->get("tbl_touts");
		$ttransfers = $this->mongo_db->select(["tlocationcode"])->get("tbl_touts");
		$itransfers = $this->mongo_db->select(["item"])->get("tbl_touts");
		
// tranfers
		
		foreach($ftransfers as $ft){

			$ldata = $this->mongo_db->get_where("tbl_locations",["loccode"=>$ft["flcoationcode"]])[0];
			$tlocdata = [];		
			if($ldata){
				
				$tlocdata = ["id"=>$ldata['_id']->{'$id'},"locname"=>$ldata["locname"],"loccode"=>$ldata["loccode"],"status"=>$ldata["status"]];
//				print_r($tlocdata);
				
				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ft['_id']->{'$id'})])->set(["flocation"=>$tlocdata,"flcoationcode"=>$ldata["loccode"]])->update("tbl_touts");	
				
			}

		}

		foreach($ttransfers as $tt){

			$ldata = $this->mongo_db->get_where("tbl_locations",["loccode"=>$tt["tlocationcode"]])[0];
			$tlocdata = [];		
			if($ldata){
				
				$tlocdata = ["id"=>$ldata['_id']->{'$id'},"locname"=>$ldata["locname"],"loccode"=>$ldata["loccode"],"status"=>$ldata["status"]];
//				print_r($tlocdata);
				
				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($tt['_id']->{'$id'})])->set(["tlcoation"=>$tlocdata,"tlocationcode"=>$ldata["loccode"]])->update("tbl_touts");	
				
			}
		}

		foreach($itransfers as $it){

			$idata = $this->mongo_db->get_where("tbl_items",["item_name"=>$it["item"]])[0];
			
			$titemdata = [];
			
			if($idata){
						
				$titemdata = ["id"=>$idata['_id']->{'$id'},"item_name"=>$idata["item_name"],"status"=>$idata["status"]];
//				print_r($titemdata);
				
				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($it['_id']->{'$id'})])->set(["item"=>$titemdata])->update("tbl_touts");
				
			}

		}

// end transfers

// shipments

		$locshipments = $this->mongo_db->select(["tlcoationcode"])->get("tbl_issues");
		$itemshipments = $this->mongo_db->select(["item"])->get("tbl_issues");

		foreach($locshipments as $ls){

			$ldata = $this->mongo_db->get_where("tbl_locations",["loccode"=>$ls["tlcoationcode"]])[0];
			$tlocdata = [];		
			if($ldata){
				
				$tlocdata = ["id"=>$ldata['_id']->{'$id'},"locname"=>$ldata["locname"],"loccode"=>$ldata["loccode"],"status"=>$ldata["status"]];
//				print_r($tlocdata);
				
				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ls['_id']->{'$id'})])->set(["tlocation"=>$tlocdata,"tlcoationcode"=>$ldata["loccode"]])->update("tbl_issues");	
				
			}

		}

		foreach($itemshipments as $li){

			$idata = $this->mongo_db->get_where("tbl_items",["item_name"=>$li["item"]])[0];
			
			$titemdata = [];
			
			if($idata){
						
				$titemdata = ["id"=>$idata['_id']->{'$id'},"item_name"=>$idata["item_name"],"status"=>$idata["status"]];
//				print_r($titemdata);
				
				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($li['_id']->{'$id'})])->set(["item"=>$titemdata])->update("tbl_issues");
				
			}

		}
		
	// end shipments			
				
	// pickups
				
		$locpickups = $this->mongo_db->select(["tlcoationcode"])->get("tbl_returns");
		$itempickups = $this->mongo_db->select(["item"])->get("tbl_returns");

		foreach($locpickups as $lp){

			$ldata = $this->mongo_db->get_where("tbl_locations",["loccode"=>$lp["tlcoationcode"]])[0];
			$tlocdata = [];		
			if($ldata){
				
				$tlocdata = ["id"=>$ldata['_id']->{'$id'},"locname"=>$ldata["locname"],"loccode"=>$ldata["loccode"],"status"=>$ldata["status"]];
//				print_r($tlocdata);
				
				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($lp['_id']->{'$id'})])->set(["tlocation"=>$tlocdata,"tlcoationcode"=>$ldata["loccode"]])->update("tbl_returns");	
				
			}

		}

		foreach($itempickups as $pi){
			
			$idata = $this->mongo_db->get_where("tbl_items",["item_name"=>$pi["item"]])[0];
			
			$titemdata = [];
			
			if($idata){
						
				$titemdata = ["id"=>$idata['_id']->{'$id'},"item_name"=>$idata["item_name"],"status"=>$idata["status"]];
//				print_r($titemdata);
				
				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($pi['_id']->{'$id'})])->set(["item"=>$titemdata])->update("tbl_returns");
				
			}

		}
	// end pickups	
				
	// adjustments
		
		$locadjustments = $this->mongo_db->select(["tlcoationcode"])->get("tbl_adjustments");
		$itemadjustments = $this->mongo_db->select(["item"])->get("tbl_adjustments");
				

		foreach($locadjustments as $la){
			
			$ldata = $this->mongo_db->get_where("tbl_locations",["loccode"=>$la["tlcoationcode"]])[0];
			$tlocdata = [];		
			if($ldata){
				
				$tlocdata = ["id"=>$ldata['_id']->{'$id'},"locname"=>$ldata["locname"],"loccode"=>$ldata["loccode"],"status"=>$ldata["status"]];
//				print_r($tlocdata);
				
				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($la['_id']->{'$id'})])->set(["tlocation"=>$tlocdata,"tlcoationcode"=>$ldata["loccode"]])->update("tbl_adjustments");	
				
			}

		}

		foreach($itemadjustments as $ai){
			
			$idata = $this->mongo_db->get_where("tbl_items",["item_name"=>$ai["item"]])[0];
			
			$titemdata = [];
			
			if($idata){
						
				$titemdata = ["id"=>$idata['_id']->{'$id'},"item_name"=>$idata["item_name"],"status"=>$idata["status"]];
//				print_r($titemdata);
				
				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ai['_id']->{'$id'})])->set(["item"=>$titemdata])->update("tbl_adjustments");
				
			}

		}
				
	// end adjustments
				
	// inventory
				
		$locinventory = $this->mongo_db->select(["loccode"])->get("tbl_inventory");
		$iteminventory = $this->mongo_db->select(["item"])->get("tbl_inventory");

		foreach($locinventory as $lin){

			$ldata = $this->mongo_db->get_where("tbl_locations",["loccode"=>$lin["loccode"]])[0];
			$tlocdata = [];		
			if($ldata){

				$tlocdata = ["id"=>$ldata['_id']->{'$id'},"locname"=>$ldata["locname"],"loccode"=>$ldata["loccode"],"status"=>$ldata["status"]];
//				print_r($tlocdata);

				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($lin['_id']->{'$id'})])->set(["location"=>$ldata['locname']." - ".$ldata['loccode'],"locname"=>$tlocdata,"loccode"=>$ldata['loccode'],"loctype"=>$ldata['Type']])->update("tbl_inventory");	

			}

		}

		foreach($iteminventory as $ii){

			$idata = $this->mongo_db->get_where("tbl_items",["item_name"=>$ii["item"]])[0];
			
			$titemdata = [];
			
			if($idata){
						
				$titemdata = ["id"=>$idata['_id']->{'$id'},"item_name"=>$idata["item_name"],"status"=>$idata["status"]];
//				print_r($titemdata);
				
				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($ii['_id']->{'$id'})])->set(["item"=>$titemdata])->update("tbl_inventory");
				
			}

		}

// end inventory				


//		print_r($uloc);
		
		
	}

	
	
}
