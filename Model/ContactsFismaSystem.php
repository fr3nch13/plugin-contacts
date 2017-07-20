<?php
App::uses('ContactsAppModel', 'Contacts.Model');

class ContactsFismaSystem extends ContactsAppModel 
{
	public $useDbConfig = 'plugin_contacts';
	public $schemaName = 'cakephp_plugin_contacts';
	public $useTable = 'fisma_systems';
	public $displayField = 'name';
	
	public $validate = [
		'owner_contact_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
	];
	
	public $_hasMany = [
		'ContactsFismaInventory' => [
			'className' => 'ContactsFismaInventory',
			'foreignKey' => 'fisma_system_id',
			'dependent' => true,
		],
	];
	
	public $_belongsTo = [
		'OwnerContact' => [
			'className' => 'AdAccount',
			'foreignKey' => 'owner_contact_id',
		],
	];
	
	public $actsAs = [
		'Contacts.ContactsFisma',
		'Utilities.Family',
	];
	
	// define the fields that can be searched
	public $searchFields = [
		'ContactsFismaSystem.name',
		'ContactsFismaSystem.fullname',
	];
	
	public $autocompleteMap = [
		'fields' => ['ContactsFismaSystem.name'],
		'value' => 'name', // the fields to use as the display value
		'data' => 'name', // the fields to use as the actual value
		'include_data' => true, // include the data result in the display value
		'group' => 'OwnerContact.name',
		'recursive' => 0,
	];
	
	public function updateRecord($record = [], $checkDiff = false)
	{
		return $this->Fisma_updateRecord($record, $checkDiff);
	}
	
	public function idsForOrg($org_id = false)
	{
		if(!$divisionIds = $this->OwnerContact->Sac->Branch->Division->idsForOrg($org_id)) { return []; }
		
		return $this->idsForDivision($divisionIds);
	}
	
	public function idsForDivision($division_id = false)
	{
		if(!$branchIds = $this->OwnerContact->Sac->Branch->idsForDivision($division_id)) { return []; }
		
		return $this->idsForBranch($branchIds);
	}
	
	public function idsForBranch($branch_id = false)
	{
		if(!$sacIds = $this->OwnerContact->Sac->idsForBranch($branch_id)) { return []; }
		
		return $this->idsForSac($sacIds);
	}
	
	public function idsForSac($sac_id = false)
	{
		if(!$ownerContactIds = $this->OwnerContact->idsForSac($sac_id)) { return []; }
		
		return $this->idsForOwnerContact($ownerContactIds);
	}
	
	public function idsForOwnerContact($owner_contact_id = false)
	{
		if(!$fismaSystemIds = $this->find('list', [
			'conditions' => [
				$this->alias.'.owner_contact_id' => $owner_contact_id,
			],
			'fields' => [$this->alias.'.id', $this->alias.'.id'],
		])) { return []; }
		
		return $fismaSystemIds;
	}
	
	public function getRelatedIpAddresses($fismaSystemId = false)
	{
		if(is_array($fismaSystemId) and count($fismaSystemId) == 1)
			$fismaSystemId = array_pop($fismaSystemId);
		
		$results = $this->FismaInventory->getCachedCounts('list', [
			'conditions' => [
				'FismaInventory.fisma_system_id' => $fismaSystemId,
				'FismaInventory.ip_address !=' => '',
				'FismaInventory.ip_address NOT IN' => ['TBD', 'NA', 'N/A'],
			],
			'fields' => ['FismaInventory.ip_address', 'FismaInventory.ip_address'],
		]);
		if(!$results)
			$results = [];
		return $results;
	}
	
	public function getRelatedHostNames($fismaSystemId = false)
	{
		if(is_array($fismaSystemId) and count($fismaSystemId) == 1)
			$fismaSystemId = array_pop($fismaSystemId);
		
		$results = $this->FismaInventory->getCachedCounts('list', [
			'conditions' => [
				'FismaInventory.fisma_system_id' => $fismaSystemId,
				'FismaInventory.dns_name !=' => '',
				'FismaInventory.dns_name NOT IN' => ['TBD', 'NA', 'N/A'],
			],
			'fields' => ['FismaInventory.dns_name', 'FismaInventory.dns_name'],
		]);
		if(!$results)
			$results = [];
		return $results;
	}
	
	public function getRelatedMacAddresses($fismaSystemId = false)
	{
		if(is_array($fismaSystemId) and count($fismaSystemId) == 1)
			$fismaSystemId = array_pop($fismaSystemId);
		
		$results = $this->FismaInventory->getCachedCounts('list', [
			'conditions' => [
				'FismaInventory.fisma_system_id' => $fismaSystemId,
				'FismaInventory.mac_address !=' => '',
				'FismaInventory.mac_address NOT IN' => ['TBD', 'NA', 'N/A'],
			],
			'fields' => ['FismaInventory.mac_address', 'FismaInventory.mac_address'],
		]);
		if(!$results)
			$results = [];
		return $results;
	}
	
	public function getRelatedAssetTags($fismaSystemId = false)
	{
		if(is_array($fismaSystemId) and count($fismaSystemId) == 1)
			$fismaSystemId = array_pop($fismaSystemId);
		
		$results = $this->FismaInventory->getCachedCounts('list', [
			'conditions' => [
				'FismaInventory.fisma_system_id' => $fismaSystemId,
				'FismaInventory.asset_tag !=' => '',
				'FismaInventory.asset_tag NOT IN' => ['TBD', 'NA', 'N/A'],
			],
			'fields' => ['FismaInventory.asset_tag', 'FismaInventory.asset_tag'],
		]);
		if(!$results)
			$results = [];
		return $results;
	}
	
	public function getPiiCount($fismaSystemId = false)
	{
		if($piiCount = $this->field('pii_count', [$this->alias.'.'.$this->primaryKey => $fismaSystemId]))
		{
			return $piiCount;
		}
		return 0;
	}
	
	public function getInventoryCount($fismaSystemId = false)
	{
		if($count = $this->FismaInventory->getCachedCounts('count', ['conditions' => ['FismaInventory.fisma_system_id' => $fismaSystemId]]))
		{
			return $count;
		}
		return 0;
	}
	
	public function _buildIndexConditions($contact_ids = [], $contact_type = false)
	{
		$conditions = [];
		$conditions[$this->alias.'.owner_contact_id'] = $contact_ids;
		return $conditions;
	}
}
