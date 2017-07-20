<?php
App::uses('ContactsAppModel', 'Contacts.Model');

class ContactsAdAccount extends ContactsAppModel 
{
	public $useDbConfig = 'plugin_contacts';
	public $schemaName = 'cakephp_plugin_contacts';
	public $useTable = 'ad_accounts';
	public $name = 'AdAccount';
	public $displayField = 'username';
	public $virtualFields = array(
		'name_email' => 'CONCAT(AdAccount.name, " (", AdAccount.email, ")")',
		'name_username' => 'CONCAT(AdAccount.name, " (", AdAccount.username, ")")',
		'name_username_email' => 'CONCAT(AdAccount.name, " (", AdAccount.username, " - ", AdAccount.email, ")")',
	);
	
	public $_hasMany = array(
		'ContactsFismaSystem' => array(
			'className' => 'ContactsFismaSystem',
			'foreignKey' => 'owner_contact_id',
			'dependent' => true,
		),
		'AssocAccount' => array(
			'className' => 'AssocAccount',
			'foreignKey' => 'ad_account_id',
			'dependent' => true,
		),
		'SacDirector' => array(
			'className' => 'Sac',
			'foreignKey' => 'director_id',
			'dependent' => true,
		),
		'BranchDirector' => array(
			'className' => 'Branch',
			'foreignKey' => 'director_id',
			'dependent' => true,
		),
		'DivisionDirector' => array(
			'className' => 'Division',
			'foreignKey' => 'director_id',
			'dependent' => true,
		),
		'OrgDirector' => array(
			'className' => 'Org',
			'foreignKey' => 'director_id',
			'dependent' => true,
		),
		'SacCrm' => array(
			'className' => 'Sac',
			'foreignKey' => 'crm_id',
			'dependent' => true,
		),
		'BranchCrm' => array(
			'className' => 'Branch',
			'foreignKey' => 'crm_id',
			'dependent' => true,
		),
		'DivisionCrm' => array(
			'className' => 'Division',
			'foreignKey' => 'crm_id',
			'dependent' => true,
		),
		'OrgCrm' => array(
			'className' => 'Org',
			'foreignKey' => 'crm_id',
			'dependent' => true,
		),
	);
	
	public $_belongsTo = array(
		'Sac' => array(
			'className' => 'Sac',
			'foreignKey' => 'sac_id',
		),
	);
	
	// define the fields that can be searched
	public $_searchFields = array(
		'AdAccount.username',
		'AdAccount.name',
		'AdAccount.email',
		'Sac.shortname',
		'Sac.name',
	);
	
	public $autocompleteMap = array(
		'fields' => array('AdAccount.username'),
		'value' => 'name', // the fields to use as the display value
		'data' => 'username', // the fields to use as the actual value
		'include_data' => true, // include the data result in the display value
		'group' => 'Sac.shortname',
		'recursive' => 0,
	);
	
	public $modelReportingCode = 'info';
	
	public function beforeSave($options = array())
	{
		if (isset($this->data[$this->alias]['userid']))
		{
			$this->data[$this->alias]['userid'] = preg_replace('/[^\d]/', '', $this->data[$this->alias]['userid']);
		}
		if (isset($this->data[$this->alias]['phone_number']))
		{
			$this->data[$this->alias]['phone_number'] = str_replace('.', '-', $this->data[$this->alias]['phone_number']);
		}
		
		return parent::beforeSave($options);
	}
	
	public function checkAdd($username = false, $extra = array())
	{
		if(!$username) return false;
		
		$username = trim($username);
		if(!$username) return false;
		
		$username = strtolower($username);
		
		if($id = $this->field($this->primaryKey, array($this->alias.'.username' => $username)))
		{
			return $id;
		}
		
		if(!isset($extra['created']))
			$extra['created'] = date('Y-m-d H:i:s');
		
		// not an existing one, create it
		$this->create();
		$this->data = array_merge(array('username' => $username), $extra);
		if($this->save($this->data))
		{
			$id = $this->id;
			$this->nedUpdate($id);
			return $id;
		}
		return false;
	}
	
