<?php
	
class snp_sendgrid_class
{
	private $username;
	private $password;
	private $url = 'https://api.sendgrid.com/api/newsletter';

	
	public function __construct($username, $password)
	{
		$this -> username = $username;
		$this -> password = $password;
	}
	public function getLists()
	{
		$request = $this -> url.'/lists/get.json';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$request); 
		curl_setopt($ch,CURLOPT_POST,true);
		$param = http_build_query(array('api_key' => $this -> password, 'api_user' => $this->username));
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch,CURLOPT_POSTFIELDS, $param); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	public function addSubscriber($list, $data)
	{
		$request = $this-> url.'/lists/email/add.json';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$request); 
		curl_setopt($ch,CURLOPT_POST,true);
		$data = json_encode($data);
		$param = http_build_query(array('api_key' => $this -> password, 'api_user' => $this->username, 'list'=> $list, 'data' =>$data));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch,CURLOPT_POSTFIELDS, $param); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	
	
	
	
	
	
	/*
	
	public function get_lists($list=NULL)
	{
		if (is_null($list))
		{
			return $this->_send('newsletter/lists/get.' . $this->api_format);
		}
		return $this->_send('newsletter/lists/get.' . $this->api_format, array('list' => $list));
	}


	
	
	*/
	
}




?>