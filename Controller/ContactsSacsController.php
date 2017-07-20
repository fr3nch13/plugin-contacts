<?php
App::uses('ContactsAppController', 'Contacts.Controller');

class ContactsSacsController extends ContactsAppController
{
	public $uses = array('Sac');
	public $allowAdminDelete = true;

	public function autocomplete()
	{
		if(!$this->request->is('ajax'))
		{
			throw new NotFoundException(__('Invalid Request'));
		}
		
		$query = false;
		if(isset($this->request->query['query']))
			$query = $this->request->query['query'];
		
		$results = $this->Sac->autocompleteLookup($query);
		$this->set('results', $results);
		
		$this->layout = 'Utilities.ajax_nodebug';
		return $this->render('/Elements/autocomplete_response');
	}
	
	public function directors()
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		$conditions = array_merge($conditions, $this->conditions);
		
		$limit = $this->Sac->find('count');
		
		$this->paginate['contain'] = array(
			'SacDirector',
			'Branch', 'Branch.BranchDirector', 
			'Branch.Division', 'Branch.Division.DivisionDirector', 
			'Branch.Division.Org', 'Branch.Division.Org.OrgDirector',
		);
		$this->paginate['order'] = array('Sac.shortname' => 'asc');
		$this->paginate['limit'] = $this->paginate['maxLimit'] = $limit;
		$this->paginate['conditions'] = $this->Sac->conditions($conditions, $this->passedArgs); 
		
		$sacs = $this->paginate();
		$this->set('sacs', $sacs);
		
		$adAccounts = $this->Sac->AdAccount->typeFormList();
		$branches = $this->Sac->Branch->typeFormList();
		$divisions = $this->Sac->Branch->Division->typeFormList();
		$orgs = $this->Sac->Branch->Division->Org->typeFormList();
		$this->set(compact(array('adAccounts', 'branches', 'divisions', 'orgs')));
		
