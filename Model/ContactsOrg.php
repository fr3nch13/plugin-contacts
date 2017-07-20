<?php
App::uses('ContactsAppModel', 'Contacts.Model');

class ContactsOrg extends ContactsAppModel 
{
	public $useDbConfig = 'plugin_contacts';
	public $schemaName = 'cakephp_plugin_contacts';
	public $useTable = 'orgs';
	public $name = 'Org';
	public $displayField = 'shortname';
	public $virtualFields = array(
		'shortnamename' => 'CONCAT("(", Org.shortname, ") ", Org.name)',
	);
	public $order = array('Org.shortname' => 'asc', 'Org.name' => 'asc');
	
	public $_hasMany = array(
		'Division' => array(
			'className' => 'Division',
			'foreignKey' => 'org_id',
			'dependent' => false,
		),
	);
	
	public $_belongsTo = array(
		'OrgDirector' => array(
			'className' => 'AdAccount',
			'foreignKey' => 'director_id',
		),
		'OrgCrm' => array(
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
		'Org.shortname',
		'Org.name',
		'OrgDirector.username',
		'OrgDirector.name',
		'OrgDirector.email',
		'OrgCrm.username',
		'OrgCrm.name',
		'OrgCrm.email',
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
	
	public function idsForEmpties()
	{
		$ids = $this->find('list', array(
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		));
		
		foreach($ids as $i => $id)
		{
			if($count = $this->Division->idsForOrg($id))
			{
				unset($ids[$i]);
			}
		}
		return $ids;
	}
}
