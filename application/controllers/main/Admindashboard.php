<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admindashboard extends CI_Controller {
	
	public function __construct(){
		
		parent::__construct();
		if(!$this->session->userdata("admin_email")){
			
			redirect("login");
			
		}
		
		$mdb = mongodb;
		$this->database = $mdb."_".$this->session->userdata('appId');
		
	}

	public function index(){
		
		$data['database'] = $this->database;
		$data['mongodb'] = mongodb;
		$this->load->view('main/dashboard',$data);
		
	}
	
	public function myLocations(){
		
		$data['database'] = $this->database;
		$this->load->view('main/myLocations',$data);
		
	}
	
	public function logout(){
		
		$this->session->sess_destroy();
		redirect("login");
		
	}
	
}
