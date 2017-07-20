<?php
App::uses('ContactsAppController', 'Contacts.Controller');

class ContactsBranchesController extends ContactsAppController
{
	public $uses = array('Branch');
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
		
		$results = $this->Branch->autocompleteLookup($query);
		$this->set('results', $results);
		
		$this->layout = 'Utilities.ajax_nodebug';
		return $this->render('/Elements/autocomplete_response');
	}

	public function index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		$conditions = array_merge($conditions, $this->conditions);
		
		if(!isset($this->passedArgs['getcount']))
			$this->paginate['contain'] = array('BranchDirector', 'BranchCrm', 'Division', 'Division.Org');
		$this->paginate['order'] = array('Branch.shortname' => 'asc');
		$this->paginate['conditions'] = $this->Branch->conditions($conditions, $this->passedArgs); 
		
		$branches = $this->paginate();
		$this->set('branches', $branches);
		
		$divisions = $this->Branch->Division->typeFormList();
		$adAccounts = $this->Branch->BranchDirector->typeFormList();
		$this->set(compact(array('divisions', 'adAccounts')));
	}
	
	public function org($org_id = false)
	{
		if (!$org_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Org')));
		}
		if (!$org =  $this->Branch->Division->Org->read(null, $org_id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Org')));
		}
		$this->set('org', $org);
		
		$conditions = array(
			'Division.org_id' => $org_id,
		);
		$this->paginate['contain'] = array('Division', 'BranchDirector', 'BranchCrm');
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
		
		$division = $this->Branch->Division->read(null, $division_id);
		if (!$division) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		$this->set('division', $division);
		
		$conditions = array(
			'Branch.division_id' => $division_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Under the %s of %s', __('Division'), $division['Division']['shortname']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function empties()
	{	
		$conditions = array(
			'Branch.id' => $this->Branch->idsForEmpties(),
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('With no %s', __('SACs')));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function orphans()
	{	
		$conditions = array(
			'Branch.division_id' => 0,
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Not associated with a %s', __('Division')));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function director($director_id = null)  
	{ 
		if (!$director_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Director')));
		}
		
		$director = $this->Branch->BranchDirector->read(null, $director_id);
		if (!$director) 
		{
			throw new NotFoundException(__('Invalid %s', __('Director')));
		}
		$this->set('director', $director);
		
		$conditions = array(
			'Branch.director_id' => $director_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('with Director %s', $director['BranchDirector']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function crm($crm_id = null)  
	{ 
		if (!$crm_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('CRM')));
		}
		
		$crm = $this->Branch->BranchCrm->read(null, $crm_id);
		if (!$crm) 
		{
			throw new NotFoundException(__('Invalid %s', __('CRM')));
		}
		$this->set('crm', $crm);
		
		$conditions = array(
			'Branch.crm_id' => $crm_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('with CRM %s', $crm['BranchCrm']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->Branch->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions = array();
		
		$conditions[] = $this->Branch->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Branch');
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Tagged with %s', $tag['Tag']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function view($id = null) 
	{
		$this->Branch->contain(array('BranchDirector', 'BranchCrm', 'Division', 'Division.Org'));
		if(!$branch = $this->Branch->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		$this->set('branch', $branch);
	}
	
	public function add($id = null) 
	{
		if ($this->request->is('post')) 
		{
			$this->Branch->create();
			if ($this->Branch->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('Branch')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->fail(__('The %s could not be saved. Please, try again.', __('Branch')));
			}
		}
		
		// create a duplicate of the id to add as a new one
		elseif($id)
		{
			if($branch = $this->Branch->read(null, $id))
			{
				unset($branch['Branch']['id']);
				$this->request->data = $branch;
			}
		}
		
		$divisions = $this->Branch->Division->typeFormList();
		$adAccounts = $this->Branch->BranchDirector->typeFormList();
		$this->set(compact(array('divisions', 'adAccounts')));
	}
	
	public function edit($id = null) 
	{
		$this->Branch->contain(array('Tag'));
		if(!$branch = $this->Branch->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		
		if ($this->request->is(array('post', 'put'))) 
		{
			if ($this->Branch->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('Branch')));
				return $this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Flash->fail(__('The %s could not be saved. Please, try again.', __('Branch')));
			}
		} 
		else 
		{
			$this->request->data = $branch;
		}
		
		$divisions = $this->Branch->Division->typeFormList();
		$adAccounts = $this->Branch->BranchDirector->typeFormList();
		$this->set(compact(array('divisions', 'adAccounts')));
	}
	
	public function admin_index()
	{
		return $this->redirect(array('action' => 'index', 'admin' => false));
	}
}
