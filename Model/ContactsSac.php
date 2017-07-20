<?php
App::uses('ContactsAppModel', 'Contacts.Model');

class ContactsSac extends ContactsAppModel 
{
	public $useDbConfig = 'plugin_contacts';
	public $schemaName = 'cakephp_plugin_contacts';
	public $useTable = 'sacs';
	public $name = 'Sac';
	public $displayField = 'shortname';
	public $virtualFields = array(
		'shortnamename' => 'CONCAT("(", Sac.shortname, ") ", Sac.name)',
	);
	public $order = array('Sac.shortname' => 'asc', 'Sac.name' => 'asc');
	
	public $_hasMany = array(
		'AdAccount' => array(
			'className' => 'AdAccount',
			'foreignKey' => 'sac_id',
			'dependent' => false,
		),
		'AssocAccount' => array(
			'className' => 'AssocAccount',
			'foreignKey' => 'sac_id',
			'dependent' => false,
		),
	);
	
	public $_belongsTo = array(
		'Branch' => array(
			'className' => 'Branch',
			'foreignKey' => 'branch_id',
		),
		'SacDirector' => array(
			'className' => 'AdAccount',
			'foreignKey' => 'director_id',
		),
		'SacCrm' => array(
			'className' => 'AdAccount',
			'foreignKey' => 'crm_id',
		),
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active');
	
	public $autocompleteMap = array(
		'fields' => array('shortname', 'name'),
		'value' => 'shortnamename', // the fields to use as the display value
		'data' => 'shortnamename', // the fields to use as the actual value
		'include_data' => true, // include the data result in the display value
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Sac.shortname',
		'Sac.name',
		'Branch.name',
		'Branch.shortname',
		'SacDirector.username',
		'SacDirector.name',
		'SacDirector.email',
		'SacCrm.username',
		'SacCrm.name',
		'SacCrm.email',
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
	
	public function idsForOrg($org_id = false)
	{
		if(!$divisionIds = $this->Branch->Division->idsForOrg($org_id)) { return array(); }
		
		return $this->idsForDivision($divisionIds);
	}
	
	public function idsForDivision($division_id = false)
	{
		if(!$branchIds = $this->Branch->idsForDivision($division_id)) { return array(); }
		
		return $this->idsForBranch($branchIds);
	}
	
	public function idsForBranch($branch_id = false)
	{
		if(!$sacIds = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.branch_id' => $branch_id,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		))) { return array(); }
		
		return $sacIds;
	}
	
	public function idsForEmpties()
	{
		$ids = $this->find('list', array(
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		));
		
		foreach($ids as $i => $id)
		{
			if($count = $this->AdAccount->idsForSac($id))
			{
				unset($ids[$i]);
			}
		}
		return $ids;
	}
	
	public function typeFormList($criteria = array())
	{
		$defaultCriteria = array(
			'order' => array('Sac.shortname' => 'ASC'),
			'contain'  => array('SacDirector', 'Branch', 'Branch.Division', 'Branch.Division.Org'),
		);
		$criteria = Hash::merge($defaultCriteria, $criteria);
		
		$sacs = $this->find('all', $criteria);
		
		$_sacs = array();
		foreach($sacs as $sac)
		{
			$id = $sac[$this->alias]['id'];
			$_sacs[$id] = __('%s - %s', $sac[$this->alias]['shortname'], $this->Contacts_makePath($sac));
		}
		$sacs = $_sacs;
		unset($_sacs);
		
		return $sacs;
	}
	
	public function gridEdit($data = array())
	{
	// mainly used with the ContactsSacController::directors().
	// however this is also a wrapper for anywhere gridediting is happening on Sacs
		$results = array();
		$fixMessage = false;
		foreach($data as $modelAlias => $modelData)
		{
			if($this->alias == $modelAlias)
			{
				$result = $this->Common_gridEdit(array($modelAlias => $modelData));
				if(!$result)
					break;
				$results = array_merge($results, $result);
			}
			elseif($modelAlias == 'Branch')
			{
				$result = $this->Branch->Common_gridEdit(array($modelAlias => $modelData));
				if(!$result)
					break;
				$results = array_merge($results, $result);
				$fixMessage = true;
			}
			elseif($modelAlias == 'Division')
			{
				$result = $this->Branch->Division->Common_gridEdit(array($modelAlias => $modelData));
				if(!$result)
					break;
				$results = array_merge($results, $result);
				$fixMessage = true;
			}
			elseif($modelAlias == 'Org')
			{
				$result = $this->Branch->Division->Org->Common_gridEdit(array($modelAlias => $modelData));
				if(!$result)
					break;
				$results = array_merge($results, $result);
				$fixMessage = true;
			}
		}
		
		if($results and $fixMessage)
			$results['message'] = __('These were updated successfully');
		return $results;
	}
		
}
