<?php

App::uses('ContactsAppController', 'Contacts.Controller');

class ContactsFismaSystemsController extends ContactsAppController
{
	public function beforeFilter()
	{
		if(isset($this->ContactsFismaSystem))
			$this->FismaSystem = &$this->ContactsFismaSystem;
		
		return parent::beforeFilter();
	}
	
	public function search_results()
	{
		return $this->index();
	}
	
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = [];
		$conditions = array_merge($conditions, $this->conditions);
		
		if(!isset($this->passedArgs['getcount']))
		{
			if(!isset($this->paginate['contain']))
				$this->paginate['contain'] = [];
			$this->paginate['contain'] = array_merge([
				'FismaSystemParent', 'OwnerContact', 'OwnerContact.Sac', 
				'OwnerContact.Sac.Branch', 'OwnerContact.Sac.Branch.Division', 
				'OwnerContact.Sac.Branch.Division.Org'
			], $this->paginate['contain']);
		}
		$this->paginate['conditions'] = $this->FismaSystem->conditions($conditions, $this->passedArgs);
		
		$records = $this->paginate();
		
		$this->set(compact(array('records')));
	}
	
	public function org($org_id = null, $contact_type = 'owner')  
	{
		if (!$org_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('ORG/IC')));
		}
		
		$org = $this->FismaSystem->OwnerContact->Sac->Branch->Division->Org->find('first', [
			'conditions' => ['Org.id' => $org_id],
		]);
		if (!$org) 
		{
			throw new NotFoundException(__('Invalid %s', __('ORG/IC')));
		}
		$this->set('object', $org);
		
		$contact_ids = $this->FismaSystem->OwnerContact->idsForOrg($org_id);
		
		$conditions = $this->FismaSystem->_buildIndexConditions($contact_ids, $contact_type);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function division($division_id = null, $contact_type = 'owner')  
	{
		if (!$division_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		
		$division = $this->FismaSystem->OwnerContact->Sac->Branch->Division->find('first', [
			'conditions' => ['Division.id' => $division_id],
			'contain' => ['Org'],
		]);
		if (!$division) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		$this->set('object', $division);
		
		$contact_ids = $this->FismaSystem->OwnerContact->idsForDivision($division_id);
		
		$conditions = $this->FismaSystem->_buildIndexConditions($contact_ids, $contact_type);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function branch($branch_id = null, $contact_type = 'owner')  
	{
		if (!$branch_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		
		$branch = $this->FismaSystem->OwnerContact->Sac->Branch->find('first', [
			'conditions' => ['Branch.id' => $branch_id],
			'contain' => ['Division', 'Division.Org'],
		]);
		if (!$branch) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		$this->set('object', $branch);
		
		$contact_ids = $this->FismaSystem->OwnerContact->idsForBranch($branch_id);
		
		$conditions = $this->FismaSystem->_buildIndexConditions($contact_ids, $contact_type);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function sac($sac_id = null, $contact_type = 'owner')  
	{
		if (!$sac_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('SAC')));
		}
		
		$sac = $this->FismaSystem->OwnerContact->Sac->find('first', [
			'conditions' => ['Sac.id' => $sac_id],
			'contain' => ['Branch', 'Branch.Division', 'Branch.Division.Org'],
		]);
		if (!$sac) 
		{
			throw new NotFoundException(__('Invalid %s', __('SAC')));
		}
		$this->set('object', $sac);
		
		$contact_ids = $this->FismaSystem->OwnerContact->idsForSac($sac_id);
		
		$conditions = $this->FismaSystem->_buildIndexConditions($contact_ids, $contact_type);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function contact($contact_id = false, $contact_type = 'owner')
	{
		if (!$contact_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Contact')));
		}
		
		$ownerContact = $this->FismaSystem->OwnerContact->find('first', [
			'conditions' => ['OwnerContact.id' => $contact_id],
			'contain' => ['Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org'],
		]);
		$ownerContact['AdAccount'] = $ownerContact['OwnerContact'];
		if (!$ownerContact) 
		{
			throw new NotFoundException(__('Invalid %s', __('Contact')));
		}
		$this->set('object', $ownerContact);
		
		$conditions = $this->FismaSystem->_buildIndexConditions($contact_id, $contact_type);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function crm($crm_id = false)
	{
		if (!$crm_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('CRM')));
		}
		
		$crm = $this->FismaSystem->OwnerContact->find('first', [
			'conditions' => ['OwnerContact.id' => $crm_id],
			'contain' => ['Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org'],
		]);
		$crm['AdAccount'] = $crm['OwnerContact'];
		if (!$crm) 
		{
			throw new NotFoundException(__('Invalid %s', __('CRM')));
		}
		$this->set('object', $crm);
		
		// find all of the orgs that this user is a crm of
		$fismaSystem_ids = $this->FismaSystem->idsForCrm($crm_id);
		
		$conditions = [
			'FismaSystem.id' => $fismaSystem_ids,
		];
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function parents()
	{
		$this->set('page_subtitle', __('All Parents, or No Family'));
		
		$conditions = [];
		
		$this->paginate['findType'] = 'AllParents';
		$this->paginate['conditions'] = $this->FismaSystem->conditions($conditions, $this->passedArgs);
		
		$conditions = array_merge($conditions, $this->conditions);
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function all_children()
	{
		$this->set('page_subtitle', __('All Children'));
		
		$conditions = [];
		
		$this->paginate['findType'] = 'AllChildren';
		$this->paginate['conditions'] = $this->FismaSystem->conditions($conditions, $this->passedArgs);
		
		$conditions = array_merge($conditions, $this->conditions);
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function no_family()
	{
		$this->set('page_subtitle', __('No Family'));
		
		$conditions = [];
		
		$this->paginate['findType'] = 'NoFamily';
		$this->paginate['conditions'] = $this->FismaSystem->conditions($conditions, $this->passedArgs);
		
		$conditions = array_merge($conditions, $this->conditions);
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function my_children($id = false)
	{
		$this->set('page_subtitle', __('My Children'));
		
		$conditions = [];
		
		$this->FismaSystem->id = $id;
		$this->FismaSystem->recursive = 0;
		if (!$fismaSystem = $this->FismaSystem->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('FISMA System')));
		}
		
		$this->set('fismaSystem', $fismaSystem);
		
		$this->paginate['findType'] = 'MyChildren';
		$this->paginate['conditions'] = $this->FismaSystem->conditions($conditions, $this->passedArgs);
		
		$conditions = array_merge($conditions, $this->conditions);
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function owners()
	{
		$this->set('page_subtitle', __('With Owners'));
		
		$conditions = [
			'FismaSystem.owner_contact_id >' => 0,
		];
		
		$conditions = array_merge($conditions, $this->conditions);
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function no_owner()
	{
		$this->set('page_subtitle', __('No Owner'));
		
		$conditions = [
			'FismaSystem.owner_contact_id <' => 1,
		];
		
		$conditions = array_merge($conditions, $this->conditions);
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function view($id = false) 
	{
		$this->FismaSystem->contain([
			'FismaSystemParent', 'OwnerContact', 
			'OwnerContact.Sac', 'OwnerContact.Sac.SacDirector', 'OwnerContact.Sac.SacCrm', 
			'OwnerContact.Sac.Branch', 'OwnerContact.Sac.Branch.BranchDirector', 'OwnerContact.Sac.Branch.BranchCrm', 
			'OwnerContact.Sac.Branch.Division', 'OwnerContact.Sac.Branch.Division.DivisionDirector', 'OwnerContact.Sac.Branch.Division.DivisionCrm', 
			'OwnerContact.Sac.Branch.Division.Org', 'OwnerContact.Sac.Branch.Division.Org.OrgDirector', 'OwnerContact.Sac.Branch.Division.Org.OrgCrm', 
		]);
		if(!$record = $this->FismaSystem->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('FISMA System')));
		}
		$this->set('record', $record);
	}
}