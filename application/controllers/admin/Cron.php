<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Cron extends CI_Controller {

	function update_inv_script(){
		
		$customers = $this->mongo_db->get("tbl_import_data");
		
		echo '<pre>';
		
		/*$udata = $this->mongo_db->aggregate("tbl_import_data",[
					['$group' => ["_id"=>'$appId','total'=>['$sum'=>1],'records'=>['$sum'=>'$records']]],
				]);
		
//		print_r($udata);
		
		$ucdata = [];
		$uapp = [];
		
		
		foreach($udata as $uc){
			
			$uapp[] = $uc["_id"];
			$ucdata[$uc["_id"]] = ["records"=>$uc["records"],"total"=>$uc["total"]];
			
			$appid = $uc["_id"];
			
			$progressChk = $this->mongo_db->where(["appId"=>$appid])->get("tbl_progressbar")[0];
			
			if(!$progressChk){

				$this->admin->mongoInsert(mongodb.".tbl_progressbar",["appId"=>$appid,"count"=>0,"startTime"=>$starttime,"endTme"=>""]);

			}else{

				$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>0,"startTime"=>$starttime,"endTme"=>""],[]);

			}
			
		}*/
		
//		print_r($ucdata["OID0019"]["records"]);
//		exit();
		
		
//		$fprogress = 0;
		
		foreach($customers as $cust){
			
			$starttime = date("Y-m-d H:i:s");
			$progress = 0;
			
			$appid = $cust["appId"];
			$database = "ongpool_".$appid;
			
			$progressChk = $this->mongo_db->where(["appId"=>$appid])->get("tbl_progressbar")[0];
			
			if(!$progressChk){

				$this->admin->mongoInsert(mongodb.".tbl_progressbar",["appId"=>$appid,"count"=>0,"startTime"=>$starttime,"endTme"=>""]);

			}else{

				$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>0,"startTime"=>$starttime,"endTme"=>""],[]);

			}
			
			$this->admin->mongoUpdate("ongpool.tbl_import_data",["_id"=>new MongoDB\BSON\ObjectID($cust["_id"]->{'$id'})],["status"=>"processing"],[]);
			
			/*if(in_array($appid,$uapp)){
				
				$rtotal = $ucdata[$appid]["records"];
				$ttotal = $ucdata[$appid]["total"];
				
			}*/
			
			$table = $cust["table"];

			$this->mongo_db->switch_db($database);
			
			if($table == "tbl_issues" || $table == "tbl_returns" || $table == "tbl_adjustments"){

				$issues = $this->mongo_db->aggregate($table,[
					['$match' => ['item.status'=>"Active","tlocation.status"=>"Active","flag"=>"excel"]],
					['$group' => ["_id"=>['loccode'=>'$tlcoationcode','item'=>'$item.item_name'],'total'=>['$sum'=>'$quantity'],'item'=>['$first'=>'$item.item_name']]],
				]);
				
				$total = count($issues);

				foreach($issues as $iss){

					$column = "";

					if($table == "tbl_issues"){

						$column = "issues";

					}elseif($table == "tbl_returns"){

						$column = "returns";

					}elseif($table == "tbl_adjustments"){

						$column = "adjustments";

					}

					$inv = $this->mongo_db->where(array('item.item_name'=>$iss["item"],"loccode"=>$iss["_id"]->loccode))->inc([$column=>$iss["total"],"ending_balance"=>$iss["total"]])->update('tbl_inventory');

					if(gettype($inv) == "object"){
					
						if($inv->getModifiedCount() >= 1){

							$this->mongo_db->where_between("_id",new MongoDB\BSON\ObjectID($cust["start"]),new MongoDB\BSON\ObjectID($cust["end"]))->where(["flag"=>"excel",'item.item_name'=>$iss["item"],"tlcoationcode"=>$iss["_id"]->loccode])->set(["flag"=>"uexcel"])->update_all($table);	

						}
						
					}
					
					++$progress;
				
					$percentage = ($progress*100)/$total;
					$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>round($percentage)],[]);
				
				}
				
			}elseif($table == "tbl_touts"){

//				$this->updateTouts($table,$cust["start"],$cust["end"]);

/*
				$tins = $this->mongo_db->aggregate($table,[
					['$match' => ['item.status'=>"Active","tlcoation.status"=>"Active","flag"=>"excel"]],
					['$group' => ["_id"=>['loccode'=>'$tlocationcode','item'=>'$item.item_name'],'total'=>['$sum'=>'$quantity'],'item'=>['$first'=>'$item.item_name']]],
				]);
				
				$touts = $this->mongo_db->aggregate($table,[
					['$match' => ['item.status'=>"Active","flocation.status"=>"Active","flag"=>"excel"]],
					['$group' => ["_id"=>['loccode'=>'$flcoationcode','item'=>'$item.item_name'],'total'=>['$sum'=>'$quantity'],'item'=>['$first'=>'$item.item_name']]],
				]);
*/

				$transferins = $this->mongo_db->get_where("tbl_touts",['item.status'=>"Active","flocation.status"=>"Active","tlcoation.status"=>"Active","tlcoation.flag"=>"excel"]);

				$transferouts = $this->mongo_db->get_where("tbl_touts",['item.status'=>"Active","flocation.status"=>"Active","tlcoation.status"=>"Active","flocation.flag"=>"excel"]);
				
				$total = count($transferins) + count($transferouts);
				
					foreach($transferins as $tin){
						
						$inv = $this->mongo_db->where(array('item.item_name'=>$tin["item"]->item_name,"loccode"=>$tin["tlocationcode"]))->inc(["transfer_ins"=>$tin["quantity"],"ending_balance"=>$tin["quantity"]])->update('tbl_inventory');
						
						if(gettype($inv) == "object"){
					
							if($inv->getModifiedCount() >= 1){

								$this->mongo_db->where(["tlcoation.flag"=>"excel","_id"=>new MongoDB\BSON\ObjectID($tin["_id"]->{'$id'})])->set(["tlcoation.flag"=>"uexcel"])->update("tbl_touts");		
								
							}
						}
						
						++$progress;
				
						$percentage = ($progress*100)/$total;
						$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>round($percentage)],[]);
						
					}
				
					foreach($transferins as $tin){
						
						$touinv = $this->mongo_db->where(array('item.item_name'=>$tin["item"]->item_name,"loccode"=>$tin["flcoationcode"]))->inc(["transfer_outs"=>($tin["quantity"]),"ending_balance"=>(-$tin["quantity"])])->update('tbl_inventory');

						if(gettype($touinv) == "object"){
					
							if($touinv->getModifiedCount() >= 1){

								$this->mongo_db->where(["flocation.flag"=>"excel","_id"=>new MongoDB\BSON\ObjectID($tin["_id"]->{'$id'})])->set(["flocation.flag"=>"uexcel"])->update("tbl_touts");		
								
							}
						}
						
						++$progress;
				
						$percentage = ($progress*100)/$total;
						$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>round($percentage)],[]);
						
					}
				
