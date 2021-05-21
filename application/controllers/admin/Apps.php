<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Apps extends CI_Controller {
	
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
	
	public function createApp(){
		
		$this->load->view('admin/apps/createApp');
		
	}
	
	public function editApp($id){
		
		$data['l'] = $this->mongo_db->get_where("tbl_apps",array('appId' => $id));
		$data['mdb'] = $this->mdb;
		$data['database'] = $this->database;
		$this->load->view('admin/apps/editApp',$data);
		
	}
		
	public function insertApp(){
		
		$appname = $this->input->post("appname");
		$sdesc = $this->input->post("sdesc");
		$status = $this->input->post("status");
		
		$id = $this->admin->insert_id("tbl_apps");
		
		$row = $this->mongo_db->get_where("tbl_apps",array('appname' => $appname,"deleted"=>0));
		
		if(count($row) > 0){
			
			echo "App Already Exists";
			exit();
			
		}
		
		$appId = "OID00".explode("_",$id)[1];
		
		$data = array(
		
			"id" => $id,
			"appId" => $appId,
			"appname" => $appname,
			"short_desc" => $sdesc,
			"status" => $status,
			"deleted" => 0,
			"created_date" => date("M-d-y H:i:s"),
		
		);
		
		
		$d = $this->mongo_db->insert("tbl_apps",$data);
		
		if($d){
			
			$database = $this->mdb."_".$appId;
			
			$adjustments = ["chepreference" => 1,"ongreference" => 1,"shippmentdate" => 1,"quantity" => 1,"item" => 1,"tlocation" => 1,"chepprocessdate" => 1,"adjdirection" => 1,"umi" => 1,"appId" => 1,"deleted" => 1,"tlcoationcode" => 1];
			
			$inventory = ["location" => 1,"locname" => 1,"loccode" => 1,"notes" => 1,"last_report_date" => 1,"starting_balance" => 1,"issues" => 1,"returns" => 1,"transfer_ins" => 1,"transfer_outs" => 1,"adjustments" => 1,"ending_balance" => 1,"audit_date2019" => 1,"audit_count2019" => 1,"appId" => 1,"deleted" => 1];
			
			$issues = ["chepreference" => 1,"ongreference" => 1,"shippmentdate" => 1,"quantity" => 1,"item" => 1,"tlocation" => 1,"chepprocessdate" => 1,"umi" => 1,"appId" => 1,"deleted" => 1,"tlcoationcode" => 1];
			
			$items = ["status" => 1,"appId"=>1,"deleted"=>1];
			
			$locations = ["nameid" => 1,"locname" => 1,"loccode" => 1,"address" => 1,"city" => 1,"state" => 1,"zip" => 1,"country" => 1,"status"=>1,"Type"=>1,"import_date"=>1,"appId" => 1,"deleted" => 1];
			
			$returns = ["chepreference" => 1,"ongreference" => 1,"shippmentdate" => 1,"quantity" => 1,"item" => 1,"tlocation" => 1,"chepprocessdate" => 1,"umi" => 1,"appId" => 1,"deleted" => 1,"tlcoationcode" => 1];		
			
			$transfers = ["shipperpo" => 1,"shippmentdate" => 1,"pronum" => 1,"reference" => 1,"item" => 1,"flocation" => 1,"flcoationcode" => 1,"tlcoation" => 1,"tlocationcode" => 1,"quantity" => 1,"reportdate" => 1,"processdate" => 1,"chepprocessdate" => 1,"uploadedetochep" => 1,"reasonforhold" => 1,"appId" => 1,"deleted" => 1];			

			
			$this->admin->create("$database","tbl_location_requests",["Status"=>1]);
			$this->admin->create("$database","tbl_location_submits",["Status"=>1]);
			$this->admin->create("$database","tbl_adjustments",$adjustments);
			$this->admin->create("$database","tbl_inventory",$inventory);
			$this->admin->create("$database","tbl_issues",$issues);
			$this->admin->create("$database","tbl_items",$items);
			$this->admin->create("$database","tbl_locations",$locations);
			$this->admin->create("$database","tbl_returns",$returns);
			$this->admin->create("$database","tbl_touts",$transfers);
//			$this->admin->create("$database","tbl_columns");
			$this->admin->create("$database","settings",["appId"=>1]);
			
			$locfields = ["table" => "tbl_locations",
						  "appId" => $appId,
						  "columns" =>["locname","loccode","address","city","state","zip","country","status","Type","import_date","accounts","notes"],
						  "labels" => ["Location Name","Location Code","Address","City","State","Zip","Country","Status","Type","Import Date","Accounts","Notes"],
						  "dataType" =>["text","text","textarea","text","text","number","text","select","select","date","multiselect","text"]];
			
						$locinventoryfields = ["table" => "tbl_inventory",
    					   "appId" => $appId,
						   "columns" => ["location","locname","loccode","loctype","notes","last_report_date","starting_balance","issues","returns","transfer_ins","transfer_outs","adjustments","ending_balance","audit_date2019","audit_count2019","item"],
						   "labels" => ["Location","Location Name","Location Code","Location Type","Notes","Last Report Date","Starting Balance","Shipments","Pickups","Transfer Ins","transfer Outs","Adjustments","Ending Balance","Audit Date","Audit Count","Item"],
						   "dataType" => ["select","text","text","text","text","date","number","number","number","number","number","number","number","date", "number","select"]
						  ];

			
			$itemfields = ["table" => "tbl_items",
    						"appId" => $appId,
							"columns" => [ 
								"item_code", 
								"item_name",
								"status"
							],
							"labels" => [ 
								"Item Code", 
								"Item Name",
								"Status"
							],
							"dataType" => [ 
								"text", 
								"text",
								"text"
							]];
			
			$transferfields = ["table" => "tbl_touts",
    							"appId" => $appId,
								"columns" => [ 
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
									"reasonforhold"
								],
								"labels" => [ 
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
									"Reason For Hold"
								],
								"dataType" => [ 
									"text", 
									"date", 
									"text", 
									"text", 
									"select", 
									"select", 
									"text", 
									"select", 
									"text", 
									"number", 
									"date", 
									"select", 
									"date", 
									"date", 
									"text", 
									"select", 
									"select"
								]];
			
			$issuesfields = ["table" => "tbl_issues",
							"appId" => $appId,
							"columns" => [ 
								"chepreference", 
								"ongreference", 
								"shippmentdate", 
								"quantity", 
								"item", 
								"tlocation", 
								"tlcoationcode", 
								"chepprocessdate", 
								"umi"
							],
							"labels" => [ 
								"Vendor Reference", 
								"Ongweoweh Reference", 
								"Shipment Date", 
								"Quantity", 
								"Item", 
								"To Location", 
								"To Location Code", 
								"Vendor Process Date", 
								"UMI"
							],
							"dataType" => [ 
								"text", 
								"text", 
								"date", 
								"number", 
								"select", 
								"select", 
								"text", 
								"date", 
								"text"
							]];
			
			$returnsfields = ["table" => "tbl_returns",
								"appId" => $appId,
								"columns" => [ 
									"chepreference", 
									"ongreference", 
									"shippmentdate", 
									"quantity", 
									"item", 
									"tlocation", 
									"tlcoationcode", 
									"chepprocessdate", 
									"umi"
								],
								"labels" => [ 
									"Vendor Reference", 
									"Ongweoweh Reference", 
									"Shipment Date", 
									"Quantity", 
									"Item", 
									"To Location", 
									"To Location Code", 
									"Vendor Process Date", 
									"UMI"
								],
								"dataType" => [ 
									"text", 
									"text", 
									"date", 
									"number", 
									"select", 
									"select", 
									"text", 
									"date", 
									"text"
								]];
			
			$adjustfields = ["table" => "tbl_adjustments",
							"appId" => $appId,
							"columns" => [ 
								"chepreference", 
								"ongreference", 
								"shippmentdate", 
								"quantity", 
								"item", 
								"tlocation", 
								"tlcoationcode", 
								"chepprocessdate", 
								"adjdirection", 
								"umi"
							],
							"labels" => [ 
								"Vendor Reference", 
								"Ongweoweh Reference", 
								"Shipment Date", 
								"Quantity", 
								"Item", 
								"To Location", 
								"To Location Code", 
								"Vendor Process Date", 
								"Adjustment Direction", 
								"UMI"
							],
							"dataType" => [ 
								"text", 
								"text", 
								"date", 
								"number", 
								"select", 
								"select", 
								"text", 
								"date", 
								"select", 
								"text"
							]];
			
			$fields = [$locfields,$locinventoryfields,$itemfields,$transferfields,$issuesfields,$returnsfields,$adjustfields];
			
			$this->admin->mongoInsert("$database.settings",$fields,"bulk");	
			
			$mng = $this->admin->Mconfig();
		
			
			echo "success";
			
		}else{
			
			echo "error";
			
		}
		
	}


	public function updateApp(){
		
		$appname = $this->input->post("appname");
		$sdesc = $this->input->post("sdesc");
		$status = $this->input->post("status");
		$id = new MongoDB\BSON\ObjectID($this->input->post("id"));
			
		$lchk = $this->mongo_db->get_where("tbl_apps",array("appname"=>$appname,"_id"=>$id,"deleted"=>0));

		if($lchk[0]["appname"]==$appname){

			
		}else{
			
			$echk1 = $this->mongo_db->get_where("tbl_apps",array("appname"=>$appname,"deleted"=>0));	
			if(count($echk1)> 0){
				echo "App Name Already Exists";
				exit();
			}else{
				
			}
			
		}
		
		$data = array(
		
			"appname" => $appname,
			"short_desc" => $sdesc,
			"status" => $status
		
		);
		
		
		$d = $this->mongo_db->where(array('_id'=>$id))->set($data)->update('tbl_apps');
		
		if($d){
	
			echo "success";
			
		}else{
			
			echo "error";
			
		}
		
	}
	
	
	public function delApp($id){
		
		$lid = new MongoDB\BSON\ObjectID($id);
		
		$adata = $this->mongo_db->get_where("tbl_apps",array('_id'=>$lid))[0];
		$aId = $adata['appId'];
		
		$d = $this->mongo_db->where(array('_id'=>$lid))->set(array("deleted"=>1))->update('tbl_apps');
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'error';
			
		}
		
	}

	public function delRequest(){
		
		$lid = new MongoDB\BSON\ObjectID($this->input->post('id'));
		$d = $this->admin->mongoDelete("$this->database.location_requests",array('_id'=>$lid),[]);
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'error';
			
		}
		
	}

	public function editRequest($id){	

		$data['l']= $this->admin->getRow("",['_id' => new MongoDB\BSON\ObjectID($id)],[],"$this->database.location_requests");
		$data['database'] = $this->database;
		$data['reqid']=$id;
		$this->load->view('admin/apps/editRequest',$data);
	}

	public function editSubmit($id){	
		$data['l']=$this->admin->getRow("",['_id' => new MongoDB\BSON\ObjectID($id)],[],"$this->database.location_submits");
		$data['reqid']=$id;
		$this->load->view('admin/apps/editSubmit',$data);
	}

	public function updateRequest(){
		$reqid = $this->input->post('reqid');
		$locations = $this->input->post("location");
		$mng= $this->admin->Mconfig();
		
		$reqdata = $this->admin->getRow("",['_id' => new MongoDB\BSON\ObjectID($reqid)],[],"$this->database.location_requests");
		
		foreach ($locations as $key => $value) {
			$ld = $this->admin->getRow("",["deleted"=>0,"loccode"=>$value],[],"$this->database.tbl_locations");
			$ndata = [];
			$ndata["Date"] = date("M-d-Y H:i:s");
			$ndata["LocationId"] = $ld->_id;
			$ndata["loccode"] = strval($value);
			$ndata["LocationName"] = $ld->locname;
			$ndata["Type"] = 'from';
			$ndata["status"] = $ld->status;
			$loc[] = $ndata;
			$this->mongo_db->where(array('email'=>$reqdata->user))->push("locations",$ndata)->update('tbl_auths');
		}
		
		$data = array("Status"=>"Approved","Updated_Date"=>date("M-d-Y"));
		$this->admin->mongoUpdate("$this->database.location_requests",['_id'=>new MongoDB\BSON\ObjectID($reqid)],$data,[]);
		echo json_encode(array("Status"=>"Success","Message"=>"Locations has been successfully updated."));
	}


	public function updateSubmit(){
		$reqid = $this->input->post('reqid');
		$data = $this->input->post();
		$mng= $this->admin->Mconfig();
		$data['nameid'] = $this->input->post('locname')." - ".$this->input->post('loccode');
		$data['status'] = 'Active';
		$data["locid"] = $this->admin->insert_id("tbl_locations",$this->database,"locid");


		unset($data['reqid']);
		$this->admin->mongoInsert("$this->database.tbl_locations",$data);
		$this->admin->mongoUpdate("$this->database.location_submits",array('_id'=>new MongoDB\BSON\ObjectID($reqid)),array("Status"=>"Approved"),[]);
		echo json_encode(array("Status"=>"Success","Message"=>"Locations has been successfully updated."));
	}
	
	public function users(){
		
		$this->load->view("admin/apps/users");
		
	}
	
	public function editUser($id){
		
		$data['l'] = $this->mongo_db->get_where("tbl_apps",array('appId' => $id));
		$data['database'] = $this->database;
		$this->load->view('admin/apps/updateUser',$data);
		
	}
	
	public function delLocation($lcode,$id){
		
		$uid = new MongoDB\BSON\ObjectID($id);
		
		// $udata = $this->mongo_db->get_where("tbl_auths",array("_id"=>$uid));
		
		$d = $this->mongo_db->where(array('_id'=>$uid))->pull("locations",array("loccode"=>$lcode))->update('tbl_auths');
		
		echo "success";
		
	}

	public function delReqData(){
		
		$this->mongo_db->switch_db($this->database);
		
		$id = $this->input->post('id');
		$table = $this->input->post('table');
		$uid = new MongoDB\BSON\ObjectID($id);
		
		$exdata = $this->mongo_db->get_where("$table",["_id"=>$uid])[0];
		
		$column = [];
		
		if($table == "tbl_adjustments" || $table == "tbl_issues" || $table == "tbl_returns"){

			$column[] = "tlcoationcode";
			
		}elseif($table == "tbl_touts"){
			
			$column[] = "flcoationcode";
			$column[] = "tlocationcode";
			
		}

		foreach($column as $col){
		
			$oldinvdata = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$exdata[$col],"item.item_name"=>$exdata["item"]->item_name])[0];

			$dval['starting_balance'] = intval($oldinvdata["starting_balance"]);
			$dval['issues'] = intval($oldinvdata["issues"]);
			$dval['returns'] = intval($oldinvdata["returns"]);
			$dval['transfer_ins'] = intval($oldinvdata["transfer_ins"]);
			$dval['transfer_outs'] = intval($oldinvdata["transfer_outs"]);
			$dval['adjustments'] = intval($oldinvdata["adjustments"]);

			if($table == "tbl_touts"){

				if($col == "tlocationcode"){

					$dval['transfer_ins'] = intval($oldinvdata["transfer_ins"]) - intval($exdata["quantity"]);

				}else{

					$dval['transfer_outs'] = intval($oldinvdata["transfer_outs"]) - intval($exdata["quantity"]);

				}

			}elseif($table == "tbl_issues"){

				$dval['issues'] = intval($oldinvdata["issues"]) - intval($exdata["quantity"]);

			}elseif($table == "tbl_returns"){

				$dval['returns'] = intval($oldinvdata["returns"]) - intval($exdata["quantity"]);

			}elseif($table == "tbl_adjustments"){

				$dval['adjustments'] = intval($oldinvdata["adjustments"]) - intval($exdata["quantity"]);

			}

			$ending_balance = ($dval['starting_balance'] + $dval['issues'] + $dval['returns'] + $dval['transfer_ins'] - $dval['transfer_outs'] + $dval['adjustments']); 

			$dval['ending_balance'] = intval($ending_balance);

			$this->mongo_db->where(["loccode"=>$exdata[$col],"item.item_name"=>$exdata["item"]->item_name])->set($dval)->update("tbl_inventory");

		}
		
		
		$d = $this->admin->mongoDelete("$this->database.$table",array('_id'=>$uid),[]);
		
		echo "success";
		
	}

	public function addColumn(){
		
		$this->mongo_db->switch_db($this->database);
		
		$appid = $this->input->post("appid");
		$cname = $this->input->post("cName");
		$ctype = $this->input->post("cType");
		$ncolumn = preg_replace('/\s+/', '', strtolower($cname));
		$mng = $this->admin->Mconfig();
		$row = $this->admin->getRow($mng,["table"=>'tbl_inventory','appId'=>$appid],[],"$this->database.settings");
		if(in_array($ncolumn, $row->columns)){
			echo json_encode(array("Status"=>"Wrong","Message"=>"Already Existed"));
		}else{
			array_push($row->columns, $ncolumn);
			array_push($row->labels, $cname);
			array_push($row->dataType, $ctype);
			$this->mongo_db->where(array('table'=>"tbl_inventory",'appId'=>$appid))->set(["columns"=>$row->columns,"labels"=>$row->labels,"dataType"=>$row->dataType])->update('settings');
			echo json_encode(array("Status"=>"Success","Message"=>"Successfully Inserted"));
		}
		
	}


	public function addFilter(){
		//echo '<pre>';print_r($_POST);exit;
		$this->mongo_db->switch_db($this->database);
		$cause = $this->input->post('cause');
		$field = $this->input->post('field');
		$value = $this->input->post('value');
		$svalue = $this->input->post('svalue');
		$dvalue = $this->input->post('dvalue');
		$appid = $this->input->post('id');
		$table = $this->input->post('table');
		$item = $this->input->post('item');

        $filter_from = $this->input->post('filter_from');
		$draw = $this->input->post('draw');
		$srow = $this->input->post('start');
		$rowperpage = $this->input->post('length'); // Rows display per page
		$columnIndex = $this->input->post('order')[0]['column']; // Column index
		$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
		$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
		$searchValue = $this->input->post('search')['value']; // Search value
		
		if($columnName == "id"){
			
			$columnName = "_id";
			
		}else{
			
			$columnName = $columnName;
			
		}
		
		if($columnSortOrder == "asc"){
			
			$sortOrder = -1;
			
		}else{
			
			$sortOrder = 1;
			
		}

		$this->session->unset_userdata('export_ids');
		$this->session->unset_userdata('export_table_name');
		
		$cdays = [];
		$i = 0;
		foreach($value as $kk => $val){
			if($val == "is during the previous" || $val == "is before the previous" || $val == "is during the next"){
				$cdays[] = $this->input->post("dvalue")[$i];
				$i++;
			}else{
				$cdays[] = "";
			}
        }
		$dvalue=$cdays;
		
		if(count($cause) > 0){
		
			$query=[];
			$queryor=[];
			foreach ($cause as $key => $val) {
				if($field[$key] == 'import_date' || $field[$key] == 'shippmentdate' || $field[$key] == 'reportdate' || $field[$key] == 'processdate' || $field[$key] == 'chepprocessdate' || $field[$key] == 'last_report_date' || $field[$key] == 'audit_date2019'){
					/* if(strtotime($svalue[$key]) !== false){
						$svalue[$key] = date('m-d-Y', strtotime($svalue[$key]));
					} */
				}
				if($field[$key] == "quantity" || $field[$key] == "starting_balance" || $field[$key] == "issues" || $field[$key] == "returns" || $field[$key] == "transfer_ins" || $field[$key] == "transfer_outs" || $field[$key] == "adjustments" || $field[$key] == "audit_count2019" || $field[$key] == "ending_balance"){
						$svalue[$key] = intval($svalue[$key]);
				}
				if($item != ""){
					array_push($query, array("item.item_name"=>$item));	
				}
				if($field[$key] == "item"){
						$field[$key] = "item.item_name";
				}
				if($field[$key] == "tlcoation"){
						$field[$key] = "tlcoation.locname";
				}
				if($field[$key] == "flocation"){
						$field[$key] = "flocation.locname";
				}
				if($field[$key] == "tlocation"){
						$field[$key] = "tlocation.locname";
				}
				if($field[$key] == "locname" && $table != "tbl_locations"){
						$field[$key] = "locname.locname";
				}
				if(($val == 'where' || $val == 'and')  && $value[$key] == 'contains'){
				array_push($query, array($field[$key]=>['$regex'=>$svalue[$key],'$options'=>'i']));	
				}
				else if(($val == 'where' || $val == 'and') && $value[$key] == 'does not contain'){
				array_push($query, array($field[$key]=>['$regex'=>'^((?!'.$svalue[$key].').)*$','$options'=>'i']));	
				}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is'){
				array_push($query, array($field[$key]=>$svalue[$key]));		
				}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is not'){
				array_push($query, array($field[$key]=>['$ne'=>$svalue[$key]]));		
				}
				else if(($val == 'where' || $val == 'and') && $value[$key] == 'starts with'){
				array_push($query, array($field[$key]=>['$regex'=>'^'.$svalue[$key],'$options'=>'i']));
				}else if(($val == 'where' || $val == 'and') && $value[$key] == 'ends with'){
				array_push($query, array($field[$key]=>['$regex'=>$svalue[$key].'$','$options'=>'i']));
				}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is blank'){
				    if($field[$key] == "accounts"){
						array_push($query, array($field[$key]=>[]));
					}else{
						array_push($query, array($field[$key]=>''));
					}		
				}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is not blank'){
				if($field[$key] == "accounts"){
						array_push($query, array($field[$key]=>['$ne'=>[],'$exists' => true]));
					}else{
						array_push($query, array($field[$key]=>['$ne'=>'','$exists' => true]));
					}		
				}
				else if(($val == 'where' || $val == 'and') && $value[$key] == 'higher than'){

				array_push($query, array($field[$key]=>['$gt'=>intval($svalue[$key])]));		
				}
				else if(($val == 'where' || $val == 'and') && $value[$key] == 'lower than'){
				array_push($query, array($field[$key]=>['$lt'=>intval($svalue[$key])]));		
				}
				else if(($val == 'where' || $val == 'and') && $value[$key] == 'is any'){
				array_push($query, array($field[$key]=>['$ne'=>'']));		
				}
                elseif(($val == 'where' || $val == 'and') && $value[$key] == "is during the current"){
					$dates = $this->getDays($svalue[$key]); 
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($query, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
			    }elseif(($val == 'where' || $val == 'and') && $value[$key] == "is during the previous"){
					$dates = $this->getDayscount($dvalue[$key],"minus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($query, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
				}elseif(($val == 'where' || $val == 'and') && $value[$key] == "is before the previous"){
					$dates = $this->getDayscount($dvalue[$key],"minus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];

					array_push($query, array($field[$key]=>['$lt'=>$start,'$ne'=>'']));
				}elseif(($val == 'where' || $val == 'and') && ($value[$key] == "is during the next" || $value[$key] == "is after the next")){
					$dates = $this->getDayscount($dvalue[$key],"plus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($query, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
				}elseif(($val == 'where' || $val == 'and') && ($value[$key] == "is before" || $value[$key] == "is after")){
					if($value[$key] == "is before"){

					array_push($query, array($field[$key]=>['$lt'=>$svalue[$key],'$ne'=>'']));	

					}elseif($value[$key] == "is after"){
                   
				    array_push($query, array($field[$key]=>['$gt'=>$svalue[$key],'$ne'=>'']));
						
					}
				}elseif(($val == 'where' || $val == 'and') && ($value[$key] == "is today or before" ||$value[$key] == "is today or after" || $value[$key] == "is before today" || $value[$key] == "is after today" || $value[$key] == "is after current time" || $value[$key] == "is before current time")){

				    $date = date("Y-m-d");               

					if($value[$key] == "is today or before" || $value[$key] == "is before today" || $value[$key] == "is before current time"){
						
						if($value[$key] == "is before today" || $value[$key] == "is before current time"){
							array_push($query, array($field[$key]=>['$lt'=>$date,'$ne'=>'']));
						}else{
							array_push($query, array($field[$key]=>['$lte'=>$date,'$ne'=>'']));
						}	
					}elseif($value[$key] == "is today or after" || $value[$key] == "is after today" || $value[$key] == "is after current time"){
						if($value[$key] == "is after today" || $value[$key] == "is after current time"){
							array_push($query, array($field[$key]=>['$gt'=>$date,'$ne'=>'']));
						}else{
							array_push($query, array($field[$key]=>['$gte'=>$date,'$ne'=>'']));
						}
					}

			    }elseif(($val == 'where' || $val == 'and') && $value[$key] == "is today"){
					 $date = date("Y-m-d");
					 array_push($query, array($field[$key]=>$date));
			    }

				elseif(($val == 'where' || $val == 'or')  && $value[$key] == 'contains'){
				array_push($queryor, array($field[$key]=>['$regex'=>$svalue[$key],'$options'=>'i']));	
				}
				else if(($val == 'where' || $val == 'or') && $value[$key] == 'does not contain'){
				array_push($queryor, array($field[$key]=>['$regex'=>'^((?!'.$svalue[$key].').)*$','$options'=>'i']));	
				}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is'){
				array_push($queryor, array($field[$key]=>$svalue[$key]));		
				}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is not'){
				array_push($queryor, array($field[$key]=>['$ne'=>$svalue[$key]]));		
				}
				else if(($val == 'where' || $val == 'or') && $value[$key] == 'starts with'){
				array_push($queryor, array($field[$key]=>['$regex'=>'^'.$svalue[$key],'$options'=>'i']));
				}else if(($val == 'where' || $val == 'or') && $value[$key] == 'ends with'){
				array_push($queryor, array($field[$key]=>['$regex'=>$svalue[$key].'$','$options'=>'i']));
				}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is blank'){
				if($field[$key] == "accounts"){
						array_push($queryor, array($field[$key]=>[]));
					}else{
						array_push($queryor, array($field[$key]=>''));
					}		
				}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is not blank'){
				if($field[$key] == "accounts"){
						array_push($queryor, array($field[$key]=>['$ne'=>[],'$exists' => true]));
					}else{
						array_push($queryor, array($field[$key]=>['$ne'=>'','$exists' => true]));
					}		
				}
				elseif(($val == 'where' || $val == 'or') && $value[$key] == "is during the current"){
					$dates = $this->getDays($svalue[$key]); 
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($queryor, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
			    }elseif(($val == 'where' || $val == 'or') && $value[$key] == "is during the previous"){
					$dates = $this->getDayscount($dvalue[$key],"minus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($queryor, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
				}elseif(($val == 'where' || $val == 'or') && $value[$key] == "is before the previous"){
					$dates = $this->getDayscount($dvalue[$key],"minus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($queryor, array($field[$key]=>['$lt'=>$start,'$ne'=>'']));
				}elseif(($val == 'where' || $val == 'or') && ($value[$key] == "is during the next" || $value[$key] == "is after the next")){
					$dates = $this->getDayscount($dvalue[$key],"plus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($queryor, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
				}elseif(($val == 'where' || $val == 'or') && ($value[$key] == "is before" || $value[$key] == "is after")){
					if($value[$key] == "is before"){

					array_push($queryor, array($field[$key]=>['$lt'=>$svalue[$key],'$ne'=>'']));	

					}elseif($value[$key] == "is after"){
                   
				    array_push($queryor, array($field[$key]=>['$gt'=>$svalue[$key],'$ne'=>'']));
						
					}
				}elseif(($val == 'where' || $val == 'or') && ($value[$key] == "is today or before" ||$value[$key] == "is today or after" || $value[$key] == "is before today" || $value[$key] == "is after today" || $value[$key] == "is after current time" || $value[$key] == "is before current time")){

				    $date = date("Y-m-d");               

					if($value[$key] == "is today or before" || $value[$key] == "is before today" || $value[$key] == "is before current time"){
						
						if($value[$key] == "is before today" || $value[$key] == "is before current time"){
							array_push($queryor, array($field[$key]=>['$lt'=>$date,'$ne'=>'']));
						}else{
							array_push($queryor, array($field[$key]=>['$lte'=>$date,'$ne'=>'']));
						}	
					}elseif($value[$key] == "is today or after" || $value[$key] == "is after today" || $value[$key] == "is after current time"){
						if($value[$key] == "is after today" || $value[$key] == "is after current time"){
							array_push($queryor, array($field[$key]=>['$gt'=>$date,'$ne'=>'']));
						}else{
							array_push($queryor, array($field[$key]=>['$gte'=>$date,'$ne'=>'']));
						}
					}

			    }elseif(($val == 'where' || $val == 'or') && $value[$key] == "is today"){
					 $date = date("Y-m-d");
					 array_push($queryor, array($field[$key]=>$date));
			    }
				else if(($val == 'where' || $val == 'or') && $value[$key] == 'higher than'){
				array_push($queryor, array($field[$key]=>['$gt'=>intval($svalue[$key])]));		
				}
				else if(($val == 'where' || $val == 'or') && $value[$key] == 'lower than'){
				array_push($queryor, array($field[$key]=>['$lt'=>intval($svalue[$key])]));		
				}
				else if(($val == 'where' || $val == 'or') && $value[$key] == 'is any'){
				array_push($queryor, array($field[$key]=>['$ne'=>'']));		
				}
			}

			$mng = $this->admin->Mconfig();
			if(count($queryor) > 0){
				array_push($query, array('$or'=>$queryor));	
				$type='$or';
			}else{
				$type='$and';
			}
			if($filter_from == "form_modal"){
				$this->session->set_userdata('filter_type', $type);
				$this->session->set_userdata('filter_query', $query);
				$this->session->set_userdata('filter_appid', $appid);
				$this->session->set_userdata('filter_table', $table);
			}
			
			if($filter_from != "form_modal" || $filter_from == ""){
				$type = $this->session->userdata('filter_type');
				$query = $this->session->userdata('filter_query');
				$appid = $this->session->userdata('filter_appid');
				$table = $this->session->userdata('filter_table');
			}   
			
			$command = new MongoDB\Driver\Command([
				'aggregate'=>$table,
				'cursor' => new stdClass,
				'pipeline'=>[
				    ['$sort'=>["_id"=>-1]],
				    ['$sort'=>["$columnName"=>$sortOrder]],
					['$match'=>[$type=>$query,"appId"=>$appid ]],
					['$skip'=>intval($srow)],
				    ['$limit'=>intval($rowperpage)]
				]
			]);

			if($table == "tbl_touts"){
				$data = $this->mongo_db->aggregate("$table",[
					['$match' => ['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active"]],
					/*['$match' => ['$or' => 
						[['tlcoation.status'=>"Active"],
						['tlcoation.locname'=>""]]
					 ]],*/
					['$match'=>[$type=>$query,"appId"=>$appid ]],
//					['$count'=>"total"],
					['$project' => ["_id"=>1]]
				]);
			}elseif($table == "tbl_locations"){
				
				$data = $this->mongo_db->aggregate("$table",[
					['$match'=>[$type=>$query,"appId"=>$appid ]],
//					['$count'=>"total"],
					['$project' => ["_id"=>1]]
				]);
				
			}elseif($table == "tbl_items"){
					$data = $this->mongo_db->aggregate("$table",[
						['$match'=>[$type=>$query,"appId"=>$appid ]],
//						['$count'=>"total"],
						['$project' => ["_id"=>1]]
					]);
			}else{
			
				if($table == "tbl_inventory"){
					
					$data = $this->mongo_db->aggregate("$table",[
						['$match'=>[$type=>$query,"appId"=>$appid ]],
						['$match' => ['item.item_name'=>"$item"]],
						['$match' => ['item.status'=>"Active","locname.status"=>"Active"]],
//						['$count'=>"total"],
						['$project' => ["_id"=>1]]
					]);
					
				}else{
				
					$data = $this->mongo_db->aggregate($table,[
						['$match'=>[$type=>$query,"appId"=>$appid ]],
						['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
						['$project' => ["_id"=>1]]
//						['$count'=>"total"],
					]);
				}
			}
				
			
			if(count($data) > 0){
					
					$totalRecords = count($data);
					
			}else{
					
					$totalRecords = 0;
					
			}
			$cursor = $mng->executeCommand("$this->database", $command);
		
		}else{
			
			$cursor = $this->admin->getArray("",[],[],"$this->database.$table");
			
		}

		
		$out = $this->common->getFiltervalues($cursor,$table);
		
		foreach($data as $r1){
			
			$r2 = json_decode(json_encode($r1), true);
			$ids[] = new MongoDB\BSON\ObjectID($r2["_id"]{'$id'});
		}
		
//		echo '<pre>';print_r($ids);exit;
		
		$this->session->set_userdata('export_ids', $ids);
		$this->session->set_userdata('export_table_name', $table);
		
		$results = ["draw" => intval($draw),"iTotalRecords" => $totalRecords,"iTotalDisplayRecords" => $totalRecords,"aaData" => $out];
		
		echo json_encode($results);
	}					
	
	public function getLocations(){
		
		$this->mongo_db->switch_db($this->database);
		
		$draw = $this->input->post('draw');
		$row = $this->input->post('start');
		$rowperpage = $this->input->post('length'); // Rows display per page
		$columnIndex = $this->input->post('order')[0]['column']; // Column index
		$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
		
		$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
		$searchValue = $this->input->post('search')['value']; // Search value
		
		$totalRecords = $this->mongo_db->count("tbl_locations");
		
		if($columnName == "locid"){
			
			$columnName = "_id";
			
		}else{
			
			$columnName = $columnName;
			
		}
		
		if($searchValue != ''){
		
			$this->mongo_db->where_or([
					'locid'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'nameid'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'loccode'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'address'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'city'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'state'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'zip'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'country'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'status'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'Type'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'import_date'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'accounts'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'notes'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'cdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
			]);
			
		}
		$filteredRecords = $this->mongo_db->count("tbl_locations");
		
		if($searchValue != ''){
		
			$this->mongo_db->where_or([
					'locid'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'nameid'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'locname'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'loccode'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'address'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'city'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'state'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'zip'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'country'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'status'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'Type'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'import_date'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'accounts'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'notes'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'cdate'=>new MongoDB\BSON\Regex($searchValue,'i'),
			]);
			
		}
		$empRecords = $this->mongo_db->order_by(array("$columnName"=>"$columnSortOrder"))->limit(intval($rowperpage))->offset(intval($row))->get("tbl_locations");
		
		$out = [];
		
		foreach($empRecords as $key=>$row){
//			$row["Sno"] = $key+1;
			$row["locid"] = $row["locid"];
			$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]->{'$id'}.'">';
			
			$date = "";
			$time = "";
			if($row["import_date"] != ''){
				$date = $this->common->getConverteddate(explode(" ",$row["import_date"])[0]);
				$time = $row["import_time"]; 
				
				$row["import_date"] = ($row["import_date"] != "") ? date("m-d-Y",strtotime($row["import_date"]))." ".$time : "";
			}
			
			if($row["nameid"]){
				
				$row["nameid"] = $row["locname"]."-".$row["loccode"];
				
			}
			
			if(is_array($row["accounts"])){
				
				$row["accounts"] = $row["accounts"];
				
			}else{
				
				$row["accounts"] = explode(",",$row["accounts"]);
				
			}
			
			
			$row["Actions"] = '<a href="javascript:void(0)" class="editLocate" lid="'.$row["_id"]->{'$id'}.'"  lcode="'.$row["loccode"].'" lname="'.$row["locname"].'" address="'.$row["address"].'" city="'.$row["city"].'" state="'.$row["state"].'" zip="'.$row["zip"].'" country="'.$row["country"].'" status="'.$row["status"].'"" Type="'.$row["Type"].'" impdate="'.$date.'" time="'.$time.'" accounts="'.implode(",",$row["accounts"]).'" notes="'.$row["notes"].'" data-toggle="modal" data-target=".bs-example-modal-lg"><i class="far fa-edit"></i></a> | <a href="javascript:void(0)" id="'.$row["_id"]->{'$id'}.'" onclick="archiveFunction(this.id)"><i class="fas fa-trash-alt" style="color: red"></i></a>';
			array_push($out,$row);
		}
		
		## Response
		$response = array(
		  "draw" => intval($draw),
		  "iTotalRecords" => $totalRecords,
		  "iTotalDisplayRecords" => $filteredRecords,
		  "aaData" => $out
		);

		echo json_encode($response);

	}

	public function getItems(){
		
		$this->mongo_db->switch_db($this->database);
		
		$draw = $this->input->post('draw');
		$row = $this->input->post('start');
		$rowperpage = $this->input->post('length'); // Rows display per page
		$columnIndex = $this->input->post('order')[0]['column']; // Column index
		$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
		$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
		$searchValue = $this->input->post('search')['value']; // Search value
		
		if($columnName == "id"){
			
			$columnName = "_id";
			
		}else{
			
			$columnName = $columnName;
			
		}
		
		$totalRecords = $this->mongo_db->count("tbl_items");

		if($searchValue != ''){
		
			$this->mongo_db->where_or([
					'id'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'item_code'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'item_name'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'status'=>new MongoDB\BSON\Regex($searchValue,'i'),
			]);
			
		}
		$filteredRecords = $this->mongo_db->count("tbl_items");
		
		if($searchValue != ''){
		
			$this->mongo_db->where_or([
					'id'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'item_code'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'item_name'=>new MongoDB\BSON\Regex($searchValue,'i'),
					'status'=>new MongoDB\BSON\Regex($searchValue,'i'),
			]);
			
		}
		$empRecords = $this->mongo_db->order_by(array("$columnName"=>"$columnSortOrder"))->limit(intval($rowperpage))->offset(intval($row))->get("tbl_items");
		
		$out = [];
		
		foreach($empRecords as $key=>$row){
			$row["id"] = $row['id'];
			$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]->{'$id'}.'">';
			$row["Actions"] = '<a href="javascript:void(0)" class="editItem" icode="'.$row['item_code'].'" iname="'.str_replace('"',"'",$row['item_name']).'" status="'.$row['status'].'" iid="'.$row["_id"]->{'$id'}.'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]->{'$id'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';
			array_push($out,$row);
		}
		
		## Response
		$response = array(
		  "draw" => intval($draw),
		  "iTotalRecords" => $totalRecords,
		  "iTotalDisplayRecords" => $filteredRecords,
		  "aaData" => $out
		);

		echo json_encode($response);
		
	}
	
	public function getTransfers(){
		
		$this->mongo_db->switch_db($this->database);
	
		$draw = $this->input->post('draw');
		$srow = $this->input->post('start');
		$rowperpage = $this->input->post('length'); // Rows display per page
		$columnIndex = $this->input->post('order')[0]['column']; // Column index
		$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
		$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
		$searchValue = $this->input->post('search')['value']; // Search value
		
		if($columnName == "id"){
			
			$columnName = "_id";
			
		}else{
			
			$columnName = $columnName;
			
		}
		
		if($columnSortOrder == "asc"){
			
			$sortOrder = -1;
			
		}else{
			
			$sortOrder = 1;
			
		}
		
		$totalRecords = $this->admin->getTotalrecords("tbl_touts","item","flocation","tlcoation");
		
//		$match = "";
		
		if($searchValue != ''){
		
//			$sort =  ['$sort'=>["$columnName"=>"$sortOrder"]];
			$match = ['$match' => ['$or'=>[
						["id"=>new MongoDB\BSON\Regex($searchValue,'i')],
						["shipperpo"=>new MongoDB\BSON\Regex($searchValue,'i')],
						['shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['pronum'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['reference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['item.item_name'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['flocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlcoation.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['quantity'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['reportdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['time'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['user'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['processdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepprocessdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepumi'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['uploadedetochep'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['reasonforhold'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['transactionid'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['cdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['flcoationcode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlocationcode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['date'=>new MongoDB\BSON\Regex($searchValue,'i')],
					 ]]];
			
		}else{
			
			$match = ['$sort'=>["$columnName"=>$sortOrder]];
			
		}
		
		$empRecords = $this->mongo_db->aggregate("tbl_touts",[
//				['$sort'=>["_id"=>-1]],
//				['$sort'=>["$columnName"=>$sortOrder]],
				$match,
				['$match' => ['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active"]],
				/*['$match' => ['$or' => 
							  	[['tlcoation.status'=>"Active"],
							 	['tlcoation.locname'=>""],
							 	['tlcoation.locname'=>null]]
							 ]],*/				
							 ['$skip'=>intval($srow)],
							 ['$limit'=>intval($rowperpage)],
//				$sort
			]);
		
		$out = [];
		
		foreach($empRecords as $key=>$row){
			
			$shipmentdate = "";
			$processdate = "";
			$chepprocessdate = "";
			$reportdate = "";
			
				$shipmentdate=date("m-d-Y",strtotime($row["shippmentdate"]));
				$processdate=date("m-d-Y",strtotime($row["processdate"]));
				$chepprocessdate=date("m-d-Y",strtotime($row["chepprocessdate"]));
				$reportdate=date("m-d-Y",strtotime($row["reportdate"]));
			
				$eshipmentdate=$row["shippmentdate"];
				$eprocessdate=$row["processdate"];
				$echepprocessdate=$row["chepprocessdate"];
				$ereportdate=$row["reportdate"];
			
			
				$row["shippmentdate"] = ($row["shippmentdate"] != "") ? $shipmentdate : "";
				$row["processdate"] = ($row["processdate"] != "") ? $processdate : "";
				$row["chepprocessdate"] = ($row["chepprocessdate"] != "") ? $chepprocessdate : "";
				$row["reportdate"] = ($row["reportdate"] != "") ? $reportdate : "";			
			
				$row["id"] = $row['id'];
				
				$row["check"] = '<input type="checkbox" class="check" name="lid" srow="'.$columnName.'-'.$rowperpage.'"  value="'.$row["_id"]->{'$id'}.'">';
				$row["Actions"] = '<a href="javascript:void(0)" class="editTransfer" lid="'.$row["_id"]->{'$id'}.'" shipperpo="'.$row["shipperpo"].'" shippmentdate="'.$eshipmentdate.'" pronum="'.$row["pronum"].'" reference="'.$row["reference"].'" item="'.$row["item"]->item_name.'" flocation="'.$row["flocation"]->locname.'" flcoationcode="'.$row["flcoationcode"].'" tlcoation="'.$row["tlcoation"]->locname.'" tlocationcode="'.$row["tlocationcode"].'" quantity="'.$row["quantity"].'" reportdate="'.$ereportdate.'" time="'.$row["reportdate_time"].'" user="'.$row["user"].'" processdate="'.$eprocessdate.'" chepprocessdate="'.$echepprocessdate.'" chepumi="'.$row["chepumi"].'" uploadedetochep="'.$row["uploadedetochep"].'" reasonforhold="'.$row["reasonforhold"].'" transactionid="'.$row["transactionid"].'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]->{'$id'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';
			
				$row["item"] = $row["item"]->item_name;
				$row["flocation"] = $row["flocation"]->locname;
				$row["tlcoation"] = $row["tlcoation"]->locname;
			
				array_push($out,$row);
			
		}
		
		// filtered records start
		
		if($searchValue != ''){
			
			$match = ['$match' => ['$or'=>[
						["id"=>new MongoDB\BSON\Regex($searchValue,'i')],
						["shipperpo"=>new MongoDB\BSON\Regex($searchValue,'i')],
						['shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['pronum'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['reference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['item.item_name'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['flocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlcoation.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['quantity'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['reportdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['time'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['user'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['processdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepprocessdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepumi'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['uploadedetochep'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['reasonforhold'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['transactionid'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['cdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['flcoationcode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlocationcode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['date'=>new MongoDB\BSON\Regex($searchValue,'i')],
					 ]]];
			
			$fRecords = $this->mongo_db->aggregate("tbl_touts",[
				$match,
				['$match' => ['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active"]],
				/*['$match' => ['$or' => 
							  	[['tlcoation.status'=>"Active"],
							 	['tlcoation.locname'=>""]]
							 ]],*/
				['$count' => 'totalFiltered']
			]);
			
			$filteredRecords = $fRecords[0]['totalFiltered'];
		
		}else{
			
			$filteredRecords = $totalRecords;
			
		}
// filtered records end
		
		## Response
		$response = array(
		  "draw" => intval($draw),
		  "iTotalRecords" => $totalRecords,
		  "iTotalDisplayRecords" => $filteredRecords,
		  "aaData" => $out
		);

		echo json_encode($response);
		
		
	}

	public function getIssues(){
		
		$this->mongo_db->switch_db($this->database);
	
		$draw = $this->input->post('draw');
		$srow = $this->input->post('start');
		$rowperpage = $this->input->post('length'); // Rows display per page
		$columnIndex = $this->input->post('order')[0]['column']; // Column index
		$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
		$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
		$searchValue = $this->input->post('search')['value']; // Search value
		
		if($columnName == "id"){
			
			$columnName = "_id";
			
		}else{
			
			$columnName = $columnName;
			
		}
		
		if($columnSortOrder == "asc"){
			
			$sortOrder = -1;
			
		}else{
			
			$sortOrder = 1;
			
		}
		
		$totalRecords = $this->admin->getTotalrecords("tbl_issues","item","tlocation","");
		
		if($searchValue != ''){
		
			$match = ['$match' => ['$or'=>[
						['id'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['ongreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['quantity'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['item.item_name'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlcoationcode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepprocessdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['cdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['umi'=>new MongoDB\BSON\Regex($searchValue,'i')],
					 ]]];
			
		}else{
			
			$match = ['$sort'=>["$columnName"=>$sortOrder]];
			
		}
		
		$empRecords = $this->mongo_db->aggregate("tbl_issues",[
//				['$sort'=>["_id"=>-1]],
//				['$sort'=>["$columnName"=>$sortOrder]],			
				$match,
				['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
				['$skip'=>intval($srow)],
				['$limit'=>intval($rowperpage)],
//				$sort
			]);
		
		$out = [];
		
		foreach($empRecords as $key=>$row){
			
			$shipmentdate = "";
			$chepprocessdate = "";
			
			$shipmentdate=date("m-d-Y",strtotime($row["shippmentdate"]));
			$chepprocessdate=date("m-d-Y",strtotime($row["chepprocessdate"]));			
			
			$eshipmentdate=$row["shippmentdate"];
			$echepprocessdate=$row["chepprocessdate"];

			$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]->{'$id'}.'">';
			$row["id"] = $row["id"];
			$row["Actions"] = '<a href="javascript:void(0)" class="editLocate" id="'.$row["_id"]->{'$id'}.'" chepreference="'.$row["chepreference"].'" ongreference="'.$row["ongreference"].'" shippmentdate="'.$eshipmentdate.'" quantity="'.$row["quantity"].'" item="'.$row["item"]->item_name.'" tlcoation="'.$row["tlocation"]->locname.'" tlcoationcode="'.$row["tlcoationcode"].'" chepprocessdate="'.$echepprocessdate.'" umi="'.$row["umi"].'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]->{'$id'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';

			$row["shippmentdate"] = ($row["shippmentdate"] != "") ? date("m-d-Y",strtotime($row["shippmentdate"])) : "";
			$row["chepprocessdate"] = ($row["chepprocessdate"] != "") ? date("m-d-Y",strtotime($row["chepprocessdate"])) : "";
			
			$row["item"] = $row["item"]->item_name;
			$row["tlocation"] = $row["tlocation"]->locname;
			

			array_push($out,$row);
				
		}
		
	// filtered records start
		
		if($searchValue != ''){
			
			$filteredRecords = 0;
			
			$match = ['$match' => ['$or'=>[
						['id'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['ongreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['quantity'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['item.item_name'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlcoationcode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepprocessdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['cdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['umi'=>new MongoDB\BSON\Regex($searchValue,'i')],
					 ]]];
		
		     $fRecords = $this->mongo_db->aggregate("tbl_issues",[
				$match,
				['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
				['$count' => 'totalFiltered']
			]);
			
			$filteredRecords = $fRecords[0]['totalFiltered'];
		
		}else{
			
			$filteredRecords = $totalRecords;
			
		}
// filtered records end
		
		## Response
		$response = array(
		  "draw" => intval($draw),
		  "iTotalRecords" => $totalRecords,
		  "iTotalDisplayRecords" => $filteredRecords,
		  "aaData" => $out
		);

		echo json_encode($response);
		
	}

	public function getReturns(){
	
		$this->mongo_db->switch_db($this->database);
	
		$draw = $this->input->post('draw');
		$srow = $this->input->post('start');
		$rowperpage = $this->input->post('length'); // Rows display per page
		$columnIndex = $this->input->post('order')[0]['column']; // Column index
		$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
		$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
		$searchValue = $this->input->post('search')['value']; // Search value
		
		if($columnName == "id"){
			
			$columnName = "_id";
			
		}else{
			
			$columnName = $columnName;
			
		}
		
		if($columnSortOrder == "asc"){
			
			$sortOrder = -1;
			
		}else{
			
			$sortOrder = 1;
			
		}
		
//		$totalRecords = $this->mongo_db->count("tbl_returns");
		$totalRecords = $this->admin->getTotalrecords("tbl_returns","item","tlocation","");

		if($searchValue != ''){
		
			$match = ['$match' => ['$or'=>[
						['id'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['ongreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['quantity'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['item.item_name'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlcoationcode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepprocessdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['cdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['umi'=>new MongoDB\BSON\Regex($searchValue,'i')],
					 ]]];
			
		}else{
			
			$match = ['$sort'=>["$columnName"=>$sortOrder]];
			
		}
		
		
		$empRecords = $this->mongo_db->aggregate("tbl_returns",[
//				['$sort'=>["_id"=>-1]],
//				['$sort'=>["$columnName"=>$sortOrder]],			
				$match,
				['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
				['$skip'=>intval($srow)],
				['$limit'=>intval($rowperpage)],
//				$sort
			]);
			
		
		$out = [];
		
		foreach($empRecords as $key=>$row){
			
			$shipmentdate = "";
			$chepprocessdate = "";
				
			$shipmentdate=date("m-d-Y",strtotime($row["shippmentdate"]));				
			$chepprocessdate=date("m-d-Y",strtotime($row["chepprocessdate"]));

			$eshipmentdate=$row["shippmentdate"];				
			$echepprocessdate=$row["chepprocessdate"];
			
			$row["id"] = $row["id"];
			$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]->{'$id'}.'">';
			$row["Actions"] = '<a href="javascript:void(0)" class="editLocate" id="'.$row["_id"]->{'$id'}.'" chepreference="'.$row["chepreference"].'" ongreference="'.$row["ongreference"].'" shippmentdate="'.$eshipmentdate.'" quantity="'.$row["quantity"].'" item="'.$row["item"]->item_name.'" tlocation1="'.$row["tlocation"]->locname.'" tlcoationcode="'.$row["tlcoationcode"].'" chepprocessdate="'.$echepprocessdate.'" umi="'.$row["umi"].'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]->{'$id'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';

			$row["shippmentdate"] = ($row["shippmentdate"] != "") ? date("m-d-Y",strtotime($row["shippmentdate"])) : "";
			$row["chepprocessdate"] = ($row["chepprocessdate"] != "") ? date("m-d-Y",strtotime($row["chepprocessdate"])) : "";

			$row["item"] = $row["item"]->item_name;
			$row["tlocation"] = $row["tlocation"]->locname;
			
			array_push($out,$row);
				
		}
		
			// filtered records start
		
		if($searchValue != ''){
			
			$match = ['$match' => ['$or'=>[
						['id'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['ongreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['quantity'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['item.item_name'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlcoationcode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepprocessdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['cdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['umi'=>new MongoDB\BSON\Regex($searchValue,'i')],
					 ]]];
		
		     $fRecords = $this->mongo_db->aggregate("tbl_returns",[
				$match,
				['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
				['$count' => 'totalFiltered']
			]);
			
			$filteredRecords = $fRecords[0]['totalFiltered'];
		
		}else{
			
			$filteredRecords = $totalRecords;
			
		}
// filtered records end

		
		## Response
		$response = array(
		  "draw" => intval($draw),
		  "iTotalRecords" => $totalRecords,
		  "iTotalDisplayRecords" => $filteredRecords,
		  "aaData" => $out
		);

		echo json_encode($response);
		
	}

	public function getAdjustments(){
		
		$this->mongo_db->switch_db($this->database);
	
		$draw = $this->input->post('draw');
		$srow = $this->input->post('start');
		$rowperpage = $this->input->post('length'); // Rows display per page
		$columnIndex = $this->input->post('order')[0]['column']; // Column index
		$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
		$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
		$searchValue = $this->input->post('search')['value']; // Search value
		
		if($columnName == "id"){
			
			$columnName = "_id";
			
		}else{
			
			$columnName = $columnName;
			
		}
		

		if($columnSortOrder == "asc"){
			
			$sortOrder = -1;
			
		}else{
			
			$sortOrder = 1;
			
		}
		
//		$totalRecords = $this->mongo_db->count("tbl_adjustments");
		$totalRecords = $this->admin->getTotalrecords("tbl_adjustments","item","tlocation","");

		if($searchValue != ''){
		
			$match = ['$match' => ['$or'=>[
						['id'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['ongreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['quantity'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['item.item_name'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlcoationcode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepprocessdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['cdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['umi'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['adjdirection'=>new MongoDB\BSON\Regex($searchValue,'i')],				
					 ]]];
			
		}else{
			
			$match = ['$sort'=>["$columnName"=>$sortOrder]];
			
		}
		
		$empRecords = $this->mongo_db->aggregate("tbl_adjustments",[
//				['$sort'=>["_id"=>-1]],
//				['$sort'=>["$columnName"=>$sortOrder]],			
				$match,
				['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
				['$skip'=>intval($srow)],
				['$limit'=>intval($rowperpage)],
//				$sort
			]);
		
		
		$out = [];
		
		foreach($empRecords as $key=>$row){
			
			$shipmentdate=date("m-d-Y",strtotime($row["shippmentdate"]));				
			$chepprocessdate=date("m-d-Y",strtotime($row["chepprocessdate"]));

			$eshipmentdate=$row["shippmentdate"];				
			$echepprocessdate=$row["chepprocessdate"];
			
			$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]->{'$id'}.'">';
			$row["id"] = $row["id"];
			$row["Actions"] = '<a href="javascript:void(0)" class="editLocate" lid="'.$row["_id"]->{'$id'}.'" chepreference="'.$row["chepreference"].'" ongreference="'.$row["ongreference"].'" shippmentdate="'.$eshipmentdate.'" quantity="'.$row["quantity"].'" item="'.$row["item"]->item_name.'" tlocation="'.$row["tlocation"]->locname.'" chepprocessdate="'.$echepprocessdate.'" adjdirection="'.strtoupper($row["adjdirection"]).'" umi="'.$row["umi"].'" tlcoationcode="'.$row["tlcoationcode"].'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]->{'$id'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';

			$row["shippmentdate"] = ($row["shippmentdate"] != "") ? date("m-d-Y",strtotime($row["shippmentdate"])) : "";
			$row["chepprocessdate"] = ($row["chepprocessdate"] != "") ? date("m-d-Y",strtotime($row["chepprocessdate"])) : "";

			$row["item"] = $row["item"]->item_name;
			$row["tlocation"] = $row["tlocation"]->locname;
			
			array_push($out,$row);
			
		}
	// filtered records start
		
		if($searchValue != ''){
			
			$filteredRecords = 0;
			
			$match = ['$match' => ['$or'=>[
						['id'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['ongreference'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['shippmentdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['quantity'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['item.item_name'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlocation.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['tlcoationcode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['chepprocessdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['cdate'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['umi'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['adjdirection'=>new MongoDB\BSON\Regex($searchValue,'i')],				
					 ]]];
		
		     $fRecords = $this->mongo_db->aggregate("tbl_adjustments",[
				$match,
				['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
				['$count' => 'totalFiltered']
			]);
			
			$filteredRecords = $fRecords[0]['totalFiltered'];
			
		}else{
			
			$filteredRecords = $totalRecords;
			
		}
// filtered records end		
		
		## Response
		$response = array(
		  "draw" => intval($draw),
		  "iTotalRecords" => $totalRecords,
		  "iTotalDisplayRecords" => $filteredRecords,
		  "aaData" => $out
		);

		echo json_encode($response);
		
	}

	public function getInventory(){
		
		$this->mongo_db->switch_db($this->database);

		$item = $this->input->post('item');		
		$draw = $this->input->post('draw');
		$srow = $this->input->post('start');
		$rowperpage = $this->input->post('length'); // Rows display per page
		$columnIndex = $this->input->post('order')[0]['column']; // Column index
		$columnName = $this->input->post('columns')[$columnIndex]['data']; // Column name
		$columnSortOrder = $this->input->post('order')[0]['dir']; // asc or desc
		$searchValue = $this->input->post('search')['value']; // Search value
		
		if($columnName == "id"){
			
			$columnName = "_id";
			
		}else{
			
			$columnName = $columnName;
			
		}
		
		if($columnSortOrder == "asc"){
			
			$sortOrder = -1;
			
		}else{
			
			$sortOrder = 1;
			
		}
		
		$totalRecords = $this->admin->getTotalrecords("tbl_inventory","$item","locname","");

		if($searchValue != ''){
		
			$match = ['$match' => ['$or'=>[
						['id'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['location'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['locname.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['loccode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['loctype'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['item.item_name'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['last_report_date'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['issues'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['returns'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['transfer_ins'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['transfer_outs'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['adjustments'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['ending_balance'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['audit_count2019'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['audit_date2019'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['cdate'=>new MongoDB\BSON\Regex($searchValue,'i')],				
					 ]]];
			
		}else{
			
			$match = ['$sort'=>["$columnName"=>$sortOrder]];
			
		}
		
		$empRecords = $this->mongo_db->aggregate("tbl_inventory",[
				['$match' => ["item.item_name"=>$item]],
//				['$sort'=>["_id"=>-1]],
//				['$sort'=>["$columnName"=>$sortOrder]],			
				$match,
				['$match' => ['item.status'=>"Active","locname.status"=>"Active"]],
				['$skip'=>intval($srow)],
				['$limit'=>intval($rowperpage)],
//				$sort
			]);
		
		$out = [];
		
		foreach($empRecords as $key=>$row){

			$last_report_date = "";
			$audit_date2019 = "";
				
			$last_report_date=date("m-d-Y",strtotime($row["last_report_date"]));			
			$audit_date2019=date("m-d-Y",strtotime($row["audit_date2019"]));

			$elast_report_date=$row["last_report_date"];			
			$eaudit_date2019=$row["audit_date2019"];

			$row["id"] = $row["id"];
			$row["check"] = '<input type="checkbox" class="check" name="lid" value="'.$row["_id"]->{'$id'}.'">';
			/*$row['issues'] = ($this->common->getInventorycount($this->database,"tbl_issues",$row['appId'],$row['loccode'],"tlcoationcode",$row['item']->item_name));
			$row['returns'] = ($this->common->getInventorycount($this->database,"tbl_returns",$row['appId'],$row['loccode'],"tlcoationcode",$row['item']->item_name));
			$row['transfer_ins'] = ($this->common->getInventorycount($this->database,"tbl_touts",$row['appId'],$row['loccode'],"tlocationcode",$row['item']->item_name));
			$row['transfer_outs'] = ($this->common->getInventorycount($this->database,"tbl_touts",$row['appId'],$row['loccode'],"flcoationcode",$row['item']->item_name));
			$row['adjustments'] = ($this->common->getInventorycount($this->database,"tbl_adjustments",$row['appId'],$row['loccode'],"tlcoationcode",$row['item']->item_name));
			$row['ending_balance'] = ($row["starting_balance"]+$row["issues"]+$row["returns"]+$row["transfer_ins"]-$row["transfer_outs"]+$row["adjustments"]);*/
			
			
//			$row['issues'] = $row['issues'];
//			$row['returns'] = $row['returns'];
//			$row['transfer_ins'] = $row['transfer_ins'];
//			$row['transfer_outs'] = $row['transfer_outs'];
//			$row['adjustments'] = $row['adjustments'];
//			$row['ending_balance'] = ($row["ending_balance"]+$row["issues"]+$row["returns"]+$row["transfer_ins"]-$row["transfer_outs"]+$row["adjustments"]);
			
			$row["Actions"] = '<a href="javascript:void(0)" class="editInventory" lid="'.$row["_id"]->{'$id'}.'" location="'.$row["location"].'" locname="'.$row["locname"]->locname.'" loccode="'.$row["loccode"].'" loctype="'.$row["loctype"].'" notes="'.$row["notes"].'" last_report_date="'.$elast_report_date.'" starting_balance="'.$row["starting_balance"].'" issues="'.$row["issues"].'" returns="'.$row["returns"].'" transfer_ins="'.$row["transfer_ins"].'" transfer_outs="'.$row["transfer_outs"].'" adjustments="'.$row["adjustments"].'" ending_balance="'.$row["ending_balance"].'" audit_count2019="'.$row["audit_count2019"].'" audit_date2019="'.$eaudit_date2019.'" item="'.$row["item"]->item_name.'"><i class="far fa-edit"></i></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="'.$row["_id"]->{'$id'}.'" onclick="archiveFunction(this.id)" class="delItem"><i class="fa fa-trash" style="color:red"></i></a>';

			$row["last_report_date"] = ($row["last_report_date"] != "") ? $last_report_date : "";
			$row["audit_date2019"] = ($row["audit_date2019"] != "") ? $audit_date2019 : "";
			$row["item"] = $row["item"]->item_name;
			$row["locname"] = $row["locname"]->locname;

			array_push($out,$row);
			
		}
		
// filtered records start
		
		if($searchValue != ''){
			
			$filteredRecords = 0;
			
			$match = ['$match' => ['$or'=>[
						['id'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['location'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['locname.locname'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['loccode'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['loctype'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['item.item_name'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['last_report_date'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['issues'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['returns'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['transfer_ins'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['transfer_outs'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['adjustments'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['ending_balance'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['audit_count2019'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['audit_date2019'=>new MongoDB\BSON\Regex($searchValue,'i')],
						['cdate'=>new MongoDB\BSON\Regex($searchValue,'i')],				
					 ]]];
		
		     $fRecords = $this->mongo_db->aggregate("tbl_inventory",[
				['$match' => ["item.item_name"=>$item]], 
				$match,
				['$match' => ['item.status'=>"Active","locname.status"=>"Active"]],
				['$count' => 'totalFiltered']
			]);
			
			$filteredRecords = $fRecords[0]['totalFiltered'];
			
		}else{
			
			$filteredRecords = $totalRecords;
			
		}
// filtered records end	
		
		## Response
		$response = array(
		  "draw" => intval($draw),
		  "iTotalRecords" => $totalRecords,
		  "iTotalDisplayRecords" => $filteredRecords,
		  "aaData" => $out
		);

		echo json_encode($response);
	}



	public function locationAccess($id){
		$data['l'] = $this->mongo_db->get_where("tbl_apps",array('appId' => $id));
		$data['database'] = $this->database;
		$data["location_requests"] = $this->admin->getRows("",[],['sort'=>['_id'=>-1]],"$this->database.location_requests");
		$data["location_submits"] = $this->admin->getRows("",[],['sort'=>['_id'=>-1]],"$this->database.location_submits");
		$this->load->view('admin/apps/locationAccess',$data);
	}

	public function transfers($id){
		
		$data['l'] = $this->mongo_db->get_where("tbl_apps",array('appId' => $id));
		$data['mdb'] = $this->mdb;
		$data['database'] = $this->database;
		$this->load->view('admin/apps/transfers',$data);
	}

	public function tests(){
		
		$this->mongo_db->switch_db($this->database);
		
//		$tlocdata = $this->admin->getRow("",["loccode"=>"B3784"],['projection' => ['locname' => 1,'loccode' => 1,'status' => 1,'Type' => 1]],"$this->database.tbl_locations");
		
		$data = $this->mongo_db->aggregate("tbl_touts",[
				['$match' => ["appId"=>"OID0020","tlocationcode"=>"B4000","item.item_name"=>"80x40 GWB Pallet 2"]],
				['$group' => ["_id"=>null,"totalQty"=>['$sum'=>'$quantity']]],
			]);
		
		print_r($data[0]['totalQty']);
		
		/*$data['l'] = $this->mongo_db->order_by('shipmentdate','ASC')->limit(10)->get_where("tbl_apps",array('appId' => $id));
		$mng = $this->admin->Mconfig();

		$command = new MongoDB\Driver\Command([
				'aggregate'=>"tbl_adjustments",
				'cursor' => new stdClass,
				'pipeline'=>[
					['$match'=>["cdate"=>[
						'$gte'=>date("m-d-Y", strtotime("2020-02-01")),
						'$lte'=>date("m-d-Y", strtotime("2020-02-20"))
						],"appId"=>"OID001" ]]
				]
			]);
		$cursor = $mng->executeCommand("ongpool_OID001", $command);
		echo "<pre>";
		foreach($cursor as $row){
			print_r($row);
		}
		exit;*/
		// $this->load->view('admin/apps/test',$data);
	}

	public function issues($id){
		$data['l'] = $this->mongo_db->get_where("tbl_apps",array('appId' => $id));
		$data['mdb'] = $this->mdb;
		$data['database'] = $this->database;
		$this->load->view('admin/apps/issues',$data);
	}

	public function returns($id){
		$data['l'] = $this->mongo_db->get_where("tbl_apps",array('appId' => $id));
		$data['mdb'] = $this->mdb;
		$data['database'] = $this->database;
		$this->load->view('admin/apps/returns',$data);
	}

	public function adjustments($id){
		$data['l'] = $this->mongo_db->get_where("tbl_apps",array('appId' => $id));
		$data['mdb'] = $this->mdb;
		$data['database'] = $this->database;
		$this->load->view('admin/apps/adjustments',$data);
	}

	public function locationInventory($id){
		
		$data['l'] = $this->mongo_db->get_where("tbl_apps",array('appId' => $id));
		$data['mdb'] = $this->mdb;
		$data['database'] = $this->database;
		
		$row = $this->admin->getRow("",array("table"=>"tbl_inventory"),[],"$this->database.settings");
		
		$rr = $row->columns;
		
		$arr = [];
		foreach($rr as $r){
			
			$arr[$r] = "";
			
		}
		
		$rows = $arr;
		
//		$narr = ["Actions"=>""];		
		
		$final = array_merge($narr,$rows);
		
		$data["columns"] = $rows;
		$data['locations'] = $this->admin->getArray("",["status"=>'Active'],["sort"=>["nameid"=>1]],"$this->database.tbl_locations");
		$data['items'] = $this->admin->getArray("",["status"=>'Active'],[],"$this->database.tbl_items");
	
		$this->load->view('admin/apps/locationInventory',$data);
	}
	
	
	public function items($id){
		$data['l'] = $this->mongo_db->get_where("tbl_apps",array('appId' => $id));
		$data['mdb'] = $this->mdb;
		$data['database'] = $this->database;
		$this->load->view('admin/apps/items',$data);
	}
	


	public function utransfer(){
		
		$id = new MongoDB\BSON\ObjectID($this->input->post("id"));
		
		$data = [];
		
		foreach($this->input->post() as $key=>$value){
//			if($key == "reportdate" || $key == "time" || $key == "shippmentdate" || $key == "processdate" || $key == "chepprocessdate"){
//				
//				
//			}else{
				
				$data[$key] = $value;
				
//			}
		}
		
		$mng = $this->admin->Mconfig();
		$data['flcoationcode'] = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$data["flocation"]],[],"loccode");
		$data['tlocationcode'] = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$data["tlcoation"]],[],"loccode");

		$data["quantity"] = intval($this->input->post("quantity"));
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_touts",$data,$_SESSION['appid'],"");
		
		if(count($conRulescheck) > 0){
			
			foreach($conRulescheck as $con){
			
				$data[$con['column']] = $con['value'];
				
			}
				
		}
		
		/*$valRulescheck = $this->common->checkValidationrules("tbl_touts",$data,$_SESSION['appid'],"");
		
		if($valRulescheck){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>$valRulescheck));
			exit();
			
		}*/
		
		$this->mongo_db->switch_db($this->database);
		
		$flocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$data["flocation"]])[0];
		$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$data["tlcoation"]])[0];
		$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$data["item"]])[0];
		
		$data["flocation"] = ["id"=>$flocdata["_id"]->{'$id'},"locname"=>$flocdata["locname"],"loccode"=>$flocdata["loccode"],"status"=>$flocdata["status"]];
		$data['flcoationcode'] = $flocdata["loccode"];
		
		$data["tlcoation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
		$data['tlocationcode'] = $tlocdata["loccode"];
		
		$data["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];


		foreach($data as $pk => $pd){
			if($pk != "quantity" && $pk != "flocation" && $pk != "tlcoation" && $pk != "item"){
				$data[$pk] = trim($pd);	
			}	
		}

		$count = $this->mongo_db->where(["shipperpo"=>$data['shipperpo']])->get("tbl_touts")[0];
		if($count){
			
			if($count["_id"]->{'$id'} != $id){
				echo json_encode(array("Status"=>"Wrong","Message"=>"Shipper PO already existed"));
				exit();
			}
		}
		
		if(trim($data['chepumi']) != ""){
		
			$count1 = $this->mongo_db->where(["chepumi"=>$data['chepumi']])->get("tbl_touts")[0];
			if($count1){

				if($count1["_id"]->{'$id'} != $id){
					echo json_encode(array("Status"=>"Wrong","Message"=>"UMI already existed"));
					exit();
				}
			}
			
		}
		
		$exdata = $this->mongo_db->get_where("tbl_touts",["_id"=>$id])[0];		
		
		unset($data["id"]);
		$d = $this->admin->mongoUpdate("$this->database.tbl_touts",array('_id'=>$id),$data,[]);
		
// update location inventory		
				
		$tins = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$tlocdata["loccode"],"tlocationcode",$itemdata["item_name"],$data["quantity"],"transfer_ins",$exdata);
		
		$touts = $this->common->updateLocationinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$flocdata["loccode"],"flcoationcode",$itemdata["item_name"],$data["quantity"],"transfer_outs",$exdata);
		
//		print_r($tins);
//		print_r($touts);
//		echo $tins." ".$touts;
//		exit();
// end update location inventory		
		
//		$d = $this->admin->getArray("",array('_id'=>$id),[],"$this->database.tbl_touts");
		
		if($d){
			echo json_encode(array("Status"=>"Success","Message"=>"Transfer Successfully Updated"));
		}else{
			echo json_encode(array("Status"=>"Wrong","Message"=>"Something went wrong"));
		}
	}

	public function ushipment(){
		$id = new MongoDB\BSON\ObjectID($this->input->post('id'));
		$data = [];
		foreach($this->input->post() as $key=>$value){
			if($key != 'id'){ $data[$key] = $value; }
		}
		
		/*$valRulescheck = $this->common->checkValidationrules("tbl_issues",$this->input->post(),$_SESSION['appid'],"");
		
		if($valRulescheck){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>$valRulescheck));
			exit();
			
		}*/
		
		$data["quantity"] = intval($this->input->post('quantity'));
		$data['tlcoationcode'] = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$data["tlocation"]],[],"loccode");
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_issues",$data,$_SESSION['appid'],"");
		
		if(count($conRulescheck) > 0){
			
			foreach($conRulescheck as $con){
			
				$data[$con['column']] = $con['value'];
				
			}
				
		}

		foreach($data as $pk => $pd){
			if($pk != "quantity"){
				$data[$pk] = trim($pd);	
			}	
		}
		
		$this->mongo_db->switch_db($this->database);
		
		$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$data["tlocation"]])[0];
		$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$data["item"]])[0];
		$exdata = $this->mongo_db->get_where("tbl_issues",["_id"=>$id])[0];
		
		if(trim($data['umi']) != ""){
		
			$count1 = $this->mongo_db->where(["umi"=>$data['umi']])->get("tbl_issues")[0];
			if($count1){

				if($count1["_id"]->{'$id'} != $id){
					echo json_encode(array("Status"=>"Wrong","Message"=>"UMI already existed"));
					exit();
				}
			}
			
		}
		
		$data["tlocation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
		$data['tlcoationcode'] = $tlocdata["loccode"];
		
		$data["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];

		
		$d = $this->admin->mongoUpdate("$this->database.tbl_issues",array('_id'=>$id),$data,[]);

        // update location inventory		
				
		$tins = $this->common->updateLocationinventorycount($this->database,"tbl_issues",$_SESSION['appid'],$tlocdata["loccode"],"tlcoationcode",$itemdata["item_name"],$data["quantity"],"issues",$exdata);
		
        // end update location inventory

		if($d){
			echo json_encode(array("Status"=>"Success","Message"=>"Successfully Updated"));
		}else{
			echo json_encode(array("Status"=>"Wrong","Message"=>"Something went wrong"));
		}
	}
	
	public function uadjustment(){
		
		$id = new MongoDB\BSON\ObjectID($this->input->post('id'));
		$data = [];
		foreach($this->input->post() as $key=>$value){
			if($key != 'id'){ $data[$key] = $value; }
		}
		
		/*$valRulescheck = $this->common->checkValidationrules("tbl_adjustments",$this->input->post(),$_SESSION['appid'],"");
		
		if($valRulescheck){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>$valRulescheck));
			exit();
			
		}*/
		
		$data["quantity"] = intval($this->input->post("quantity"));
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_adjustments",$data,$_SESSION['appid'],"");
		
		if(count($conRulescheck) > 0){
			
			foreach($conRulescheck as $con){
			
				$data[$con['column']] = $con['value'];
				
			}
				
		}

		foreach($data as $pk => $pd){
			if($pk != "quantity"){
				$data[$pk] = trim($pd);	
			}	
		}
		
		$this->mongo_db->switch_db($this->database);
		
		$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$data["tlocation"]])[0];
		$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$data["item"]])[0];
		$exdata = $this->mongo_db->get_where("tbl_adjustments",["_id"=>$id])[0];
		
		if(trim($data['umi']) != ""){		
		
			$count1 = $this->mongo_db->where(["umi"=>$data['umi']])->get("tbl_adjustments")[0];
			if($count1){

				if($count1["_id"]->{'$id'} != $id){
					echo json_encode(array("Status"=>"Wrong","Message"=>"UMI already existed"));
					exit();
				}
			}
			
		}
		
		$data["tlocation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
		$data['tlcoationcode'] = $tlocdata["loccode"];
		
		$data["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];

		
		$d = $this->admin->mongoUpdate("$this->database.tbl_adjustments",array('_id'=>$id),$data,[]);

        // update location inventory		
				
		$tins = $this->common->updateLocationinventorycount($this->database,"tbl_adjustments",$_SESSION['appid'],$tlocdata["loccode"],"tlcoationcode",$itemdata["item_name"],$data["quantity"],"adjustments",$exdata);
		
        // end update location inventory

		if($d){
			echo json_encode(array("Status"=>"Success","Message"=>"Successfully Updated"));
		}else{
			echo json_encode(array("Status"=>"Wrong","Message"=>"Something went wrong"));
		}
	}	

	public function ureturns(){
		
		$id = new MongoDB\BSON\ObjectID($this->input->post('id'));
		$data = [];
		foreach($this->input->post() as $key=>$value){
			if($key != 'id'){ $data[$key] = $value; }
		}
		
		/*$valRulescheck = $this->common->checkValidationrules("tbl_returns",$this->input->post(),$_SESSION['appid'],"");
		
		if($valRulescheck){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>$valRulescheck));
			exit();
			
		}*/
		
		$data["quantity"] = intval($this->input->post("quantity"));
		
		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_returns",$data,$_SESSION['appid'],"");
		
		if(count($conRulescheck) > 0){
			
			foreach($conRulescheck as $con){
			
				$data[$con['column']] = $con['value'];
				
			}
				
		}

		foreach($data as $pk => $pd){
			if($pk != "quantity"){
				$data[$pk] = trim($pd);	
			}
		}
		
		$this->mongo_db->switch_db($this->database);
		
		$tlocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$data["tlocation"]])[0];
		$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$data["item"]])[0];
		$exdata = $this->mongo_db->get_where("tbl_returns",["_id"=>$id])[0];
		
		
		if(trim($data['umi']) != ""){

			$count1 = $this->mongo_db->where(["umi"=>$data['umi']])->get("tbl_returns")[0];
			if($count1){

				if($count1["_id"]->{'$id'} != $id){
					echo json_encode(array("Status"=>"Wrong","Message"=>"UMI already existed"));
					exit();
				}
			}
			
		}
		
		$data["tlocation"] = ["id"=>$tlocdata["_id"]->{'$id'},"locname"=>$tlocdata["locname"],"loccode"=>$tlocdata["loccode"],"status"=>$tlocdata["status"]];
		$data['tlcoationcode'] = $tlocdata["loccode"];
		
		$data["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];
		
		$d = $this->mongo_db->where(array('_id'=>$id))->set($data)->update('tbl_returns');
		// update location inventory		
			
		$tins = $this->common->updateLocationinventorycount($this->database,"tbl_returns",$_SESSION['appid'],$tlocdata["loccode"],"tlcoationcode",$itemdata["item_name"],$data["quantity"],"returns",$exdata);
//		echo $tins;
//		exit();
		
        // end update location inventory
		if($d){
			echo json_encode(array("Status"=>"Success","Message"=>"Successfully Updated"));
		}else{
			echo json_encode(array("Status"=>"Wrong","Message"=>"Something went wrong"));
		}
	}
	
	
	
	public function uInventory(){
		$id = new MongoDB\BSON\ObjectID($this->input->post("id"));
		$data = [];
		foreach($this->input->post() as $key=>$value){
			if($key != 'id'){ $data[$key] = $value; }
		}
		$data['ending_balance'] = ($data['starting_balance']+$data['issues']+$data['returns']+$data['transfer_ins']-$data['transfer_outs']+$data['adjustments']);
		// echo "<pre>";
		// print_r($data);
		// exit;

		/*$valRulescheck = $this->common->checkValidationrules("tbl_inventory",$this->input->post(),$_SESSION['appid'],"");
		
		if($valRulescheck){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>$valRulescheck));
			exit();
			
		}*/

		foreach($data as $pk => $pd){
			
			$data[$pk] = trim($pd);	
				
		}
		
		$data['starting_balance'] = intval($data['starting_balance']);
		/*$data['issues'] = intval($data['issues']);
		$data['returns'] = intval($data['returns']);
		$data['transfer_ins'] = intval($data['transfer_ins']);
		$data['transfer_outs'] = intval($data['transfer_outs']);
		$data['adjustments'] = intval($data['adjustments']);
		$data['ending_balance'] = intval($data['ending_balance']);*/
		$data['audit_count2019'] = ($data['audit_count2019'] != "") ? intval($data['audit_count2019']) : intval();
		
//		$locid = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$data['locname']],[],"_id");
//		$data['locname'] = (string) $locid;

		$conRulescheck = $this->conditions_model->checkConditionrules("tbl_inventory",$data,$_SESSION['appid'],"");

		if(count($conRulescheck) > 0){

			foreach($conRulescheck as $con){

				$data[$con['column']] = $con['value'];

			}

		}

		$this->mongo_db->switch_db($this->database);		

		$flocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$data["locname"]])[0];
		$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$data["item"]])[0];

		$data["locname"] = ["id"=>$flocdata["_id"]->{'$id'},"locname"=>$flocdata["locname"],"loccode"=>$flocdata["loccode"],"status"=>$flocdata["status"]];
		$data["location"] = $flocdata['locname']." - ".$flocdata['loccode'];
		$data["loccode"] = $flocdata["loccode"];
		$data["loctype"] = $flocdata["Type"];
//		$data["notes"] = $flocdata["notes"];
		
		$data["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];

		$invdata = $this->mongo_db->get_where("tbl_inventory",["_id"=>$id])[0];	
		
		$loccode = $data["locname"]["loccode"];
		$item = $data["item"]["item_name"];
		
		if($invdata["locname"]->locname != $data["locname"]["locname"]){
			
			$ltdata = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$data["locname"]["loccode"],"item.item_name"=>$item])[0];	
			
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
							
			$d = $this->admin->mongoUpdate("$this->database.tbl_inventory",array('_id'=>new MongoDB\BSON\ObjectID($ltdata['_id']->{'$id'})),$data,[]);

			$this->mongo_db->where(["_id"=>$id])->delete("tbl_inventory");
			
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

			$d = $this->admin->mongoUpdate("$this->database.tbl_inventory",array('_id'=>$id),$data,[]);
			
		}
		
//		$d = $this->admin->getArray("",array('_id'=>$id),[],"$this->database.tbl_touts");
		
		if($d){
			echo json_encode(array("Status"=>"Success","Message"=>"Successfully Updated"));
		}else{
			echo json_encode(array("Status"=>"Wrong","Message"=>"Something went wrong"));
		}
	}

	public function addInventory(){
		
		$fields = $this->input->post();
		if($fields['starting_balance'] == ""){ $fields['starting_balance']= 0; }
		if($fields['issues'] == ""){ $fields['issues']= 0; }
		if($fields['returns'] == ""){ $fields['returns']= 0; }
		if($fields['transfer_ins'] == ""){ $fields['transfer_ins']= 0; }
		if($fields['transfer_outs'] == ""){ $fields['transfer_outs']= 0; }
		if($fields['adjustments'] == ""){ $fields['adjustments']= 0; }
				
		$fields['ending_balance'] = ($fields['starting_balance']+$fields['issues']+$fields['returns']+$fields['transfer_ins']-$fields['transfer_outs']+$fields['adjustments']);
		
		/*$valRulescheck = $this->common->checkValidationrules("tbl_inventory",$this->input->post(),$_SESSION['appid'],"");
		
		if($valRulescheck){
			
			echo json_encode(array("Status"=>"Wrong","Message"=>$valRulescheck));
			exit();
			
		}*/

		
		
		$this->mongo_db->switch_db($this->database);
		$loccode = $this->input->post("loccode");
		$item = $this->input->post("item");

		
		$ltdata = $this->mongo_db->where(["loccode"=>$loccode,"item.item_name"=>$item])->get("tbl_inventory");
		
//		echo json_encode(array("Status"=>"Wrong","Message"=>$cloc));
//		exit();

		foreach($fields as $pk => $pd){
			
			$fields[$pk] = trim($pd);	
				
		}
		
		if($ltdata){
			
			$conRulescheck = $this->conditions_model->checkConditionrules("tbl_inventory",$fields,$_SESSION['appid'],"");

			if(count($conRulescheck) > 0){

				foreach($conRulescheck as $con){

					$fields[$con['column']] = $con['value'];

				}

			}
			
//			$ltdata = $this->mongo_db->get_where("tbl_inventory",["loccode"=>$loccode,"item.item_name"=>$item])[0];
			
			$sbalance = $ltdata[0]["starting_balance"] + $fields['starting_balance'];
			$issues = ($this->common->getInventorycount($this->database,"tbl_issues",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
			$returns = ($this->common->getInventorycount($this->database,"tbl_returns",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
			$transfer_ins = ($this->common->getInventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"tlocationcode",$item));
			$transfer_outs = ($this->common->getInventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"flcoationcode",$item));
			$adjustments = ($this->common->getInventorycount($this->database,"tbl_adjustments",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
			$ending_balance = ($sbalance+$issues+$returns+$transfer_ins-$transfer_outs+$adjustments);
			
			$fields['starting_balance'] = intval($sbalance);
			$fields['issues'] = intval($issues);
			$fields['returns'] = intval($returns);
			$fields['transfer_ins'] = intval($transfer_ins);
			$fields['transfer_outs'] = intval($transfer_outs);
			$fields['adjustments'] = intval($adjustments);
			$fields['ending_balance'] = intval($ending_balance);	
			$fields['audit_count2019'] = $ltdata[0]["audit_count2019"] + $this->input->post("audit_count2019");
//			$locid = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$fields['locname']],[],"_id");
//			$fields['locname'] = (string) $locid;
			
			$this->mongo_db->switch_db($this->database);

			$flocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$fields["locname"]])[0];
			$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$fields["item"]])[0];

			$fields["locname"] = ["id"=>$flocdata["_id"]->{'$id'},"locname"=>$flocdata["locname"],"loccode"=>$flocdata["loccode"],"status"=>$flocdata["status"]];

			$fields["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];
						
			$d = $this->admin->mongoUpdate("$this->database.tbl_inventory",["loccode"=>$loccode,"item.item_name"=>$itemdata["item_name"]],$fields,[]);
			
		}else{
			
			$conRulescheck = $this->conditions_model->checkConditionrules("tbl_inventory",$fields,$_SESSION['appid'],"");

			if(count($conRulescheck) > 0){

				foreach($conRulescheck as $con){

					$fields[$con['column']] = $con['value'];

				}

			}
			
//			$fields['starting_balance'] = 0;
			$fields['issues'] = 0;
			$fields['returns'] = 0;
			$fields['transfer_ins'] = 0;
			$fields['transfer_outs'] = 0;
			$fields['adjustments'] = 0;
			$fields['ending_balance'] = 0;
			
			
			$fields["id"] = $this->admin->insert_id("tbl_inventory",$this->database);
			
//			$locid = $this->admin->getReturn($mng,"$this->database.tbl_locations",["locname"=>$fields['locname']],[],"_id");
//			$fields['locname'] = (string) $locid;
			
			$this->mongo_db->switch_db($this->database);

			$flocdata = $this->mongo_db->get_where("tbl_locations",["locname"=>$fields["locname"]])[0];
			$itemdata = $this->mongo_db->get_where("tbl_items",["item_name"=>$fields["item"]])[0];

			$fields["locname"] = ["id"=>$flocdata["_id"]->{'$id'},"locname"=>$flocdata["locname"],"loccode"=>$flocdata["loccode"],"status"=>$flocdata["status"]];

			$fields["item"] = ["id"=>$itemdata["_id"]->{'$id'},"item_name"=>$itemdata["item_name"],"status"=>$itemdata["status"]];
			
			$d = $this->admin->mongoInsert("$this->database.tbl_inventory",$fields);
			
			$sbalance = intval($fields['starting_balance']);
			$issues = intval($this->common->getAddinventorycount($this->database,"tbl_issues",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
			$returns = intval($this->common->getAddinventorycount($this->database,"tbl_returns",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
			$transfer_ins = intval($this->common->getAddinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"tlocationcode",$item));
			$transfer_outs = intval($this->common->getAddinventorycount($this->database,"tbl_touts",$_SESSION['appid'],$loccode,"flcoationcode",$item));
			$adjustments = intval($this->common->getAddinventorycount($this->database,"tbl_adjustments",$_SESSION['appid'],$loccode,"tlcoationcode",$item));
			$ending_balance = intval($sbalance+$issues+$returns+$transfer_ins-$transfer_outs+$adjustments);
			
			$fields['starting_balance'] = $sbalance;
			$fields['issues'] = $issues;
			$fields['returns'] = $returns;
			$fields['transfer_ins'] = $transfer_ins;
			$fields['transfer_outs'] = $transfer_outs;
			$fields['adjustments'] = $adjustments;
			$fields['ending_balance'] = $ending_balance;
			
			$d = $this->admin->mongoUpdate("$this->database.tbl_inventory",["loccode"=>$loccode,"item.item_name"=>$item],$fields,[]);
			
		}
		
		echo json_encode(array("Status"=>"Success","Message"=>"Inventory successfully added."));
		
	}


	public function delInventory($id){

		$this->mongo_db->switch_db($this->database);		
		$lid = new MongoDB\BSON\ObjectID($id);
		$invData = $this->mongo_db->get_where("tbl_inventory",["_id"=>$lid])[0];
		
		$issues = $this->mongo_db->aggregate("tbl_issues",[
			['$match' => ["item.item_name"=>$invData["item"]->item_name,"tlcoationcode"=>$invData["loccode"],"flag"=>"uexcel"]],
		]);

// pickups		

		$pickups = $this->mongo_db->aggregate("tbl_returns",[
			['$match' => ["item.item_name"=>$invData["item"]->item_name,"tlcoationcode"=>$invData["loccode"],"flag"=>"uexcel"]],
		]);

// adjustments		

		$adjustments = $this->mongo_db->aggregate("tbl_adjustments",[
			['$match' => ["item.item_name"=>$invData["item"]->item_name,"tlcoationcode"=>$invData["loccode"],"flag"=>"uexcel"]],
		]);

// Transfers Ins		

		$transferins = $this->mongo_db->aggregate("tbl_touts",[
			['$match' => ["item.item_name"=>$invData["item"]->item_name,"tlocationcode"=>$invData["loccode"],"flag"=>"uexcel"]],
		]);

// Transfers Outs		

		$transferouts = $this->mongo_db->aggregate("tbl_touts",[
			['$match' => ["item.item_name"=>$invData["item"]->item_name,"flcoationcode"=>$invData["loccode"],"flag"=>"uexcel"]],
		]);		
		
// end query


// update issues count		
//
		if($issues){

			foreach($issues as $iss){

				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($iss["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_issues");		

			}

		}

// update pickups count		

		if($pickups){

			foreach($pickups as $pkk){

				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($pkk["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_returns");		

			}

		}

// update adjustments count		

		if($adjustments){

			foreach($adjustments as $adj){

				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($adj["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_adjustments");		

			}

		}		

// update transfer ins count		

		if($transferins){

			foreach($transferins as $tin){

				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($tin["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_touts");		
				
			}

		}
		
		if($transferouts){

			foreach($transferouts as $tout){

				$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($tout["_id"]->{'$id'})])->set(["flag"=>"excel"])->update("tbl_touts");		
				
			}

		}

		$d = $this->admin->mongoDelete("$this->database.tbl_inventory",array('_id'=>$lid),[]);
		
		if($d){
			
			echo 'success';
			
		}else{
			
			echo 'error';
			
		}
		
	}

	
	public function getInventorylocation(){
		
		$this->mongo_db->switch_db($this->database);
		
		$location = $this->input->post("location");
		
		$ldata = $this->mongo_db->get_where("tbl_locations",array("nameid"=>$location))[0];
		
		echo json_encode(["locname"=>$ldata['locname'],"loccode"=>$ldata['loccode'],"type"=>$ldata['Type'],"notes"=>$ldata['notes']]);
		
	}
	
	public function getlocationcode(){
		
		$this->mongo_db->switch_db($this->database);
		
		$location = $this->input->post("location");
		
		$ldata = $this->mongo_db->get_where("tbl_locations",array("locname"=>$location))[0];
		
		echo json_encode(["locname"=>$ldata['locname'],"loccode"=>$ldata['loccode'],"type"=>$ldata['Type'],"notes"=>$ldata['notes']]);
		
	}
	
	public function getLocdata(){
		
		$this->mongo_db->switch_db($this->database);
		
		$ldata = $this->mongo_db->order_by(["locname"=>'asc'])->get("tbl_locations");
		
		$html = '<select name="svalue[]" class="form-control select2 svalueData">';
		$html .= '<option value="">Select Location</option>';
		foreach($ldata as $ld){
			
			$html .= '<option value="'.$ld['locname'].'">'.$ld['locname'].'</option>';	
			
		}
		$html .= '</select>';
		
		echo $html;
		
	}
	
	public function exportAll($table,$filename){
		
		$this->common->exportAllfilestoexcel($table,$filename);
 
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
		
		
		if($column == "location" || $column == "flocation" || $column == "tlcoation" || $column == "locname" || $column == "tlocation" && $table != "tbl_locations"){
			
			$locations = $this->mongo_db->where(["status"=>'Active'])->get("tbl_locations");
			
			$locnames = "";
			
			$locnames = '<select class="select2 form-control svalueData" style="height: 35px !important;" data-placeholder="Choose ..." name="svalue[]" required>';			
			
			foreach($locations as $loc){

				$locnames .= '<option value="'.$loc['locname'].'">'.$loc['locname'].'</option>';

			}
			$locnames .= '</select>'; 
			
		}elseif($column == "status"){
			
			$status = '<select class="form-control svalueData" name="svalue[]" required=""><option value="Active">Active</option><option value="Inactive">Inactive</option></select>';
			
		}elseif($column == "Type"){
			
			$loctype = '<select class="form-control svalueData" name="svalue[]" required><option value="External">External</option><option value="Internal">Internal</option></select>';
			
		}elseif($column == "import_date" || $column == "shippmentdate" || $column == "reportdate" || $column == "processdate" || $column == "chepprocessdate" || $column == "last_report_date" || $column == "audit_date2019"){
			
			$import_date = '<input type="date" class="form-control svalueData" name="svalue[]" value="'.date("Y-m-d").'">';
			
		}elseif($column == "adjdirection"){
			
			$accounts = '<select class="form-control svalueData" name="svalue[]" required>';
			
			$accounts .= '<option value="IN">IN</option><option value="OUT">OUT</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "accounts" || $column == "user"){
			
			$accounts = '<select class="select2 form-control svalueData" style="height: 35px !important;" data-placeholder="Choose ..." name="svalue[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active',"appid"=>$_SESSION['appid']],[],"$this->mdb.tbl_auths");
			
			 foreach($users as $u){
				 
				 $accounts .= '<option value="'.$u->uname.'">'.$u->uname.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "item"){
			
			$accounts = '<select class="select2 form-control svalueData" style="height: 35px !important;" data-placeholder="Choose ..." name="svalue[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active'],[],"$this->database.tbl_items");
			
			 foreach($users as $u){
				 
				 $accounts .= '<option value="'.$u->item_name.'">'.$u->item_name.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "uploadedetochep"){
			
			$accounts = '<select class="form-control svalueData" name="svalue[]" required>';
			
			$accounts .= '<option value="Yes">Yes</option><option value="Hold">Hold</option><option value="From Customer">From Customer</option><option value="No">No</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "loctype"){
			
			$accounts = '<select class="form-control svalueData" name="svalue[]" required>';
			
			$accounts .= '<option value="External">External</option><option value="Internal">Internal</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "reasonforhold"){
			
			$accounts = '<select class="form-control svalueData" name="svalue[]" required>';
			
			$accounts .= '<option value="Reversed in Customer">Reversed in Customer</option><option value="Suspended During Customer Upload">Suspended During Customer Upload</option><option value="Rejected During Customer Upload">Rejected During Customer Upload</option><option value="Error During Customer Upload">Error During Customer Upload</option><option value="Need Customer ID">Need Customer ID</option><option value="Duplicate Transaction">Duplicate Transaction</option><option value="International Shipment">International Shipment</option><option value="Data Error on Submission to">Data Error on Submission to</option>';
			
			$accounts .= '</select>';
			
		}else{
			
			if(($table == "tbl_inventory" && $column == "starting_balance") || ($table == "tbl_transfers" && $column == "quantity")){
			
				$import_date = '<input type="number" name="svalue[]" class="form-control svalueData" pattern="^[0-9]"  required>';
			
			}elseif($column == "issues" || $column == "returns" || $column == "transfer_ins" || $column == "transfer_outs" || $column == "adjustments" || $column == "ending_balance" || $column == "audit_count2019" || $column == "quantity"){
				
				$import_date = '<input type="number" name="svalue[]" class="form-control svalueData" pattern="^[0-9]" required>';
				
			}else{
				
				$import_date = '<input type="text" name="svalue[]" class="form-control svalueData">';
				
			}
		}
		
		$operators = $this->common->getConditionbydatatype($datatype);
		
		$oper = '<select name="value[]" class="form-control '.$onchg.' valueData valueData_filter" rCount="'.$rRef.'" lopid="'.$opt.'"  opid="'.$opt.'">';
		
		foreach($operators as $op){
			
			$oper .= '<option value="'.$op.'">'.$op.'</option>';
			
		}
		
		$oper .= '</select>';
		
		$fields = array("locnames"=>$locnames,"status"=>$status,"location_type"=>$loctype,"import_date"=>$import_date,"accounts"=>$accounts,"common"=>$common);
		
		echo json_encode(["fields"=>$fields,"operators"=>$oper]);
		
	}
    
    public function getDays($condition){
	
		if($condition == "week"){

			$signupdate=date("Y-m-d");
			$signupweek=date("W",strtotime($signupdate));
			$year=date("Y",strtotime($signupdate));
			$currentweek = date("W");

			$dto = new DateTime();
			$start = $dto->setISODate($year, $signupweek, 0)->format('m-d-Y');
			$finish = $dto->setISODate($year, $signupweek, 7)->format('m-d-Y');

			$astart = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
			$afinish = $dto->setISODate($year, $signupweek, 7)->format('Y-m-d');
			
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
		return($data);		
		
	}
	
	
	function filter_excel_download(){
		$ids = $this->session->userdata('export_ids');
		$table = $this->session->userdata('export_table_name');
		$file_name = "ExportFile";
		$this->common->exportAllfilestoexcel_Filter($ids,$table,$file_name);		
	}
function date_script(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_inventory");
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["audit_date2019"] != "" || $res["audit_date2019"] != " "){
				 
				 $custom_date = $res["audit_date2019"];
					echo $custom_date;echo '<br>';
		     }
	    }
	}
	function quantity_script(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_touts");
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["quantity"] != "" || $res["quantity"] != " "){
				 $qty = intval($res["quantity"]);
				 
				 $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["quantity"=>$qty])->update('tbl_touts');

		     }
	    }
	}
	function space_script(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_adjustments");
		$i=1;
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res['ongreference'] == " "){
				 $rsh = "";
				 //$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["reasonforhold"=>$rsh])->update('tbl_touts');
				  echo $i;echo $res['ongreference'];echo '<br>';
			 }
			
			 $i++;
	    }
	}
	
	public function getDatatypeconditions_dyn(){
		//echo '<pre>';print_r($_POST);exit;
		$this->mongo_db->switch_db($this->database);
		
		$column = explode("-",$this->input->post("column"))[0];
		$datatype = explode("-",$this->input->post("column"))[1];
		
		$onchangeColref = $this->input->post("onchangeColref");
		$rCount = $this->input->post("rCount");
		$form_type = $this->input->post("form_type");
		
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
		
		
		if($column == "location" || $column == "flocation" || $column == "tlcoation" || $column == "locname" || $column == "tlocation" && $table != "tbl_locations"){
			
			$locations = $this->mongo_db->where(["status"=>'Active'])->get("tbl_locations");
			
			$locnames = "";
			
			$locnames = '<select class="select2 form-control svalueData'.$form_type.'" style="height: 35px !important;" data-placeholder="Choose ..." name="svalue[]" required>';			
			
			foreach($locations as $loc){

				$locnames .= '<option value="'.$loc['locname'].'">'.$loc['locname'].'</option>';

			}
			$locnames .= '</select>'; 
			
		}elseif($column == "status"){
			
			$status = '<select class="form-control svalueData'.$form_type.'" name="svalue[]" required=""><option value="Active">Active</option><option value="Inactive">Inactive</option></select>';
			
		}elseif($column == "Type"){
			
			$loctype = '<select class="form-control svalueData'.$form_type.'" name="svalue[]" required><option value="External">External</option><option value="Internal">Internal</option></select>';
			
		}elseif($column == "import_date" || $column == "shippmentdate" || $column == "reportdate" || $column == "processdate" || $column == "chepprocessdate" || $column == "last_report_date" || $column == "audit_date2019"){
			
			$import_date = '<input type="date" class="form-control svalueData'.$form_type.'" name="svalue[]" value="'.date("Y-m-d").'">';
			
		}elseif($column == "adjdirection"){
			
			$accounts = '<select class="form-control svalueData'.$form_type.'" name="svalue[]" required>';
			
			$accounts .= '<option value="IN">IN</option><option value="OUT">OUT</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "accounts" || $column == "user"){
			
			$accounts = '<select class="select2 form-control svalueData'.$form_type.'" style="height: 35px !important;" data-placeholder="Choose ..." name="svalue[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active',"appid"=>$_SESSION['appid']],[],"$this->mdb.tbl_auths");
			
			 foreach($users as $u){
				 
				 $accounts .= '<option value="'.$u->uname.'">'.$u->uname.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "item"){
			
			$accounts = '<select class="select2 form-control svalueData'.$form_type.'" style="height: 35px !important;" data-placeholder="Choose ..." name="svalue[]" required>';
			
			$users = $this->admin->getArray("",["status"=>'Active'],[],"$this->database.tbl_items");
			
			 foreach($users as $u){
				 
				 $accounts .= '<option value="'.$u->item_name.'">'.$u->item_name.'</option>';
				 
			 }
			
			$accounts .= '</select>';
			
		}elseif($column == "uploadedetochep"){
			
			$accounts = '<select class="form-control svalueData'.$form_type.'" name="svalue[]" required>';
			
			$accounts .= '<option value="Yes">Yes</option><option value="Hold">Hold</option><option value="From Customer">From Customer</option><option value="No">No</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "loctype"){
			
			$accounts = '<select class="form-control svalueData'.$form_type.'" name="svalue[]" required>';
			
			$accounts .= '<option value="External">External</option><option value="Internal">Internal</option>';
			
			$accounts .= '</select>';
			
		}elseif($column == "reasonforhold"){
			
			$accounts = '<select class="form-control svalueData'.$form_type.'" name="svalue[]" required>';
			
			$accounts .= '<option value="Reversed in Customer">Reversed in Customer</option><option value="Suspended During Customer Upload">Suspended During Customer Upload</option><option value="Rejected During Customer Upload">Rejected During Customer Upload</option><option value="Error During Customer Upload">Error During Customer Upload</option><option value="Need Customer ID">Need Customer ID</option><option value="Duplicate Transaction">Duplicate Transaction</option><option value="International Shipment">International Shipment</option><option value="Data Error on Submission to">Data Error on Submission to</option>';
			
			$accounts .= '</select>';
			
		}else{
			
			if(($table == "tbl_inventory" && $column == "starting_balance") || ($table == "tbl_transfers" && $column == "quantity")){
			
				$import_date = '<input type="number" name="svalue[]" class="form-control svalueData'.$form_type.'" pattern="^[0-9]"  required>';
			
			}elseif($column == "issues" || $column == "returns" || $column == "transfer_ins" || $column == "transfer_outs" || $column == "adjustments" || $column == "ending_balance" || $column == "audit_count2019" || $column == "quantity"){
				
				$import_date = '<input type="number" name="svalue[]" class="form-control svalueData'.$form_type.'" pattern="^[0-9]" required>';
				
			}else{
				
				$import_date = '<input type="text" name="svalue[]" class="form-control svalueData'.$form_type.'">';
				
			}
		}
		
		$operators = $this->common->getConditionbydatatype($datatype);
		$oper = '<select name="value[]" class="form-control '.$onchg.' valueData'.$form_type.' valueData_filter" rCount="'.$rRef.'" lopid'.$form_type.'="'.$opt.'"  opid="'.$opt.'">';
		
		
		foreach($operators as $op){
			
			$oper .= '<option value="'.$op.'">'.$op.'</option>';
			
		}
		
		$oper .= '</select>';
		
		$fields = array("locnames"=>$locnames,"status"=>$status,"location_type"=>$loctype,"import_date"=>$import_date,"accounts"=>$accounts,"common"=>$common);
		
		echo json_encode(["fields"=>$fields,"operators"=>$oper]);
		
	}
	public function addFilter_mainAdmin(){
		//echo '<pre>';print_r($_POST);exit;
		$cause = $this->input->post('cause');
		$field = $this->input->post('field');
		$value = $this->input->post('value');
		$svalue = $this->input->post('svalue');
		$dvalue = $this->input->post('dvalue');
		$appid = $this->input->post('id');
		$table = $this->input->post('table');
		$item = $this->input->post('item');
		$loc_code = $this->input->post('loc_code');
		$app_id = $this->input->post('app_id');
		$filter_type = $this->input->post('filter_type');

		$this->session->unset_userdata('export_ids');
		$this->session->unset_userdata('export_table_name');
		
		$cdays = [];
		$i = 0;
		foreach($value as $kk => $val){
			if($val == "is during the previous" || $val == "is before the previous" || $val == "is during the next"){
				$cdays[] = $this->input->post("dvalue")[$i];
				$i++;
			}else{
				$cdays[] = "";
			}
        }
		$dvalue=$cdays;
		
		if(count($cause) > 0){
		
			$query=[];
			$queryor=[];
			$test=[];
			
			foreach ($cause as $key => $val) {
				if($field[$key] == 'import_date' || $field[$key] == 'shippmentdate' || $field[$key] == 'reportdate' || $field[$key] == 'processdate' || $field[$key] == 'chepprocessdate' || $field[$key] == 'last_report_date' || $field[$key] == 'audit_date2019'){
					/* if(strtotime($svalue[$key]) !== false){
						$svalue[$key] = date('m-d-Y', strtotime($svalue[$key]));
					} */
				}
				if($field[$key] == "quantity" || $field[$key] == "starting_balance"){
						$svalue[$key] = intval($svalue[$key]);
				}
				
				if($field[$key] == "tlcoation"){
						$field[$key] = "tlcoation.locname";
				}
				if($field[$key] == "flocation"){
						$field[$key] = "flocation.locname";
				}
				
				if($field[$key] == "tlocation"){
						$field[$key] = "tlocation.locname";
				}
				
				if($field[$key] == "locname"){
						$field[$key] = "locname.locname";
				}
				     
				
				if(($val == 'where' || $val == 'and')  && $value[$key] == 'contains'){
				array_push($query, array($field[$key]=>['$regex'=>$svalue[$key],'$options'=>'i']));	
				}
				else if(($val == 'where' || $val == 'and') && $value[$key] == 'does not contain'){
				array_push($query, array($field[$key]=>['$regex'=>'^((?!'.$svalue[$key].').)*$','$options'=>'i']));	
				}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is'){
				array_push($query, array($field[$key]=>$svalue[$key]));		
				}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is not'){
				array_push($query, array($field[$key]=>['$ne'=>$svalue[$key]]));		
				}
				else if(($val == 'where' || $val == 'and') && $value[$key] == 'starts with'){
				array_push($query, array($field[$key]=>['$regex'=>'^'.$svalue[$key],'$options'=>'i']));
				}else if(($val == 'where' || $val == 'and') && $value[$key] == 'ends with'){
				array_push($query, array($field[$key]=>['$regex'=>$svalue[$key].'$','$options'=>'i']));
				}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is blank'){
				array_push($query, array($field[$key]=>''));		
				}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is not blank'){
				array_push($query, array($field[$key]=>['$ne'=>'','$exists' => true]));		
				}
				else if(($val == 'where' || $val == 'and') && $value[$key] == 'higher than'){

				array_push($query, array($field[$key]=>['$gt'=>intval($svalue[$key])]));		
				}
				else if(($val == 'where' || $val == 'and') && $value[$key] == 'lower than'){
				array_push($query, array($field[$key]=>['$lt'=>intval($svalue[$key])]));		
				}
				else if(($val == 'where' || $val == 'and') && $value[$key] == 'is any'){
				array_push($query, array($field[$key]=>['$ne'=>'']));		
				}
                elseif(($val == 'where' || $val == 'and') && $value[$key] == "is during the current"){
					$dates = $this->getDays($svalue[$key]); 
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($query, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
			    }elseif(($val == 'where' || $val == 'and') && $value[$key] == "is during the previous"){
					$dates = $this->getDayscount($dvalue[$key],"minus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($query, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
				}elseif(($val == 'where' || $val == 'and') && $value[$key] == "is before the previous"){
					$dates = $this->getDayscount($dvalue[$key],"minus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];

					array_push($query, array($field[$key]=>['$lt'=>$start,'$ne'=>'']));
				}elseif(($val == 'where' || $val == 'and') && ($value[$key] == "is during the next" || $value[$key] == "is after the next")){
					$dates = $this->getDayscount($dvalue[$key],"plus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($query, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
				}elseif(($val == 'where' || $val == 'and') && ($value[$key] == "is before" || $value[$key] == "is after")){
					if($value[$key] == "is before"){

					array_push($query, array($field[$key]=>['$lt'=>$svalue[$key]]));	

					}elseif($value[$key] == "is after"){
                   
				    array_push($query, array($field[$key]=>['$gt'=>$svalue[$key]]));
						
					}
				}elseif(($val == 'where' || $val == 'and') && ($value[$key] == "is today or before" ||$value[$key] == "is today or after" || $value[$key] == "is before today" || $value[$key] == "is after today" || $value[$key] == "is after current time" || $value[$key] == "is before current time")){

				    $date = date("Y-m-d");               

					if($value[$key] == "is today or before" || $value[$key] == "is before today" || $value[$key] == "is before current time"){
						
						if($value[$key] == "is before today" || $value[$key] == "is before current time"){
							array_push($query, array($field[$key]=>['$lt'=>$date]));
						}else{
							array_push($query, array($field[$key]=>['$lte'=>$date]));
						}	
					}elseif($value[$key] == "is today or after" || $value[$key] == "is after today" || $value[$key] == "is after current time"){
						if($value[$key] == "is after today" || $value[$key] == "is after current time"){
							array_push($query, array($field[$key]=>['$gt'=>$date]));
						}else{
							array_push($query, array($field[$key]=>['$gte'=>$date]));
						}
					}

			    }elseif(($val == 'where' || $val == 'and') && $value[$key] == "is today"){
					 $date = date("Y-m-d");
					 array_push($query, array($field[$key]=>$date));
			    }

				elseif(($val == 'where' || $val == 'or')  && $value[$key] == 'contains'){
				array_push($queryor, array($field[$key]=>['$regex'=>$svalue[$key],'$options'=>'i']));	
				}
				else if(($val == 'where' || $val == 'or') && $value[$key] == 'does not contain'){
				array_push($queryor, array($field[$key]=>['$regex'=>'^((?!'.$svalue[$key].').)*$','$options'=>'i']));	
				}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is'){
				array_push($queryor, array($field[$key]=>$svalue[$key]));		
				}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is not'){
				array_push($queryor, array($field[$key]=>['$ne'=>$svalue[$key]]));		
				}
				else if(($val == 'where' || $val == 'or') && $value[$key] == 'starts with'){
				array_push($queryor, array($field[$key]=>['$regex'=>'^'.$svalue[$key],'$options'=>'i']));
				}else if(($val == 'where' || $val == 'or') && $value[$key] == 'ends with'){
				array_push($queryor, array($field[$key]=>['$regex'=>$svalue[$key].'$','$options'=>'i']));
				}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is blank'){
				array_push($queryor, array($field[$key]=>''));		
				}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is not blank'){
				array_push($queryor, array($field[$key]=>['$ne'=>'','$exists' => true]));		
				}
				elseif(($val == 'where' || $val == 'or') && $value[$key] == "is during the current"){
					$dates = $this->getDays($svalue[$key]); 
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($queryor, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
			    }elseif(($val == 'where' || $val == 'or') && $value[$key] == "is during the previous"){
					$dates = $this->getDayscount($dvalue[$key],"minus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($queryor, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
				}elseif(($val == 'where' || $val == 'or') && $value[$key] == "is before the previous"){
					$dates = $this->getDayscount($dvalue[$key],"minus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($queryor, array($field[$key]=>['$lt'=>$start]));
				}elseif(($val == 'where' || $val == 'or') && ($value[$key] == "is during the next" || $value[$key] == "is after the next")){
					$dates = $this->getDayscount($dvalue[$key],"plus",$svalue[$key]);
					$start = $dates["astart"];
					$end = $dates["aend"];
					array_push($queryor, array($field[$key]=>['$gte'=>$start,'$lte'=>$end]));
				}elseif(($val == 'where' || $val == 'or') && ($value[$key] == "is before" || $value[$key] == "is after")){
					if($value[$key] == "is before"){

					array_push($queryor, array($field[$key]=>['$lt'=>$svalue[$key]]));	

					}elseif($value[$key] == "is after"){
                   
				    array_push($queryor, array($field[$key]=>['$gt'=>$svalue[$key]]));
						
					}
				}elseif(($val == 'where' || $val == 'or') && ($value[$key] == "is today or before" ||$value[$key] == "is today or after" || $value[$key] == "is before today" || $value[$key] == "is after today" || $value[$key] == "is after current time" || $value[$key] == "is before current time")){

				    $date = date("Y-m-d");               

					if($value[$key] == "is today or before" || $value[$key] == "is before today" || $value[$key] == "is before current time"){
						
						if($value[$key] == "is before today" || $value[$key] == "is before current time"){
							array_push($queryor, array($field[$key]=>['$lt'=>$date]));
						}else{
							array_push($queryor, array($field[$key]=>['$lte'=>$date]));
						}	
					}elseif($value[$key] == "is today or after" || $value[$key] == "is after today" || $value[$key] == "is after current time"){
						if($value[$key] == "is after today" || $value[$key] == "is after current time"){
							array_push($queryor, array($field[$key]=>['$gt'=>$date]));
						}else{
							array_push($queryor, array($field[$key]=>['$gte'=>$date]));
						}
					}

			    }elseif(($val == 'where' || $val == 'or') && $value[$key] == "is today"){
					 $date = date("Y-m-d");
					 array_push($queryor, array($field[$key]=>$date));
			    }
				else if(($val == 'where' || $val == 'or') && $value[$key] == 'higher than'){
				array_push($queryor, array($field[$key]=>['$gt'=>intval($svalue[$key])]));		
				}
				else if(($val == 'where' || $val == 'or') && $value[$key] == 'lower than'){
				array_push($queryor, array($field[$key]=>['$lt'=>intval($svalue[$key])]));		
				}
				else if(($val == 'where' || $val == 'or') && $value[$key] == 'is any'){
				array_push($queryor, array($field[$key]=>['$ne'=>'']));		
				}
			}

			$mng = $this->admin->Mconfig();
			/* echo '<pre>';print_r($query);
			echo '<pre>';print_r($queryor);
			exit; */
			
			 
        
			
			
			if(count($queryor) > 0){
				array_push($query, array('$or'=>$queryor));	
				$type='$or';
			}else{
				$type='$and';
			}
			
						
						
					
						
						if($filter_type == "transfer_outs"){
							
							$command = new MongoDB\Driver\Command([
								'aggregate'=>$table,
								'cursor' => new stdClass,
								'pipeline'=>[
									['$match'=>[$type=>$query,"appId"=>$appid,"flcoationcode"=>$loc_code,"item.item_name"=>$item ]]
								]
							]);
						}elseif($filter_type == "transfer_ins"){
							$command = new MongoDB\Driver\Command([
								'aggregate'=>$table,
								'cursor' => new stdClass,
								'pipeline'=>[
									['$match'=>[$type=>$query,"appId"=>$appid,"tlocationcode"=>$loc_code,"item.item_name"=>$item ]]
								]
							]);
						}elseif($filter_type == "shipments" || $filter_type == "pickup" || $filter_type == "adjus"){
							$command = new MongoDB\Driver\Command([
								'aggregate'=>$table,
								'cursor' => new stdClass,
								'pipeline'=>[
									['$match'=>[$type=>$query,"appId"=>$appid,"tlcoationcode"=>$loc_code,"item.item_name"=>$item ]]
								]
							]);
						}
						
			
			
			$cursor = $mng->executeCommand("$this->database", $command);
		    //echo '<pre>';print_r($cursor);exit;
		}else{
			
			$cursor = $this->admin->getArray("",[],[],"$this->database.$table");
			
		}

		
		$out = $this->common->getFiltervalues($cursor,$table);
		//echo '<pre>';print_r($out);exit;
		foreach($out as $r1){
			
			$r2 = json_decode(json_encode($r1), true);
			$ids[] = new MongoDB\BSON\ObjectID($r2["_id"]{'$oid'});
		}
		$this->session->set_userdata('export_ids', $ids);
		$this->session->set_userdata('export_table_name', $table);
		
		$results = ["sEcho" => 1,"iTotalRecords" => count($out),"iTotalDisplayRecords" => count($out),"aaData" => $out];
		
		echo json_encode($results);
	}
	
	function loc_filter(){
		//echo '<pre>';print_r($_POST);exit;
		$appid = $this->input->post('id');
		$item = $this->input->post('item');
		
		$mng= $this->admin->Mconfig();
		$query = $this->mongo_db->get_where("tbl_auths",array("email"=>$this->session->userdata("admin_email")));
       
		$locations = $query[0]["locations"];
		$database = $this->database;

		
		
		foreach($locations as $location){
			
				$loccode = $location->loccode;
				$locdata = $this->admin->getRow("",['loccode'=>$loccode],[],"$database.tbl_locations");
                
				if($locdata->status == "Active"){

					$locinvdata = $this->admin->getRow("",['appId'=>$appid,"item.item_name"=>$item,"loccode"=>$loccode],[],"$database.tbl_inventory");
					$issues=($this->common->getInventorycount($database,"tbl_issues",$appid,$loccode,"tlcoationcode",$item));
					$returns=($this->common->getInventorycount($database,"tbl_returns",$appid,$loccode,"tlcoationcode",$item));
					$tins=($this->common->getInventorycount($database,"tbl_touts",$appid,$loccode,"tlocationcode",$item));
					$touts=($this->common->getInventorycount($database,"tbl_touts",$appid,$loccode,"flcoationcode",$item));
					$adjusts=($this->common->getInventorycount($database,"tbl_adjustments",$appid,$loccode,"tlcoationcode",$item));
					$eb = ($locinvdata->starting_balance+$issues+$returns+$tins-$touts+$adjusts);
					$audit_count2019 = (intval($locinvdata->audit_count2019) != "") ? intval($locinvdata->audit_count2019) : 0;
					$audit_date = ($locinvdata->audit_date2019 != "") ? ($locinvdata->audit_date2019) : "";
					$data[] = array(
					"locname" => $locdata->locname,
					"loccode" => $loccode,
					"issues" => $issues,
					"returns" => $returns,
					"transfer_ins" => $tins,
					"transfer_outs" => $touts,
					"adjustments" => $adjusts,
					"ending_balance" => $eb,
					"audit_count2019" => $audit_count2019,
					"audit_date2019" => $audit_date,
					"item" => $item,
					"appId" => $appid,
					"email"=> $this->session->userdata("admin_email")
					);
		        }				
		}
		$d = $this->admin->mongoInsert("$this->mdb.temp_inventory",$data,"bulk");
				
		$cause = $this->input->post('cause');
		$field = $this->input->post('field');
		$value = $this->input->post('value');
		$svalue = $this->input->post('svalue');
		$dvalue = $this->input->post('dvalue');
		$appid = $this->input->post('id');
		$table = $this->input->post('table');
		
		if(count($cause) > 0){
		
			$query=[];
			$queryor=[];
			$out=[];
			
		foreach ($cause as $key => $val) {
						
						if($field[$key] == "issues" || $field[$key] == "returns" || $field[$key] == "transfer_ins" || $field[$key] == "transfer_outs" || $field[$key] == "adjustments" || $field[$key] == "ending_balance" || $field[$key] == "audit_count2019"){
								$svalue[$key] = intval($svalue[$key]);
						}
						if(($val == 'where' || $val == 'and')  && $value[$key] == 'contains'){
						array_push($query, array($field[$key]=>['$regex'=>$svalue[$key],'$options'=>'i']));	
						}
						else if(($val == 'where' || $val == 'and') && $value[$key] == 'does not contain'){
						array_push($query, array($field[$key]=>['$regex'=>'^((?!'.$svalue[$key].').)*$','$options'=>'i']));	
						}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is'){
						array_push($query, array($field[$key]=>$svalue[$key]));		
						}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is not'){
						array_push($query, array($field[$key]=>['$ne'=>$svalue[$key]]));		
						}
						else if(($val == 'where' || $val == 'and') && $value[$key] == 'starts with'){
						array_push($query, array($field[$key]=>['$regex'=>'^'.$svalue[$key],'$options'=>'i']));
						}else if(($val == 'where' || $val == 'and') && $value[$key] == 'ends with'){
						array_push($query, array($field[$key]=>['$regex'=>$svalue[$key].'$','$options'=>'i']));
						}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is blank'){
						array_push($query, array($field[$key]=>''));		
						}else if(($val == 'where' || $val == 'and') && $value[$key] == 'is not blank'){
						array_push($query, array($field[$key]=>['$ne'=>'','$exists' => true]));		

						}
						else if(($val == 'where' || $val == 'and') && $value[$key] == 'higher than'){

						array_push($query, array($field[$key]=>['$gt'=>intval($svalue[$key])]));		
						}
						else if(($val == 'where' || $val == 'and') && $value[$key] == 'lower than'){
						array_push($query, array($field[$key]=>['$lt'=>intval($svalue[$key])]));		
						}
						else if(($val == 'where' || $val == 'and') && $value[$key] == 'is any'){
						array_push($query, array($field[$key]=>['$ne'=>'']));		
						}
						

						elseif(($val == 'where' || $val == 'or')  && $value[$key] == 'contains'){
						array_push($queryor, array($field[$key]=>['$regex'=>$svalue[$key],'$options'=>'i']));	
						}
						else if(($val == 'where' || $val == 'or') && $value[$key] == 'does not contain'){
						array_push($queryor, array($field[$key]=>['$regex'=>'^((?!'.$svalue[$key].').)*$','$options'=>'i']));	
						}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is'){
						array_push($queryor, array($field[$key]=>$svalue[$key]));		
						}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is not'){
						array_push($queryor, array($field[$key]=>['$ne'=>$svalue[$key]]));		
						}
						else if(($val == 'where' || $val == 'or') && $value[$key] == 'starts with'){
						array_push($queryor, array($field[$key]=>['$regex'=>'^'.$svalue[$key],'$options'=>'i']));
						}else if(($val == 'where' || $val == 'or') && $value[$key] == 'ends with'){
						array_push($queryor, array($field[$key]=>['$regex'=>$svalue[$key].'$','$options'=>'i']));
						}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is blank'){
						array_push($queryor, array($field[$key]=>''));		
						}else if(($val == 'where' || $val == 'or') && $value[$key] == 'is not blank'){
						array_push($queryor, array($field[$key]=>['$ne'=>'','$exists' => true]));		
						}
						
						else if(($val == 'where' || $val == 'or') && $value[$key] == 'higher than'){
						array_push($queryor, array($field[$key]=>['$gt'=>intval($svalue[$key])]));		
						}
						else if(($val == 'where' || $val == 'or') && $value[$key] == 'lower than'){
						array_push($queryor, array($field[$key]=>['$lt'=>intval($svalue[$key])]));		
						}
						else if(($val == 'where' || $val == 'or') && $value[$key] == 'is any'){
						array_push($queryor, array($field[$key]=>['$ne'=>'']));		
						}
					}
		
					if(count($queryor) > 0){
						array_push($query, array('$or'=>$queryor));	
						$type='$or';
					}else{
						$type='$and';
					}
					//echo '<pre>';print_r($query);exit;
					$mng = $this->admin->Mconfig();
					$command = new MongoDB\Driver\Command([
						'aggregate'=>"temp_inventory",
						'cursor' => new stdClass,
						'pipeline'=>[
							['$match'=>[$type=>$query,"item"=>$item,"appId"=>$appid ]]
						]
					]);
					$cursor = $mng->executeCommand("ongpool", $command);
	            }else{
                    					
					$cursor = $this->admin->getArray("",[],[],"$this->database.$table");					
				}
				
				$tmp_res = $this->admin->getArray("",["item" => $item,"appId" => $appid,"email"=> $this->session->userdata("admin_email")],[],"$this->mdb.temp_inventory");
				 foreach($tmp_res as $u){				 
					 $d = $this->admin->mongoDelete("$this->mdb.temp_inventory",array('_id'=>new MongoDB\BSON\ObjectID($u->_id)),[]);				 
				 }
				 
				    $msg='';				
			   	    foreach($cursor as $row1){
						
						$audDate = ($row1->audit_date2019 != "") ? date("m-d-Y",strtotime($row1->audit_date2019)) : "";
						$dat = count($row1);
						$msg.='<tr>
						<td><a href="'.base_url().'main/inventory/location/'.$row1->loccode.'/off/'.$row1->item.'"><span class="badge badge-primary" style="font-size: 14px;white-space: unset !important;">'.$row1->locname.'-'.$row1->loccode.'</span></a></td>
						<td>'.$row1->issues.'</td>
						<td>'.$row1->returns.'</td>
						<td align="right">'.$row1->transfer_ins.'</td>
						<td align="right">'.$row1->transfer_outs.'</td>
						<td align="right">'.$row1->adjustments.'</td>
						<td align="right">'.$row1->ending_balance.'</td>
						<td align="right">'.$row1->audit_count2019.'</td>
						<td align="right">'.$audDate.'</td>
						</tr>';
						$iss[] = $row1->issues;
						$ret[] = $row1->returns;
						$tin[] = $row1->transfer_ins;
						$tou[] = $row1->transfer_outs;
						$adj[] = $row1->adjustments;
						$ebs[] = $row1->ending_balance;
						$ac219[] = $row1->audit_count2019;
				    }				
			        $msg.='<tr>
					<td></td>
					<td style="font-weight: bold;text-align: right"><span id="issues_count">'.array_sum($iss).'</span></td>
					<td style="font-weight: bold;text-align: right"><span id="returns_count">'.array_sum($ret).'</span></td>
					<td style="font-weight: bold;text-align: right"><span id="tins_count">'.array_sum($tin).'</span></td>
					<td style="font-weight: bold;text-align: right"><span id="touts_count">'.array_sum($tou).'</span></td>
					<td style="font-weight: bold;text-align: right"><span id="adjustments_count">'.array_sum($adj).'</span></td>
					<td style="font-weight: bold;text-align: right"><span id="ebal_count">'.array_sum($ebs).'</span></td>
					<td style="font-weight: bold;text-align: right"><span id="audit19_count">'.array_sum($ac219).'</span></td>
					<td></td>
					</tr>'; 
				 
				if($dat == 0 || $dat == ""){
					$msg.='<tr>
					<td colspan="8"><p style="text-align:center">No Data Avaliable</p></td>
					</tr>';
					echo $msg;
				}else{
					echo $msg;
				}
                			
		

	}
	

    function starting_script(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_inventory");
		$i=1;
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if(is_string($res['starting_balance'])){
				 $rsh = intval($res['starting_balance']);
				 $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["starting_balance"=>$rsh])->update('tbl_inventory');
				  
			 }			  
	    }
	}
	function date_script6(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_issues");
		$test = "";
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["shippmentdate"] != "" || $res["shippmentdate"] != " "){
				 
				 $custom_date = $res["shippmentdate"];
					if ($custom_date == "1969-12-31") {
					    $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["shippmentdate"=>""])->update('tbl_issues');
					}else{
						$mp=explode("-",$custom_date);
						if(strlen($mp[1]) == 1){
							$mnth = sprintf("%02d", $mp[1]);
							$test = "work";
						}else{
							$mnth = $mp[1];
							$test = "";
						}
						
						if(strlen($mp[2]) == 1){
							$dts = sprintf("%02d", $mp[2]);
							$test = "work";
						}else{
							$dts = $mp[2];
							$test = "";
						}
						
						$date = $mp[0]."-".$mnth."-".$dts;
						if($test == "work"){
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["shippmentdate"=>$date])->update('tbl_issues');
						}	
					}
		     }
	    }
		$this->date_script7();
	}
	function date_script7(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_issues");
		$i = 1;
		$test = "";
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["chepprocessdate"] != "" || $res["chepprocessdate"] != " "){
				 
				 $custom_date = $res["chepprocessdate"];
					if ($custom_date == "1969-12-31") {
					     $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["chepprocessdate"=>""])->update('tbl_issues');
					}else{
						$mp=explode("-",$custom_date);
						if(strlen($mp[1]) == 1){
							$mnth = sprintf("%02d", $mp[1]);
							$test = "work";
						}else{
							$mnth = $mp[1];
							$test = "";
						}
						
						if(strlen($mp[2]) == 1){
							$dts = sprintf("%02d", $mp[2]);
							$test = "work";
						}else{
							$dts = $mp[2];
							$test = "";
						}
						
						$date = $mp[0]."-".$mnth."-".$dts;
						if($test == "work"){
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["chepprocessdate"=>$date])->update('tbl_issues');
						}	
					}
		     }
			 $i++;
	    }
		$this->date_script8();
	}
	function date_script8(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_returns");
		$test = "";
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["shippmentdate"] != "" || $res["shippmentdate"] != " "){
				 
				 $custom_date = $res["shippmentdate"];
					if ($custom_date == "1969-12-31") {
					     $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["shippmentdate"=>""])->update('tbl_returns');
					}else{
						$mp=explode("-",$custom_date);
						if(strlen($mp[1]) == 1){
							$mnth = sprintf("%02d", $mp[1]);
							$test = "work";
						}else{
							$mnth = $mp[1];
							$test = "";
						}
						
						if(strlen($mp[2]) == 1){
							$dts = sprintf("%02d", $mp[2]);
							$test = "work";
						}else{
							$dts = $mp[2];
							$test = "";
						}
						
						$date = $mp[0]."-".$mnth."-".$dts;
						if($test == "work"){
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["shippmentdate"=>$date])->update('tbl_returns');
						}	
					}
		     }
	    }
		$this->date_script9();
	}
	function date_script9(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_returns");
		$i = 1;
		$test = "";
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["chepprocessdate"] != "" || $res["chepprocessdate"] != " "){
				 
				 $custom_date = $res["chepprocessdate"];
					if ($custom_date == "1969-12-31") {
					    $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["chepprocessdate"=>""])->update('tbl_returns');
					}else{
						$mp=explode("-",$custom_date);
						if(strlen($mp[1]) == 1){
							$mnth = sprintf("%02d", $mp[1]);
							$test = "work";
						}else{
							$mnth = $mp[1];
							$test = "";
						}
						
						if(strlen($mp[2]) == 1){
							$dts = sprintf("%02d", $mp[2]);
							$test = "work";
						}else{
							$dts = $mp[2];
							$test = "";
						}
						
						$date = $mp[0]."-".$mnth."-".$dts;
						if($test == "work"){
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["chepprocessdate"=>$date])->update('tbl_returns');
						}	
					}
		     }
			 $i++;
	    }
		$this->date_script10();
	}
	function date_script10(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_adjustments");
		$test = "";
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["shippmentdate"] != "" || $res["shippmentdate"] != " "){
				 
				 $custom_date = $res["shippmentdate"];
					if ($custom_date == "1969-12-31") {
					     $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["shippmentdate"=>""])->update('tbl_adjustments');
					}else{
						$mp=explode("-",$custom_date);
						if(strlen($mp[1]) == 1){
							$mnth = sprintf("%02d", $mp[1]);
							$test = "work";
						}else{
							$mnth = $mp[1];
							$test = "";
						}
						
						if(strlen($mp[2]) == 1){
							$dts = sprintf("%02d", $mp[2]);
							$test = "work";
						}else{
							$dts = $mp[2];
							$test = "";
						}
						
						$date = $mp[0]."-".$mnth."-".$dts;
						if($test == "work"){
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["shippmentdate"=>$date])->update('tbl_adjustments');
						}	
					}
		     }
	    }
		$this->date_script11();
	}
	function date_script11(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_adjustments");
		$i = 1;
		$test = "";
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["chepprocessdate"] != "" || $res["chepprocessdate"] != " "){
				 
				 $custom_date = $res["chepprocessdate"];
					if ($custom_date == "1969-12-31") {
					    $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["chepprocessdate"=>""])->update('tbl_adjustments');
					}else{
						$mp=explode("-",$custom_date);
						if(strlen($mp[1]) == 1){
							$mnth = sprintf("%02d", $mp[1]);
							$test = "work";
						}else{
							$mnth = $mp[1];
							$test = "";
						}
						
						if(strlen($mp[2]) == 1){
							$dts = sprintf("%02d", $mp[2]);
							$test = "work";
						}else{
							$dts = $mp[2];
							$test = "";
						}
						
						$date = $mp[0]."-".$mnth."-".$dts;
						if($test == "work"){
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["chepprocessdate"=>$date])->update('tbl_adjustments');
						}	
					}
		     }
			 $i++;
	    }
		//$this->date_script12();
	}
	function date_script12(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_inventory");
		$i = 1;
		$test = "";
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["last_report_date"] != "" || $res["last_report_date"] != " "){
				 
				    $custom_date = $res["last_report_date"];
					if ($custom_date == "1969-12-31") {
					    $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["last_report_date"=>""])->update('tbl_inventory');
					}else{
						$mp=explode("-",$custom_date);
						if(strlen($mp[1]) == 1){
							$mnth = sprintf("%02d", $mp[1]);
							$test = "work";
						}else{
							$mnth = $mp[1];
							$test = "";
						}
						
						if(strlen($mp[2]) == 1){
							$dts = sprintf("%02d", $mp[2]);
							$test = "work";
						}else{
							$dts = $mp[2];
							$test = "";
						}
						
						$date = $mp[0]."-".$mnth."-".$dts;
						if($test == "work"){
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["last_report_date"=>$date])->update('tbl_inventory');
						}	
					}
		     }
			 $i++;
	    }
		$this->date_script13();
	}
	function date_script13(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_inventory");
		$i = 1;
		$test = "";
		foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["audit_date2019"] != "" || $res["audit_date2019"] != " "){
				 
				 $custom_date = $res["audit_date2019"];
					if ($custom_date == "1969-12-31") {
					     $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["audit_date2019"=>""])->update('tbl_inventory');
					}else{
						$mp=explode("-",$custom_date);
						if(strlen($mp[1]) == 1){
							$mnth = sprintf("%02d", $mp[1]);
							$test = "work";
						}else{
							$mnth = $mp[1];
							$test = "";
						}
						
						if(strlen($mp[2]) == 1){
							$dts = sprintf("%02d", $mp[2]);
							$test = "work";
						}else{
							$dts = $mp[2];
							$test = "";
						}
						
						$date = $mp[0]."-".$mnth."-".$dts;
						if($test == "work"){
							$this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["audit_date2019"=>$date])->update('tbl_inventory');
						}	
					}
		     }
			 $i++;
	    }
	}
	function accounts_script(){
		$this->mongo_db->switch_db($this->database);
		$result = $this->mongo_db->get("tbl_locations");
		 foreach($result as $res){
			 $id = $res["_id"]->{'$id'};
			 if($res["accounts"] == "" || $res["accounts"] == " "){
				    $custom = array();
				    $this->mongo_db->where(["_id"=>new MongoDB\BSON\ObjectID($id)])->set(["accounts"=>$custom])->update('tbl_locations');
		     }
	    }
	}
}
