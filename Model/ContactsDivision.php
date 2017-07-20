<?php
App::uses('ContactsAppModel', 'Contacts.Model');

class ContactsDivision extends ContactsAppModel 
{
	public $useDbConfig = 'plugin_contacts';
	public $schemaName = 'cakephp_plugin_contacts';
	public $useTable = 'divisions';
	public $name = 'Division';
	public $displayField = 'shortname';
	public $virtualFields = array(
		'shortnamename' => 'CONCAT("(", Division.shortname, ") ", Division.name)',
	);
	public $order = array('Division.shortname' => 'asc', 'Division.name' => 'asc');
	
	public $_hasMany = array(
		'Branch' => array(
			'className' => 'Branch',
			'foreignKey' => 'division_id',
			'dependent' => false,
		),
	);
	
	public $_belongsTo = array(
		'Org' => array(
			'className' => 'Org',
			'foreignKey' => 'org_id',
		),
		'DivisionDirector' => array(
			'className' => 'AdAccount',
			'foreignKey' => 'director_id',
		),
		'DivisionCrm' => array(
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
		'Division.shortname',
		'Division.name',
		'Org.shortname',
		'Org.name',
		'DivisionDirector.username',
		'DivisionDirector.name',
		'DivisionDirector.email',
		'DivisionCrm.username',
		'DivisionCrm.name',
		'DivisionCrm.email',
	);
    public $duplicateFields = array(
    	'shortname',
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
		if(!$divisionIds = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.org_id' => $org_id,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		))) { return array(); }
		
		return $divisionIds;
	}
	
	public function idsForEmpties()
	{
		$ids = $this->find('list', array(
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		));
		
		foreach($ids as $i => $id)
		{
			if($count = $this->Branch->idsForDivision($id))
			{
				unset($ids[$i]);
			}
		}
		return $ids;
	}
	
	public function cron_remap()
	{
		Configure::write('debug', 1);
		
		$orgDefaults = $this->Org->makeDefaultFields();
		$divisionDefaults = $this->makeDefaultFields();
		$branchDefaults = $this->Branch->makeDefaultFields();
		$sacDefaults = $this->Branch->Sac->makeDefaultFields();
		
		$orgCache = array();
		$divisionCache = array();
		$branchCache = array();
		$sacCache = array();
		$adAccountCache = array();
		
		
		$divisions = $this->find('all', array(
			'order' => array('Division.shortname' => 'ASC'),
		));
		
		foreach($divisions as $i => $division)
		{	
			/////////// try to find the director
			$director_id = 0;
			if($division['Division']['email'])
			{
				// try to figure out their username
				$directorConditions = array('OR' => array(
					'DivisionDirector.email' => $division['Division']['email'],
				));
				$adAccount_username = false;
				if(!preg_match('/@example.com$/', $division['Division']['email']))
				{
					$adAccount_username = explode('@', $division['Division']['email']);
					$adAccount_username = array_shift($adAccount_username);
					$adAccount_username = strtolower($adAccount_username);
					$directorConditions['OR']['DivisionDirector.username'] = $adAccount_username;
				}
				else
				{
					$adAccount_username = explode('@', $division['Division']['email']);
					$adAccount_username = array_shift($adAccount_username);
				}
				
				if(isset($adAccountCache[$adAccount_username]) and $adAccountCache[$adAccount_username])
				{
					$director = $adAccountCache[$adAccount_username];
				}
				else
				{
					$director = $this->DivisionDirector->find('first', array(
						'conditions' => $directorConditions,
						'recursive' => -1,
						'order' => array('DivisionDirector.email' => 'ASC'),
					));
					if($director)
						$adAccountCache[$adAccount_username] = $director;
				}
				
				$director_id = 0;
				if($director)
				{
					$director_id = $director['DivisionDirector']['id'];
				}
				else
				{
					$directorData = array(
						'username' => $adAccount_username,
						'email' => $division['Division']['email'],
						'name' => $division['Division']['director'],
						'guessed' => true,
					);
					
					if($director_id = $this->DivisionDirector->checkAdd($adAccount_username, $directorData))
					{
						$this->DivisionDirector->recursive = -1;
						$director = $this->DivisionDirector->read(null, $director_id);
						$adAccountCache[$adAccount_username] = $director;
					}
				}
			}
			
			/////////// try to find/figure out the org
			$org_id = 0;
			if($division['Division']['org'])
			{
				$orgData = $orgDefaults;
				$orgData['director_id'] = $director_id;
				$orgData['name'] = strtoupper($division['Division']['org']);
				$orgData['shortname'] = strtoupper($division['Division']['org']);
				$orgData['active'] = true;
				$orgData['created'] = $division['Division']['created'];
				$orgData['modified'] = $division['Division']['modified'];
				
				if(isset($orgCache[$orgData['shortname']]) and $orgCache[$orgData['shortname']])
				{
					$org = $orgCache[$orgData['shortname']];
				}
				else
				{
					$org = $this->Org->find('first', array(
						'recursive' => -1,
						'order' => array('Org.shortname' => 'ASC'),
						'conditions' => array(
							'Org.shortname' => $orgData['shortname'],
						),
					));
					
					if($org)
					{
						// update the existing record
						$this->Org->id = $org['Org']['id'];
						$this->Org->data = $orgData;
						$this->Org->save($this->Org->data);
						
						$this->Org->recursive = -1;
						$org = $this->Org->read(null, $org['Org']['id']);
						$orgCache[$orgData['shortname']] = $org;
					}
				}
				
				if($org)
				{
					$org_id = $org['Org']['id'];
				}
				else
				{
					if($org_id = $this->Org->checkAdd($orgData['shortname'], $orgData))
					{
						$this->Org->recursive = -1;
						$org = $this->Org->read(null, $org_id);
						$orgCache[$orgData['shortname']] = $org;
					}
				}
			}
			
			/////////// try to find/figure out the division
			$division_id = 0;
			$divisionData = $division['Division'];
			
			$shortName = $division['Division']['shortname'];
			$name = $division['Division']['name'];
			$name = $this->fixNname($name);
			
			$divisionData['name'] = $name;
			$divisionData['active'] = true;
			$divisionData['chosen'] = true;
			$divisionData['shortname'] = ($shortName?$shortName:$this->makeShortname($name));
			$divisionData['director_id'] = $director_id;
			$divisionData['org_id'] = $org_id;
			
			$chosenDivision = false;
			if(isset($divisionCache[$divisionData['shortname']]) and $divisionCache[$divisionData['shortname']])
			{
				$chosenDivision = $divisionCache[$divisionData['shortname']];
			}
			else
			{
				$chosenDivision = $this->find('first', array(
					'recursive' => -1,
					'order' => array('Division.shortname' => 'ASC'),
					'conditions' => array(
						'Division.shortname' => $divisionData['shortname'],
					),
				));
					
				if($chosenDivision)
				{
					// update the existing record
					$this->id = $chosenDivision['Division']['id'];
					$this->data = $divisionData;
					$this->save($this->data);
					
					$this->recursive = -1;
					$chosenDivision = $this->read(null, $chosenDivision['Division']['id']);
					$divisionCache[$divisionData['shortname']] = $chosenDivision;
				}
			}
			
			if($chosenDivision)
			{
				$division_id = $chosenDivision['Division']['id'];
			}
			else
			{	
				if($division_id = $this->checkAdd($divisionData['shortname'], $divisionData))
				{
					$this->recursive = -1;
					$chosenDivision = $this->read(null, $division_id);
					$divisionCache[$divisionData['shortname']] = $chosenDivision;
				}
			}
						
			/////////// try to find/figure out the branch
			$branch_id = 0;
			$branchData = $branchDefaults;
			$branchData['name'] = $name;
			$branchData['active'] = true;
			$branchData['shortname'] = $this->makeShortname($name);
			$branchData['old_director_name'] = $division['Division']['director'];
			$branchData['old_director_email'] = $division['Division']['email'];
			$branchData['director_id'] = $director_id;
			$branchData['division_id'] = $division_id;
			$branchData['created'] = $division['Division']['created'];
			$branchData['modified'] = $division['Division']['modified'];
			
			// try to map some of the fields
			foreach($branchData as $branchField => $branchValue)
			{
				if(isset($chosenDivision['Division'][$branchField]) and !$branchData[$branchField])
					$branchData[$branchField] = $chosenDivision['Division'][$branchField];
			}
			
			unset($branchData['id']);
			
			if(isset($branchCache[$branchData['shortname']]) and $branchCache[$branchData['shortname']])
			{
				$branch = $branchCache[$branchData['shortname']];
			}
			else
			{
				$branch = $this->Branch->find('first', array(
					'recursive' => -1,
					'order' => array('Branch.shortname' => 'ASC'),
					'conditions' => array(
						'Branch.shortname' => $branchData['shortname'],
					),
				));
				
				if($branch)
				{
					// Update the existing record
					$this->Branch->id = $branch['Branch']['id'];
					$this->Branch->data = $branchData;
					$this->Branch->save($this->Branch->data);
					
					$this->Branch->recursive = -1;
					$branch = $this->Branch->read(null, $branch['Branch']['id']);
					$branchCache[$branchData['shortname']] = $branch;
				}
			}
			
			if($branch)
			{
				$branch_id = $branch['Branch']['id'];
			}
			else
			{	
				if($branch_id = $this->Branch->checkAdd($branchData['shortname'], $branchData))
				{
					$this->Branch->recursive = -1;
					$branch = $this->Branch->read(null, $branch_id);
					$branchCache[$branchData['shortname']] = $branch;
				}
			}
			
			/////////// try to find/figure out the sac
			$sac_id = 0;
			$sacData = $sacDefaults;
			
			$sacName = $division['Division']['sac'];
			if(!$sacName)
				$sacName = 'TMP_'. ($shortName?$shortName:$this->makeShortname($name));
			
			$sacData['shortname'] = strtoupper($sacName);
			$sacData['active'] = true;
			$sacData['old_director_name'] = $division['Division']['director'];
			$sacData['old_director_email'] = $division['Division']['email'];
			$sacData['director_id'] = $director_id;
			$sacData['branch_id'] = $branch_id;
			$sacData['created'] = $division['Division']['created'];
			$sacData['modified'] = $division['Division']['modified'];
			
			if(isset($sacCache[$sacData['shortname']]) and $sacCache[$sacData['shortname']])
			{
				$sac = $sacCache[$sacData['shortname']];
			}
			else
			{
				$sac = $this->Branch->Sac->find('first', array(
					'recursive' => -1,
					'order' => array('Sac.shortname' => 'ASC'),
					'conditions' => array(
						'Sac.shortname' => $sacData['shortname'],
					),
				));
				if($sac)
				{
					// Update the existing record
					$this->Branch->Sac->id = $sac['Sac']['id'];
					$this->Branch->Sac->data = $sacData;
					$this->Branch->Sac->save($this->Branch->Sac->data);
					
					$sacCache[$sacData['shortname']] = $sac;
				}
			}
			
			if($sac)
			{
				$sac_id = $sac['Sac']['id'];
			}
			else
			{	
				if($sac_id = $this->Branch->Sac->checkAdd($sacData['shortname'], $sacData))
				{
					$this->Branch->Sac->recursive = -1;
					$sac = $this->Branch->Sac->read(null, $sac_id);
					$sacCache[$sacData['shortname']] = $sac;
				}
			}


			// find all AdAccounts assigned to this old division, and assign them to this new sac
			$adAccounts = $this->Branch->Sac->AdAccount->find('all', array(
				'conditions' => array(
					'AdAccount.division_id' => $division['Division']['id'],
				),
			));
			
			foreach($adAccounts as $adAccount)
			{
				$this->Branch->Sac->AdAccount->id = $adAccount['AdAccount']['id'];
				$this->Branch->Sac->AdAccount->data = array(
					'id' => $this->Branch->Sac->AdAccount->id,
					'sac_id' => $sac_id,
				);
				$this->Branch->Sac->AdAccount->save($this->Branch->Sac->AdAccount->data);
			}
		}
	}
}