/*
				
				print_r($tins);
				
				$total = count($tins) + count($touts);
				
				foreach($tins as $tin){

					
					echo $tin["_id"]->loccode." ".$tin["total"]."<br>";
					
					$tinv = $this->mongo_db->where(array('item.item_name'=>$tin["item"],"loccode"=>$tin["_id"]->loccode))->inc(["transfer_ins"=>$tin["total"],"ending_balance"=>$tin["total"]])->update('tbl_inventory');

					if(gettype($tinv) == "object"){
					
						if($tinv->getModifiedCount() >= 1){

							$this->mongo_db->where_between("_id",new MongoDB\BSON\ObjectID($cust["start"]),new MongoDB\BSON\ObjectID($cust["end"]))->where(["flag"=>"excel",'item.item_name'=>$tin["item"],"tlocationcode"=>$tin["_id"]->loccode])->set(["flag"=>"uexcel"])->update_all($table);	

						}
						
					}
					
					
				
				}

				foreach($touts as $tout){
					
//					echo $tout["_id"]->loccode." ".$tout["total"]."<br>";

					$toutinv = $this->mongo_db->where(array('item.item_name'=>$tout["item"],"loccode"=>$tout["_id"]->loccode))->inc(["transfer_outs"=>$tout["total"],"ending_balance"=>(-$tout["total"])])->update('tbl_inventory');

					if(gettype($toutinv) == "object"){
					
						if($toutinv->getModifiedCount() >= 1){

							$this->mongo_db->where_between("_id",new MongoDB\BSON\ObjectID($cust["start"]),new MongoDB\BSON\ObjectID($cust["end"]))->where(["flag"=>"excel",'item.item_name'=>$tout["item"],"flcoationcode"=>$tout["_id"]->loccode])->set(["flag"=>"uexcel"])->update_all($table);	

						}
						
					}
					
					++$progress;
				
					$percentage = ($progress*100)/$total;
					$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>round($percentage)],[]);
				
				}*/
				
				
			}elseif($table == "tbl_inventory"){

//				$this->updateInventoryrecords($appid);
				
				$inventory = $this->mongo_db->where(["flag"=>"excel"])->get("tbl_inventory");
				$total = count($inventory);
				
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
					
					++$progress;

					$percentage = ($progress*100)/$total;
					$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>round($percentage)],[]);
					
				}
				
			}
			
			$progress = 0;

			$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["count"=>100,"appId"=>$appid],["endTme"=>date("Y-m-d H:i:s")],[]);

			$this->admin->mongoInsert(mongodb.".tbl_inventory_update_history",["appId"=>$appid,"count"=>$cust["records"],"startTime"=>$starttime,"endTme"=>date("Y-m-d H:i:s")]);

			$this->admin->mongoDelete("ongpool.tbl_import_data",["_id"=>new MongoDB\BSON\ObjectID($cust["_id"]->{'$id'})],[]);			
			
		}
		
		/*foreach($udata as $uc){
			
			$appid = $uc["_id"];
			$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["count"=>100,"appId"=>$appid],["endTme"=>date("Y-m-d H:i:s")],[]);
		
			$this->admin->mongoInsert(mongodb.".tbl_inventory_update_history",["appId"=>$appid,"count"=>$uc["records"],"startTime"=>$starttime,"endTme"=>date("Y-m-d H:i:s")]);

		}*/

		$this->mongo_db->switch_db(mongodb);
		$crondata = $this->mongo_db->count("tbl_import_data");
		
		if($crondata > 0){
			
			redirect("admin/cron/update_inv_script");
			
		}
		
	}
	
	public function getInventorycount($database,$table,$aid,$loccode,$column,$itemname){
		
		$this->mongo_db->switch_db($database);
		$sum = 0;
		
		if($table == "tbl_touts"){
						
			if($column == "tlocationcode"){
				
				$this->mongo_db->where(['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active","tlcoation.flag"=>"excel"]);
				
			}else{

				$this->mongo_db->where(['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active","flocation.flag"=>"excel"]);

			}
			
		}elseif($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){

			$this->mongo_db->where(['item.status'=>"Active","tlocation.status"=>"Active","flag"=>"excel"]);
								   
		}
		$data = $this->mongo_db->select(["quantity","item","$column"])->get_where($table,["appId"=>$aid,$column=>$loccode,"item.item_name"=>$itemname]);
		
		if(count($data) > 0){
			
			foreach($data as $d){
				
				$inv = $this->mongo_db->where(array('item.item_name'=>$d["item"]->item_name,"loccode"=>$d[$column]))->get('tbl_inventory')[0];

				if($inv){
					
					if($table == "tbl_touts"){
						
						if($column == "tlocationcode"){
							
							$this->mongo_db->where(["tlcoation.flag"=>"excel","_id"=>new MongoDB\BSON\ObjectID($d["_id"]->{'$id'})])->set(["tlcoation.flag"=>"uexcel"])->update($table);
							
						}else{
							
							$this->mongo_db->where(["flocation.flag"=>"excel","_id"=>new MongoDB\BSON\ObjectID($d["_id"]->{'$id'})])->set(["flocation.flag"=>"uexcel"])->update($table);
							
						}
						
					}else{
					
						$this->mongo_db->where(["flag"=>"excel","_id"=>new MongoDB\BSON\ObjectID($d["_id"]->{'$id'})])->set(["flag"=>"uexcel"])->update($table);		
					
					}
				}

				$sum += intval($d['quantity']);
				
			}
		}
		
		return $sum;
		
	}

	
	public function updateInventoryrecords($appid){
	
	// Issues
		
		$issues = $this->mongo_db->aggregate("tbl_issues",[
			['$match' => ['item.status'=>"Active","tlocation.status"=>"Active","flag"=>"excel"]],
			['$group' => ["_id"=>['loccode'=>'$tlcoationcode','item'=>'$item.item_name'],'total'=>['$sum'=>'$quantity'],'item'=>['$first'=>'$item.item_name']]],
		]);

		$returns = $this->mongo_db->aggregate("tbl_returns",[
			['$match' => ['item.status'=>"Active","tlocation.status"=>"Active","flag"=>"excel"]],
			['$group' => ["_id"=>['loccode'=>'$tlcoationcode','item'=>'$item.item_name'],'total'=>['$sum'=>'$quantity'],'item'=>['$first'=>'$item.item_name']]],
		]);
		
		$adjustments = $this->mongo_db->aggregate("tbl_adjustments",[
			['$match' => ['item.status'=>"Active","tlocation.status"=>"Active","flag"=>"excel"]],
			['$group' => ["_id"=>['loccode'=>'$tlcoationcode','item'=>'$item.item_name'],'total'=>['$sum'=>'$quantity'],'item'=>['$first'=>'$item.item_name']]],
		]);
	
		$tins = $this->mongo_db->aggregate("tbl_touts",[
			['$match' => ['item.status'=>"Active","tlcoation.status"=>"Active","flag"=>"excel"]],
			['$group' => ["_id"=>['loccode'=>'$tlocationcode','item'=>'$item.item_name'],'total'=>['$sum'=>'$quantity'],'item'=>['$first'=>'$item.item_name']]],
		]);
		
		$touts = $this->mongo_db->aggregate("tbl_touts",[
			['$match' => ['item.status'=>"Active","flocation.status"=>"Active","flag"=>"excel"]],
			['$group' => ["_id"=>['loccode'=>'$flcoationcode','item'=>'$item.item_name'],'total'=>['$sum'=>'$quantity'],'item'=>['$first'=>'$item.item_name']]],
		]);
		
		$progress = 0;
		$total = count($issues) + count($returns) + count($adjustments) + count($tins) + count($touts);
		
		foreach($issues as $iss){

			$inv = $this->mongo_db->where(array('item.item_name'=>$iss["item"],"loccode"=>$iss["_id"]->loccode))->inc(["issues"=>$iss["total"],"ending_balance"=>$iss["total"]])->update('tbl_inventory');

			if(gettype($inv) == "object"){
			
				if($inv->getModifiedCount() >= 1){

					$this->mongo_db->where(["flag"=>"excel",'item.item_name'=>$iss["item"],"tlcoationcode"=>$iss["_id"]->loccode])->set(["flag"=>"uexcel"])->update_all("tbl_issues");	

				}
				
			}

			++$progress;
						
			$percentage = ($progress*100)/$total;
			$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>round($percentage)],[]);
			
		}
		
	// Returns	
		
		foreach($returns as $ret){

			$reinv = $this->mongo_db->where(array('item.item_name'=>$ret["item"],"loccode"=>$ret["_id"]->loccode))->inc(["returns"=>$ret["total"],"ending_balance"=>$ret["total"]])->update('tbl_inventory');

			if(gettype($reinv) == "object"){
			
				if($reinv->getModifiedCount() >= 1){

					$this->mongo_db->where(["flag"=>"excel",'item.item_name'=>$ret["item"],"tlcoationcode"=>$ret["_id"]->loccode])->set(["flag"=>"uexcel"])->update_all("tbl_returns");	

				}
				
			}
			
			++$progress;
						
			$percentage = ($progress*100)/$total;
			$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>round($percentage)],[]);
			

		}
		
	// Adjustments	
		
		foreach($adjustments as $adj){

			$adjinv = $this->mongo_db->where(array('item.item_name'=>$adj["item"],"loccode"=>$adj["_id"]->loccode))->inc(["tlcoationcode"=>$adj["total"],"ending_balance"=>$adj["total"]])->update('tbl_inventory');

			if(gettype($adjinv) == "object"){
			
				if($adjinv->getModifiedCount() >= 1){

					$this->mongo_db->where(["flag"=>"excel",'item.item_name'=>$adj["item"],"tlcoationcode"=>$adj["_id"]->loccode])->set(["flag"=>"uexcel"])->update_all("tbl_adjustments");	

				}
			
			}
			++$progress;
						
			$percentage = ($progress*100)/$total;
			$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>round($percentage)],[]);			

		}	

	// Transfer Ins	
		
		foreach($tins as $tin){

			$tinv = $this->mongo_db->where(array('item.item_name'=>$tin["item"],"loccode"=>$tin["_id"]->loccode))->inc(["transfer_ins"=>$tin["total"],"ending_balance"=>$tin["total"]])->update('tbl_inventory');

			if(gettype($tinv) == "object"){
			
				if($tinv->getModifiedCount() >= 1){

					$this->mongo_db->where(["flag"=>"excel",'item.item_name'=>$tin["item"],"tlocationcode"=>$tin["_id"]->loccode])->set(["flag"=>"uexcel"])->update_all("tbl_touts");	

				}
				
			}
			
			++$progress;
						
			$percentage = ($progress*100)/$total;
			$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>round($percentage)],[]);			

		}

	// Transfer Outs
		
		foreach($touts as $tout){

			$toutinv = $this->mongo_db->where(array('item.item_name'=>$tout["item"],"loccode"=>$tout["_id"]->loccode))->inc(["transfer_outs"=>$tout["total"],"ending_balance"=>(-$tout["total"])])->update('tbl_inventory');

			if(gettype($toutinv) == "object"){
			
				if($toutinv->getModifiedCount() >= 1){

					$this->mongo_db->where(["flag"=>"excel",'item.item_name'=>$tout["item"],"flcoationcode"=>$tout["_id"]->loccode])->set(["flag"=>"uexcel"])->update_all("tbl_touts");	

				}
			
			}
			++$progress;
						
			$percentage = ($progress*100)/$total;
			$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>round($percentage)],[]);			

		}
		
	}
	
	public function getProgresscount(){
		
		$this->mongo_db->switch_db(mongodb);
		$appid = $this->input->post("appId");
		
		$pcount = $this->mongo_db->get_where("tbl_progressbar",["appId"=>$appid])[0]["count"];
		echo $pcount;
		
		if($pcount >= 100){
			
			$this->admin->mongoUpdate(mongodb.".tbl_progressbar",["appId"=>$appid],["count"=>0],[]);
			
		}
		
	}
	
}