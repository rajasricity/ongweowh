<?php

defined("BASEPATH") OR exit("No direct script access allow");


class Admin extends CI_Model{
	
	public function __construct(){
		
		parent::__construct();
		
	
	}
	
	public function getTotalrecords($table,$item,$flocation,$tlocation){
		
//		$this->mongo_db->switch_db($this->database);
		
		if($flocation && $tlocation){
			/*$data = $this->mongo_db->aggregate($table,[
				
				['$match' => ['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active"]],
				['$match' => ['$or' => 
							  	[['tlcoation.status'=>"Active"],
							 	['tlcoation.locname'=>""],
								['tlcoation.locname'=>null]]
							 ]],
				['$count'=>"total"],
				
			]);*/
			
			$total = $this->mongo_db->where(['item.status'=>"Active","flocation.status"=>"Active",'tlcoation.status'=>"Active"])->count($table);
			
		}else{
			
			if($table == "tbl_inventory"){
				
				/*$data = $this->mongo_db->aggregate("$table",[
					['$match' => ['item.item_name'=>"$item"]],
					['$match' => ['item.status'=>"Active","locname.status"=>"Active"]],
					['$count'=>"total"],
				]);*/
				
				$total = $this->mongo_db->where(['item.item_name'=>"$item",'item.status'=>"Active","locname.status"=>"Active"])->count($table);

				
			}else{
			
				/*$data = $this->mongo_db->aggregate($table,[
					['$match' => ['item.status'=>"Active","tlocation.status"=>"Active"]],
					['$count'=>"total"],
				]);*/
								
				$total = $this->mongo_db->where(['item.status'=>"Active","tlocation.status"=>"Active"])->count($table);

			}
		}
	
		/*if($data[0]['total']){
			
			$total = $data[0]['total'];
			
		}else{
			
			$total = 0;
			
		}*/
		
//		return ($data);
		return ($total);
		
	}

	public function create($db,$collection,$indexes){
		
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

		$data["create"] = (string)$collection;

		try {
			$command = new MongoDB\Driver\Command($data);
			$cursor = $manager->executeCommand($db, $command);
			$response = $cursor->toArray()[0];
//			var_dump($response);

			$collstats = ["collstats" => $collection];
			$cursor = $manager->executeCommand($db, new MongoDB\Driver\Command($collstats));
			$response = $cursor->toArray()[0];
//			var_dump($response);
			
			$command = new MongoDB\Driver\Command([
				"createIndexes" => "$collection",
				"indexes"       => [[
				  "name" => "indexes",
				  "key"  => $indexes,
				  "ns"   => "$db.$collection",
			   ]],
			]);

			$result = $manager->executeCommand("$db", $command);
			
		} catch(MongoDB\Driver\Exception $e) {
			echo $e->getMessage(), "\n";
			exit;
		}

		
	}

	public function drop_database($db){
		
		$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
		
		try {
			
			$command = new MongoDB\Driver\Command(["dropDatabase"=>1]);
			$cursor = $manager->executeCommand($db, $command);
			$response = $cursor->toArray()[0];
			var_dump($response);
			
		} catch(MongoDB\Driver\Exception $e) {
			
			echo $e->getMessage(), "\n";
			exit;
			
		}
		
	}
	
	public function getPrefix($table){
		
		if($table == "tbl_locations"){
			
			$prefix = "LOC_";
			
		}elseif($table == "tbl_items"){
			
			$prefix = "IT_";
			
		}elseif($table == "tbl_touts"){
			
			$prefix = "TRIO_";
			
		}elseif($table == "tbl_issues"){
			
			$prefix = "SH_";
			
		}elseif($table == "tbl_returns"){
			
			$prefix = "PU_";
			
		}elseif($table == "tbl_adjustments"){
			
			$prefix = "ADJ_";
			
		}elseif($table == "tbl_inventory"){
			
			$prefix = "LINV_";
			
		}elseif($table == "tbl_apps"){
			
			$prefix = "CUST_";
			
		}else{
			
			$prefix = "";
			
		}
		
		return $prefix;
		
	}
		
	public function insert_id($table,$db="",$col=""){
		
		$prefix = $this->getPrefix($table);
		
		if($db){
			
			$this->mongo_db->switch_db($db);
			
		}
		
		if($col){
			
			$column = $col;
			
		}else{
			
			$column = "id";
			
		}
		
		$lid = $this->mongo_db->select(array("$column"))->order_by(array("_id"=>'desc'))->find_one($table)[0]["$column"];
		
		if($lid){
			
			$lastid = explode("_",$lid)[1];
			$id = $prefix.($lastid+1);
			
		}else{
			
			$id = $prefix.(1);
			
		}
		
		return $id;
		
	}



	public function insertoption($option_name,$option_value){
		
		$on=$this->db->get_where("fdm_va_options",array('option_name'=>$option_name));
		
		$os=$on->num_rows();
		
		if($os=='0'){
			
			$data=array("option_name"=>$option_name,
					   "option_value"=>$option_value);
			
			$oss=$this->db->insert("fdm_va_options",$data);
			
		}
		
		if($os='1'){
			
				$data=array("option_name"=>$option_name,
					   "option_value"=>$option_value);
			
			$oss=$this->db->set($data);
			
			$oss=$this->db->where("option_name",$option_name);
			
			$oss=$this->db->update("fdm_va_options");
			
		}		
		
	}
	
	
	public function get_option($option_name){
		
		$option=$this->db->get_where("fdm_va_options",array("option_name"=>$option_name));
		$o=$option->row();
		if($o){
		
		return $o->option_value;	
		}else{
			$oo='0';
		return $oo;
		}
	}