		$this->view = 'Contacts./ContactsSacs/directors';
		return $this->render($this->view);
	}
	
	public function crms()
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		$conditions = array_merge($conditions, $this->conditions);
		
		$limit = $this->Sac->find('count');
		
		$this->paginate['contain'] = array(
			'SacCrm',
			'Branch', 'Branch.BranchCrm', 
			'Branch.Division', 'Branch.Division.DivisionCrm', 
			'Branch.Division.Org', 'Branch.Division.Org.OrgCrm',
		);
		$this->paginate['order'] = array('Sac.shortname' => 'asc');
		$this->paginate['limit'] = $this->paginate['maxLimit'] = $limit;
		$this->paginate['conditions'] = $this->Sac->conditions($conditions, $this->passedArgs); 
		
		$sacs = $this->paginate();
		$this->set('sacs', $sacs);
		
		$adAccounts = $this->Sac->AdAccount->typeFormList();
		$branches = $this->Sac->Branch->typeFormList();
		$divisions = $this->Sac->Branch->Division->typeFormList();
		$orgs = $this->Sac->Branch->Division->Org->typeFormList();
		$this->set(compact(array('adAccounts', 'branches', 'divisions', 'orgs')));
		
		$this->view = 'Contacts./ContactsSacs/crms';
		return $this->render($this->view);
	}

	public function index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		$conditions = array_merge($conditions, $this->conditions);
		
		if(!isset($this->passedArgs['getcount']))
			$this->paginate['contain'] = array('SacDirector', 'SacCrm', 'Branch', 'Branch.Division', 'Branch.Division.Org');
		$this->paginate['order'] = array('Sac.shortname' => 'asc');
		$this->paginate['conditions'] = $this->Sac->conditions($conditions, $this->passedArgs); 
		
		$sacs = $this->paginate();
		$this->set('sacs', $sacs);
		
		$branches = $this->Sac->Branch->typeFormList();
		$adAccounts = $this->Sac->SacDirector->typeFormList();
		$this->set(compact(array('branches', 'adAccounts')));
	}
	
	public function org($org_id = false)
	{
		if (!$org_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Org')));
		}
		if (!$org =  $this->Sac->Branch->Division->Org->read(null, $org_id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Org')));
		}
		$this->set('org', $org);
		
		$divisionIds = $this->Sac->Branch->Division->find('list', array(
			'conditions' => array(
				'Division.org_id' => $org_id,
			),
			'fields' => array('Division.id', 'Division.id'),
		));
		
		$branchIds = $this->Sac->Branch->find('list', array(
			'conditions' => array(
				'Branch.division_id' => $divisionIds,
			),
			'fields' => array('Branch.id', 'Branch.id'),
		));
		
		$conditions = array(
			'Sac.branch_id' => $branchIds,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Under the %s of %s', __('ORG/IC'), $org['Org']['shortname']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function division($division_id = null)  
	{
		if (!$division_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		
		$division = $this->Sac->Branch->Division->read(null, $division_id);
		if (!$division) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		$this->set('division', $division);
		
		$branchIds = $this->Sac->Branch->find('list', array(
			'conditions' => array(
				'Branch.division_id' => $division_id,
			),
			'fields' => array('Branch.id', 'Branch.id'),
		));
		
		$conditions = array(
			'Sac.branch_id' => $branchIds,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Under the %s of %s', __('Division'), $division['Division']['shortname']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function branch($branch_id = null)  
	{
		if (!$branch_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		
		$branch = $this->Sac->Branch->read(null, $branch_id);
		if (!$branch) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		$this->set('branch', $branch);
		
		$conditions = array(
			'Sac.branch_id' => $branch_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Under the %s of %s', __('Branch'), $branch['Branch']['shortname']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function empties()
	{	
		$conditions = array(
			'Sac.id' => $this->Sac->idsForEmpties(),
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('With no %s', __('AD Accounts')));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function orphans()
	{	
		$conditions = array(
			'Sac.branch_id' => 0,
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Not associated with a %s', __('Branch')));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function director($director_id = null)  
	{ 
		if (!$director_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Director')));
		}
		
		$director = $this->Sac->SacDirector->read(null, $director_id);
		if (!$director) 
		{
			throw new NotFoundException(__('Invalid %s', __('Director')));
		}
		$this->set('director', $director);
		
		$conditions = array(
			'Sac.director_id' => $director_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('with Director %s', $director['SacDirector']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function crm($crm_id = null)  
	{ 
		if (!$crm_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('CRM')));
		}
		
		$crm = $this->Sac->SacCrm->read(null, $crm_id);
		if (!$crm) 
		{
			throw new NotFoundException(__('Invalid %s', __('CRM')));
		}
		$this->set('crm', $crm);
		
		$conditions = array(
			'Sac.crm_id' => $crm_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('with CRM %s', $crm['SacCrm']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function orgchart_directors()
	{
		$list = $this->Sac->Contacts_orgChart(array(
			'contain' => array(
				'Branch', 'Branch.BranchDirector', 
				'Branch.Division', 'Branch.Division.DivisionDirector',
				'Branch.Division.Org', 'Branch.Division.Org.OrgDirector'
			),
		));
		$this->set('list', $list);
		
		$this->view = 'Contacts./ContactsSacs/orgchart_directors';
		return $this->render($this->view);
	}
	
	public function orgchart_crms()
	{
		$list = $this->Sac->Contacts_orgChart(array(
			'contain' => array(
				'Branch', 'Branch.BranchCrm', 
				'Branch.Division', 'Branch.Division.DivisionCrm',
				'Branch.Division.Org', 'Branch.Division.Org.OrgCrm'
			),
		));
		$this->set('list', $list);
		
		$this->view = 'Contacts./ContactsSacs/orgchart_crms';
		return $this->render($this->view);
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->Sac->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions = array();
		
		$conditions[] = $this->Sac->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Sac');
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Tagged with %s', $tag['Tag']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function view($id = null) 
	{
		$this->Sac->contain(array('SacDirector', 'SacCrm', 'Branch', 'Branch.Division', 'Branch.Division.Org'));
		if(!$sac = $this->Sac->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Sac')));
		}
		$this->set('sac', $sac);
	}
	
	public function add($id = null) 
	{
		if ($this->request->is('post')) 
		{
			$this->Sac->create();
			if ($this->Sac->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('Sac')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->fail(__('The %s could not be saved. Please, try again.', __('Sac')));
			}
		}
		
		// create a duplicate of the id to add as a new one
		elseif($id)
		{
			if($sac = $this->Sac->read(null, $id))
			{
				unset($sac['Sac']['id']);
				$this->request->data = $sac;
			}
		}
		
		$branches = $this->Sac->Branch->typeFormList();
		$adAccounts = $this->Sac->SacDirector->typeFormList();
		$this->set(compact(array('branches', 'adAccounts')));
	}
	
	public function edit($id = null) 
	{
		$this->Sac->contain(array('Tag'));
		if(!$sac = $this->Sac->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Sac')));
		}
		
		if ($this->request->is(array('post', 'put'))) 
		{
			if ($this->Sac->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('Sac')));
				return $this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Flash->fail(__('The %s could not be saved. Please, try again.', __('Sac')));
			}
		} 
		else 
		{
			$this->request->data = $sac;
		}
		
		$branches = $this->Sac->Branch->typeFormList();
		$adAccounts = $this->Sac->SacDirector->typeFormList();
		$this->set(compact(array('branches', 'adAccounts')));
	}
	
	public function admin_index()
	{
		return $this->redirect(array('action' => 'index', 'admin' => false));
	}
}