	public function typeFormList()
	{
		return $this->find('list', array(
			'fields' => array(
				$this->alias.'.id', 
				$this->alias.'.name_username_email',
			),
			'order' => array($this->alias.'.name' => 'ASC'),
		));
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
	
	public function getDirInfo($username = false, $email = false, $userid = false)
	{
		$this->modelError = false;
		$this->modelErrorCode = 0;
		if(!$username and !$email and !$userid)
		{
			$this->modelError = _('Invalid AD Account username, email, or NIH ID.');
			$this->modelReportingCode = 'error';
			return false;
		}
		
		$userInfo = false;
		if($username)
		{
			if(!$userInfo = $this->Contacts_getInfoByUsername($username))
			{
				$this->modelReportingCode = 'warning';
				if(!$this->modelError) $this->modelError = _('Unable to find the User in the USER Database from the username.');
				//return false;
			}
		}
		
		if($userid and !$userInfo)
		{
			if(!$userInfo = $this->Contacts_getInfoByUserid($userid))
			{
				$this->modelReportingCode = 'warning';
				if(!$this->modelError) $this->modelError = _('Unable to find the User in the USER Database from the NIH ID.');
				//return false;
			}
		}
		
		if($email and !$userInfo)
		{
			if(!$userInfo = $this->Contacts_getInfoByEmail($email))
			{
				$this->modelReportingCode = 'warning';
				if(!$this->modelError) $this->modelError = _('Unable to find the User in the USER Database from the email.');
				//return false;
			}
		}
		
		if(!$userInfo)
		{
			return false;
		}
		
		// check the Sac
		if($userInfo['sac'])
		{
			$sac = trim($userInfo['sac']);
			$sac = strtoupper($sac);
			
			if($sac_id = $this->Sac->checkAdd($sac))
			{
				$userInfo['sac_id'] = $sac_id;
			}
		}
		return $userInfo;
	}
	
	public function userUpdate($id = false)
	{
		$this->modelReportingCode = 'info';
		$this->modelError = false;
		if(!$id)
		{
			$this->modelReportingCode = 'error';
			$this->modelError = _('Invalid AD Account ID.');
			return false;
		}
		
		if(!$adAccount = $this->find('first', array(
			'conditions' => array(
					$this->alias.'.id' => $id,
			),
			'recursive' => -1,
		)))
		{
			$this->modelReportingCode = 'error';
			$this->modelError = _('Invalid AD Account.');
			return false;
		}
		
		if(!$adAccount['AdAccount']['username'])
		{
			$this->modelReportingCode = 'error';
			$this->modelError = _('Invalid AD Account username.');
			return false;
		}
		
		$this->data = array();
		
		if(!$userInfo = $this->getDirInfo($adAccount['AdAccount']['username'], $adAccount['AdAccount']['email'], $adAccount['AdAccount']['userid']))
		{
			$this->modelReportingCode = 'warning';
			if(!$this->modelError) $this->modelError = _('Unable to find the User in the USER Database.');
			
			// update the database to show he wasn't in USER
			$this->id = $this->data['id'] = $adAccount['AdAccount']['id'];
			$this->data['user_present'] = 1;
			$this->save($this->data);
			return false;
		}
		$this->data['user_present'] = 2;
		
		$update = false;
		// only update fields that are empty in our database
		foreach($adAccount['AdAccount'] as $field => $value)
		{
			if(isset($userInfo[$field]) and $value != $userInfo[$field])
			{
				$this->data[$field] = $userInfo[$field];
				$update = true;
			}
		}
		
		// check the Sac
		if($userInfo['sac'])
		{
			$sac = trim($userInfo['sac']);
			$sac = strtoupper($sac);
			
			if($sac_id = $this->Sac->checkAdd($sac))
			{
				if($adAccount['AdAccount']['sac_id'] != $sac_id)
				{
					$this->data['sac_id'] = $sac_id;
					$update = true;
				}
			}
		}
		
		if($this->data)
		{
			$this->id = $this->data['id'] = $adAccount['AdAccount']['id'];
			$this->data['user_checked'] = date('Y-m-d H:i:s');
			if($update)
			{
				$this->modelReportingCode = 'notice';
				if(!$this->modelError) $this->modelError = _('User has fields that needed to be updated.');
				$this->data['user_updated'] = date('Y-m-d H:i:s');
			}
			
			if($this->save($this->data))
			{
				$adAccount = $this->find('first', array(
					'contain' => array('Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org'),
					'conditions' => array(
						$this->alias.'.id' => $this->id,
					),
				));
				
				return $adAccount;
			}
		}
		
		$this->modelReportingCode = 'info';
		if(!$this->modelError) $this->modelError = _('Nothing to update for this user.');
		return false;
	}
	
	public function idsForOrg($org_id = false)
	{
		if(!$divisionIds = $this->Sac->Branch->Division->idsForOrg($org_id)) { return array(); }
		
		return $this->idsForDivision($divisionIds);
	}
	
	public function idsForDivision($division_id = false)
	{
		if(!$branchIds = $this->Sac->Branch->idsForDivision($division_id)) { return array(); }
		
		return $this->idsForBranch($branchIds);
	}
	
	public function idsForBranch($branch_id = false)
	{
		if(!$sacIds = $this->Sac->idsForBranch($branch_id)) { return array(); }
		
		return $this->idsForSac($sacIds);
	}
	
	public function idsForSac($sac_id = false)
	{
		if(!$adAccountIds = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.sac_id' => $sac_id,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		))) { return array(); }
		
		return $adAccountIds;
	}
	
	public function idsForDirector($ad_account_id = false, $type = false)
	{
		$out = array();
		
		if(!$type or $type == 'Sac')
			if($sac_ids = $this->Sac->idsForDirector($ad_account_id))
				$out['sac_ids'] = $sac_ids;
		if(!$type or $type == 'Branch')
			if($branch_ids = $this->Sac->Branch->idsForDirector($ad_account_id))
				$out['branch_ids'] = $branch_ids;
		if(!$type or $type == 'Division')
			if($division_ids = $this->Sac->Branch->Division->idsForDirector($ad_account_id))
				$out['division_ids'] = $division_ids;
		if(!$type or $type == 'Org')
			if($org_ids = $this->Sac->Branch->Division->Org->idsForDirector($ad_account_id))
				$out['org_ids'] = $org_ids;
		
		return $out;
	}
	
	public function listDirectorOf($ad_account_id = false, $type = false)
	{
		if(!$ad_account_id)
			return array();
		
		$directorOf_ids = $this->idsForDirector($ad_account_id, $type);
		if(!$directorOf_ids)
			return array();
		
		$out = array();
		
		if(isset($directorOf_ids['sac_ids']))
			if($results = $this->Sac->find('all', array(
				'contain' => array('SacDirector', 'SacCrm'),
				'conditions' => array('Sac.id' => $directorOf_ids['sac_ids']),
			)))
				$out['sacs'] = $results;
				
		if(isset($directorOf_ids['branch_ids']))
			if($results = $this->Sac->Branch->find('all', array(
				'contain' => array('BranchDirector', 'BranchCrm'),
				'conditions' => array('Branch.id' => $directorOf_ids['branch_ids']),
			)))
				$out['branches'] = $results;
				
		if(isset($directorOf_ids['division_ids']))
			if($results = $this->Sac->Branch->Division->find('all', array(
				'contain' => array('DivisionDirector', 'DivisionCrm'),
				'conditions' => array('Division.id' => $directorOf_ids['division_ids']),
			)))
				$out['divisions'] = $results;
				
		if(isset($directorOf_ids['org_ids']))
			if($results = $this->Sac->Branch->Division->Org->find('all', array(
				'contain' => array('OrgDirector', 'OrgCrm'),
				'conditions' => array('Org.id' => $directorOf_ids['org_ids']),
			)))
				$out['orgs'] = $results;
		
		return $out;
	}
	
	public function idsForEmpties()
	{
		$ids = $this->find('list', array(
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		));
		
		foreach($ids as $i => $id)
		{
			if($count = $this->AssocAccount->idsForAdAccount($id))
			{
				unset($ids[$i]);
			}
		}
		return $ids;
	}
	
	public function testDir($username = false)
	{
		if(!$username)
		{
			$this->shellOut(__('No username given'), 'contacts', 'error');
			return false;
		}
		
		if(preg_match('/^\d+$/', $username))
		{
			$this->shellOut(__('Given username is possibly a primary key.'), 'contacts', 'notice');
			if(!$username = $this->getUsername($username))
			{
				$this->shellOut(__('Unable to find username from primary key in the database.'), 'contacts', 'error');
				return false;
			}
		}
		$this->shellOut(__('Testing with username: %s', $username), 'contacts');
		
		
		$this->shellOut(__('Testing the connection to the USER database'), 'contacts');
		$result = $this->Contacts_testDir($username);
		$this->shellOut(__('Connection - Code: %s - Result: %s', $result['code'], $result['message']), 'contacts', ($result['success']?'info':'error'));
	}
	
	public function updateFromDir()
	{
		$adAccounts = $this->find('list', array(
			'order' => array($this->alias.'.username' => 'ASC'),
			'conditions' => array(
				'OR' => array(
					$this->alias.'.user_checked <' => date('Y-m-d H:i:s', strtotime('-1 week')),
					$this->alias.'.user_checked' => null
				),
			),
		));
		
		$count_adAccounts = count($adAccounts);
		
		$this->shellOut(__('Found %s AD Accounts', $count_adAccounts), 'contacts');
		
		$no = $yes = $updated = 0;
		$i=0;
		foreach($adAccounts as $adAccount_id => $adAccount_username)
		{
			$i++;
			$this->shellOut(__('(%s/%s) Checking/Updating: %s', $i, $count_adAccounts, $adAccount_username), 'contacts');
			
			if(!$this->nedUpdate($adAccount_id))
			{
				$this->shellOut(__('Unable to update: %s - Reason: %s', $adAccount_username, $this->modelError), 'contacts', $this->modelReportingCode);
				if($this->modelReportingCode == 'warning')
					$no++;
				continue;
			}
			
			if($this->modelReportingCode == 'info')
				$yes++;
			elseif($this->modelReportingCode == 'notice')
				$updated++;
			$this->shellOut(__('(%s/%s) Checking/Updating: %s - Success -  %s', $i, $count_adAccounts, $adAccount_username, $this->modelError), 'contacts', $this->modelReportingCode);
		}
		
		$this->shellOut(__('Checking/Updating complete: NO: %s - YES: %s - Updated: %s', $no, $yes, $updated), 'contacts');
	}
}