	public function generateOtp(){
		
		
		$i='1';
		
		do{
			
			$id=random_string("numeric",8);
			
			$chk=$this->db->get_where("fdm_va_otp",array('otp'=>$id))->num_rows();
			
			if($chk>0){
				$i='1';
				
			}else{
				$i='10';
			}
			
			
		}while($i<5);
		
		return $id;
	}

	public function get_admin(){

		$id = ($this->session->userdata("admin_email"));
		return $this->mongo_db->get_where("tbl_auths",array("email"=>$id))[0];

	}

	public function get_role(){
		return $this->db->get_where("fdm_va_roles")->result();
	}

	public function get_user($value=""){

		return $this->db->get_where("fdm_va_users",array("id"=>$this->session->userdata("user_id")))->row()->$value;
	}

	public function get_user_role(){
		$rr = $this->db->get_where("fdm_va_auths",array("id"=>$this->session->userdata("admin_id"),"role"=>2))->row();
        return $rr;
	}

	public function get_admin_role(){

		return $this->db->get_where("fdm_va_auths",array("id"=>$this->session->userdata("admin_id"),"role"=>1))->row();
	}
	
	public function send_email($subject,$email,$msg){
		
		$from = new SendGrid\Email("noreply", "ongpooling@ongweoweh.com");
		$to = new SendGrid\Email("Ongweoweh",$email);

		$content = new SendGrid\Content("text/html",$msg);
		$mail = new SendGrid\Mail($from, $subject, $to, $content);
		$sg = new \SendGrid('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');
		$response = $sg->client->mail()->send()->post($mail);
		
		return $response;
		
	}

	public function Mconfig(){
		try {
			$mng = new MongoDB\Driver\Manager("mongodb://localhost:27017");
			return $mng;
		  } catch (MongoDB\Driver\Exception\Exception $e) {
			$filename = basename(__FILE__);
			echo "The $filename script has experienced an error.\n";
			echo "Exception:", $e->getMessage(), "\n";
			return null;
		  }
	}

	public function getRows($mng="",$filter,$options,$table){		
		$mng = $this->Mconfig();
		$query = new MongoDB\Driver\Query($filter,$options);
		$rows = $mng->executeQuery($table, $query);
		return $rows;
	}

	public function getArray($mng="",$filter,$options,$table){
		
		$mng = $this->Mconfig();
		$query = new MongoDB\Driver\Query($filter,$options);
		$rows = $mng->executeQuery($table, $query);
		$return = [];
		foreach($rows as $row){
			array_push($return, $row);
		}
		return $return;
	}

	public function getRow($mng="",$filter,$options,$table){
		
		$mng = $this->Mconfig();
		$query = new MongoDB\Driver\Query($filter,$options);
		$rows = $mng->executeQuery($table, $query);
		return $rows->toArray()[0];
	}

	public function getCount($mng="",$table,$filter,$options){
		
		$mng = $this->Mconfig();
		$query = new MongoDB\Driver\Query($filter,$options);
		$rows = $mng->executeQuery($table, $query);
		return count($rows->toArray());
	}

	public function getReturn($mng="",$table,$filter,$options,$return){
		
		$mng = $this->Mconfig();
		$query = new MongoDB\Driver\Query($filter,$options);
		$rows = $mng->executeQuery($table, $query);
		return $rows->toArray()[0]->$return;
	}
	
	public function getSno($mng="",$table){
		
		$mng = $this->Mconfig();
		$filter = array();
		$options = array("sort"=>array("_id"=>-1),"limit"=>1);
		$query = new MongoDB\Driver\Query($filter,$options);
		$rows = $mng->executeQuery($table, $query);
		if(count($rows->toArray())>0){
			return $rows->toArray()[0]->Sno+1;	
		}else{
			return 1;
		}
		
	}
	
	public function mongoInsert($table,$data,$insertType=""){
		
		$mng = $this->Mconfig();
		
		$bulk = new MongoDB\Driver\BulkWrite;
		
		if($insertType == "bulk"){
		
			foreach($data as $row){

				$bulk->insert($row);

			}
			
		}else{
			
			$bulk->insert($data);

		}
		
		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$result = $mng->executeBulkWrite($table, $bulk, $writeConcern);
		
		return $result;
		
	}
	
	public function mongoDelete($table,$where,$options){
		
		$mng = $this->Mconfig();
		
		$bulk = new MongoDB\Driver\BulkWrite;
		
		$options = array('limit'=>true);
		$bulk->delete($where, $options);

		$writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
		$write = $mng->executeBulkWrite($table, $bulk, $writeConcern);
		
		return $write;
		
	}
	
	public function mongoUpdate($table,$where,$data,$options){
		
		$mng = $this->Mconfig();
		
		$bulk = new MongoDB\Driver\BulkWrite;
		$bulk->update(
			$where,
			['$set' => $data],
			$options
		);

		$result = $mng->executeBulkWrite($table, $bulk);
		return $result;
		
	}
	
	public function getAppdb(){
		
		$appId = $_SESSION['appid'];
		$database = $this->mongo_db->return_database_name();
		
		return "$database"."_"."$appId";
		
		
	}
	

}