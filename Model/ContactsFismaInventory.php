<?php
App::uses('ContactsAppModel', 'Contacts.Model');

class ContactsFismaInventory extends ContactsAppModel 
{
	public $useDbConfig = 'plugin_contacts';
	public $schemaName = 'cakephp_plugin_contacts';
	public $useTable = 'fisma_inventories';
	public $displayField = 'name';
	
	public $_belongsTo = array(
		'ContactsFismaSystem' => array(
			'className' => 'ContactsFismaSystem',
			'foreignKey' => 'fisma_system_id',
		),
	);
	
	public $actsAs = [
		'Contacts.ContactsFisma',
	];
	
	// define the fields that can be searched
	public $searchFields = array(
		'ContactsFismaInventory.nat_ip_address',
		'ContactsFismaInventory.ip_address',
		'ContactsFismaInventory.mac_address',
		'ContactsFismaInventory.dns_name',
		'ContactsFismaInventory.asset_tag',
	);
	
	public function updateRecord($record = [], $checkDiff = false)
	{
		return $this->Fisma_updateRecord($record, $checkDiff);
	}
	
	public function idsForOrg($org_id = false)
	{
		if(!$divisionIds = $this->ContactsFismaSystem->OwnerContact->Sac->Branch->Division->idsForOrg($org_id)) { return []; }
		
		return $this->idsForDivision($divisionIds);
	}
	
	public function idsForDivision($division_id = false)
	{
		if(!$branchIds = $this->ContactsFismaSystem->OwnerContact->Sac->Branch->idsForDivision($division_id)) { return []; }
		
		return $this->idsForBranch($branchIds);
	}
	
	public function idsForBranch($branch_id = false)
	{
		if(!$sacIds = $this->ContactsFismaSystem->OwnerContact->Sac->idsForBranch($branch_id)) { return []; }
		
		return $this->idsForSac($sacIds);
	}
	
	public function idsForSac($sac_id = false)
	{
		if(!$ownerContactIds = $this->ContactsFismaSystem->OwnerContact->idsForSac($sac_id)) { return []; }
		
		return $this->idsForOwnerContact($ownerContactIds);
	}
	
	public function idsForOwnerContact($owner_contact_id = false)
	{
		if(!$fismaSystemIds = $this->ContactsFismaSystem->idsForOwnerContact($owner_contact_id)) { return []; }
		
		return $this->idsForFismaSystem($fismaSystemIds);
	}
	
	public function idsForFismaSystem($fisma_system_id = false)
	{
		if(!$fismaInventoryIds = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.fisma_system_id' => $fisma_system_id,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		))) { return []; }
		
		return $fismaInventoryIds;
	}
}
