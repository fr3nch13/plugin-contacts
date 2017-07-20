<?php

class DirPerson extends AppModel 
{
	public $useDbConfig = 'plugin_contacts_ned';
	public $useTable = 'USERPerson';
	public $displayField = 'username';
	
	public function testConnection($username = false)
	{
		try 
		{
			$this->connect();
		}
		catch (Exception $e)
		{
			$message = $e->getMessage();
			$attributes = $e->getAttributes();
			$code = $e->getCode();
			return array(
				'success' => false,
				'code' => $code,
				'message' => __('Message: %s - Database Error: %s', $message, $attributes['message'])
			);
		}
		// more testing here
		
		if($username)
		{
			if(!$userInfo = $this->getUserInfo($username))
			{
				return array(
					'success' => false,
					'code' => 10,
					'message' => $this->modelError,
				);
			}
		}
		
		return array(
			'success' => true,
			'code' => 0,
			'message' => __('All tests were successful.'),
		);
	}
	
	public function getUserInfo($username = false, $email = false, $userid = false)
	{
		$this->modelError = false;
		
		$userInfo = array();
		
		if($username)
		{
			if(!$result = $this->find('first', array(
				'conditions' => array(
					$this->alias.'.NIHSSOUSERNAME' => $username,
				),
			)))
			{
				$this->modelError = __('Unable to find the user in the database with username: %s', $username);
				return false;
			}
		}
		elseif($userid)
		{
			if(!$result = $this->find('first', array(
				'conditions' => array(
					$this->alias.'.UNIQUEIDENTIFIER' => $userid,
				),
			)))
			{
				$this->modelError = __('Unable to find the user in the database with userid: %s', $userid);
				return false;
			}
		}
		elseif($email)
		{
			if(!$result = $this->find('first', array(
				'conditions' => array(
					$this->alias.'.MAIL' => $email,
				),
			)))
			{
				$this->modelError = __('Unable to find the user in the database with email: %s', $email);
				return false;
			}
		}
		
		// try to match the values to the ad account model values
		$userInfo['adaccount'] = ($result[$this->alias]['NIHSSOUSERNAME']?$result[$this->alias]['NIHSSOUSERNAME']:false);
		$userInfo['userid'] = ($result[$this->alias]['UNIQUEIDENTIFIER']?$result[$this->alias]['UNIQUEIDENTIFIER']:false);
		$userInfo['sac'] = ($result[$this->alias]['NIHSAC']?$result[$this->alias]['NIHSAC']:false);
		$userInfo['email'] = ($result[$this->alias]['MAIL']?$result[$this->alias]['MAIL']:false);
		// try to build the name
		$firstname = ($result[$this->alias]['NIHCOMMONGIVENNAME']?$result[$this->alias]['NIHCOMMONGIVENNAME']:($result[$this->alias]['GIVENNAME']?$result[$this->alias]['GIVENNAME']:false));
		$lastname = ($result[$this->alias]['NIHCOMMONSN']?$result[$this->alias]['NIHCOMMONSN']:($result[$this->alias]['SN']?$result[$this->alias]['SN']:false));
		$firstname = ucfirst(strtolower($firstname));
		$lastname = ucfirst(strtolower($lastname));
		$userInfo['name'] = __('%s %s', $firstname, $lastname);
		
		$phone = ($result[$this->alias]['MOBILETELEPHONENUM']?$result[$this->alias]['MOBILETELEPHONENUM']:($result[$this->alias]['TELEPHONENUMBER']?$result[$this->alias]['TELEPHONENUMBER']:false));
		
		if($phone)
		{
			$phone = preg_replace('/^\+\d+\s+/', '', $phone);
			$phone = preg_replace('/\s+/', '-', $phone);
		}
		$userInfo['phone_number'] = $userInfo['phone'] = $phone;
		
		$userInfo['address'] = ($result[$this->alias]['NIHPHYSICALADDRESS']?$result[$this->alias]['NIHPHYSICALADDRESS']:($result[$this->alias]['POSTALADDRESS']?$result[$this->alias]['POSTALADDRESS']:false));
		
		return array_merge($result[$this->alias], $userInfo);
	}
}