<?php
App::uses('ContactsAppModel', 'Contacts.Model');

class ContactsAssocAccount extends ContactsAppModel 
{
	public $useDbConfig = 'plugin_contacts';
	public $schemaName = 'cakephp_plugin_contacts';
	public $useTable = 'assoc_accounts';
	public $name = 'AssocAccount';
	public $displayField = 'username';
	
	public $validate = array(
		'ad_account_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $_belongsTo = array(
		'AdAccount' => array(
			'className' => 'AdAccount',
			'foreignKey' => 'ad_account_id',
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'AssocAccount.username',
		'AssocAccount.name',
		'AssocAccount.email',
		'AdAccount.username',
		'AdAccount.name',
		'AdAccount.email',
	);
	
	public $autocompleteMap = array(
		'fields' => array('AssocAccount.username'),
		'value' => 'username', // the fields to use as the display value
		'data' => 'username', // the fields to use as the actual value
		'include_data' => true, // include the data result in the display value
		'group' => 'AdAccount.username',
		'recursive' => 0,
	);
    
    // fields that we use to find duplicates
    public $duplicateFields = array(
    	'username', 
    );
	
	public function checkAdd($username = false, $ad_account_id = false, $extra = array())
	{
		if(!$username) return false;
		
		$username = trim($username);
		if(!$username) return false;
		
		$username = strtolower($username);
		
		if($id = $this->field($this->primaryKey, array($this->alias.'.username' => $username, $this->alias.'.ad_account_id' => $ad_account_id)))
		{
			return $id;
		}
		
		if(!isset($extra['created']))
			$extra['created'] = date('Y-m-d H:i:s');
		
		// not an existing one, create it
		$this->create();
		$this->data = array_merge(array('username' => $username, 'ad_account_id' => $ad_account_id), $extra);
		if($this->save($this->data))
		{
			return $this->id;
		}
		return false;
	}
	
	public function getUsername($id = false)
	{
		if(!$id) return false;
		
		
		if($username = $this->field('username', array($this->alias.'.'.$this->primaryKey => $id)))
		{
			return $username;
		}
		return false;
	}
	
	public function idsForOrg($org_id = false)
	{
		if(!$divisionIds = $this->AdAccount->Sac->Branch->Division->idsForOrg($org_id)) { return array(); }
		
		return $this->idsForDivision($divisionIds);
	}
	
	public function idsForDivision($division_id = false)
	{
		if(!$branchIds = $this->AdAccount->Sac->Branch->idsForDivision($division_id)) { return array(); }
		
		return $this->idsForBranch($branchIds);
	}
	
	public function idsForBranch($branch_id = false)
	{
		if(!$sacIds = $this->AdAccount->Sac->idsForBranch($branch_id)) { return array(); }
		
		return $this->idsForSac($sacIds);
	}
	
	public function idsForSac($sac_id = false)
	{
		if(!$adAccountIds = $this->AdAccount->idsForSac($sac_id)) { return array(); }
		
		return $this->idsForAdAccount($adAccountIds);
	}
	
	public function idsForAdAccount($ad_account_id = false)
	{
		if(!$assocAccountIds = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.ad_account_id' => $ad_account_id,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		))) { return array(); }
		
		return $assocAccountIds;
	}
}
