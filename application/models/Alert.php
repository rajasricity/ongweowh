<?php

defined("BASEPATH") OR exit("No direct script access allow");


class Alert extends CI_Model{


	public function pnotify($title="Title",$msg="",$type="success"){
		
		$this->session->set_flashdata("msg",$msg);
		$this->session->set_flashdata("type",$type);
		$this->session->set_flashdata("title",$title);
		
		return true;
		
	}

	public function delAlert($title="Not Decided",$msg="Empty",$type="warning"){
		
		$this->session->set_flashdata("msg",$msg);
		$this->session->set_flashdata("type",$type);
		$this->session->set_flashdata("title",$title);
		
	}


}