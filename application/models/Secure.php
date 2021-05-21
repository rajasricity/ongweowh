<?php
defined("BASEPATH") OR exit("NO direct script access allow");


class Secure extends CI_Model{
	
	
	
	public function __construct(){
		
		
		
		parent::__construct();
		
	
		
	}
	
	public function encrypt($data){
		
			$key="bjvd!@#$%^&*13248*/-/*vjvdf";
			$hmac_key = "kbdkh2365765243";

	
		$e = $this->encryption->initialize(
        array(
                'cipher' => 'blowfish',
                'mode' => 'cbc',
                'key' => $key,
                'hmac_digest' => 'sha256',
				'hmac_key' => $hmac_key
        )
		);
		
		$s=$this->encryption->encrypt($data);	
		
		
		if($s){
		
			return $s;		
			
		}else{
			
			return false;		
		
		}
	}
	
	public function encryptWithKey($data,$key){
		
		$this->encryption->initialize(
        array(
                'cipher' => 'aes-256',
                'mode' => 'ctr',
                'key' => $key
        )
		);
		
		$s=$this->encryption->encrypt($data);	
		
		
		if($s){
		
		return $s;		
			
		}else{
			
		return false;		
		
		}
	}
	
	public function decrypt($data){
		
			$key="bjvd!@#$%^&*13248*/-/*vjvdf";
			$hmac_key = "kbdkh2365765243";

		$d =$this->encryption->initialize(
        array(
                'cipher' => 'blowfish',
                'mode' => 'cdc',
                'key' => $key,
                'hmac_digest' => 'sha256',
				'hmac_key' => $hmac_key
        )
);
		$s=$this->encryption->decrypt($data);	
		if($s){
		
			return $s;		
			
		}else{
			
			return false;		
		
		}
	}
	
	public function agent(){
		if ($this->agent->is_browser())
			{
			        $data['agent'] = $this->agent->browser().' '.$this->agent->version();
			}
			elseif ($this->agent->is_robot())
			{
			       $data['agent'] = $this->agent->robot();
			}
			elseif ($this->agent->is_mobile())
			{
			       $data['agent'] = $this->agent->mobile();
			}
			else
			{
			       $data['agent'] = 'Unidentified User Agent';
			}

			$data['platform']= $this->agent->platform(); 
			$data['ip_address'] = $_SERVER['SERVER_ADDR'];
			
			return $data;
	}
	
	
	
}