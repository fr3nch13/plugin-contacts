<?php
App::uses('ContactsAppModel', 'Contacts.Model');

class ContactsBranch extends ContactsAppModel 
{
	public $useDbConfig = 'plugin_contacts';
	public $schemaName = 'cakephp_plugin_contacts';
	public $useTable = 'branches';
	public $name = 'Branch';
	public $displayField = 'shortname';
	public $virtualFields = array(
		'shortnamename' => 'CONCAT("(", Branch.shortname, ") ", Branch.name)',
	);
	public $order = array('Branch.shortname' => 'asc', 'Branch.name' => 'asc');
	
	public $_hasMany = array(
		'Sac' => array(
			'className' => 'Sac',
			'foreignKey' => 'branch_id',
			'dependent' => false,
		),
	);
	
	public $_belongsTo = array(
		'Division' => array(
			'className' => 'Division',
			'foreignKey' => 'division_id',
		),
		'BranchDirector' => array(
			'className' => 'AdAccount',
			'foreignKey' => 'director_id',
		),
		'BranchCrm' => array(
			'className' => 'AdAccount',
			'foreignKey' => 'crm_id',
		),
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active');
	
	public $autocompleteMap = array(
		'fields' => array('shortname', 'name'),
		'value' => 'shortnamename', // the fields to use as the display value
		'data' => 'shortname', // the fields to use as the actual value
		'include_data' => true, // include the data result in the display value
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Branch.shortname',
		'Branch.name',
		'Division.shortname',
		'Division.name',
		'BranchDirector.username',
		'BranchDirector.name',
		'BranchDirector.email',
		'BranchCrm.username',
		'BranchCrm.name',
		'BranchCrm.email',
	);
	
	public function checkAdd($shortname = false, $extra = array())
	{
		if(!$shortname) return false;
		
		$shortname = trim($shortname);
		if(!$shortname) return false;
		
		$shortname = strtoupper($shortname);
		
		if($id = $this->field($this->primaryKey, array($this->alias.'.shortname' => $shortname)))
		{
			return $id;
		}
		
		if(!isset($extra['created']))
			$extra['created'] = date('Y-m-d H:i:s');
		
		// not an existing one, create it
		$this->create();
		$this->data = array_merge(array('shortname' => $shortname), $extra);
		if($this->save($this->data))
		{
			return $this->id;
		}
		return false;
	}
	
	public function typeFormList()
	{
		return $this->find('list', array(
			'fields' => array(
				$this->alias.'.id', 
				$this->alias.'.shortnamename',
			),
			'order' => array($this->alias.'.shortname' => 'ASC'),
		));
	}
	
	public function idsForOrg($org_id = false)
	{
		if(!$divisionIds = $this->Division->idsForOrg($org_id)) { return array(); }
		
		return $this->idsForDivision($divisionIds);
	}
	
	public function idsForDivision($division_id = false)
	{
		if(!$branchIds = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.division_id' => $division_id,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		))) { return array(); }
		
		return $branchIds;
	}
	
	public function idsForEmpties()
	{
		$ids = $this->find('list', array(
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		));
		
		foreach($ids as $i => $id)
		{
			if($count = $this->Sac->idsForBranch($id))
			{
				unset($ids[$i]);
			}
		}
		return $ids;
	}
}
