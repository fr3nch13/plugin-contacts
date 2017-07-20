<?php
App::uses('ContactsAppController', 'Contacts.Controller');

class ContactsDivisionsController extends ContactsAppController
{
	public $uses = array('Division');
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
		
		$results = $this->Division->autocompleteLookup($query);
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
			$this->paginate['contain'] = array('Org', 'DivisionDirector', 'DivisionCrm');
		$this->paginate['order'] = array('Division.shortname' => 'asc', 'Division.sac' => 'asc');
		$this->paginate['conditions'] = $this->Division->conditions($conditions, $this->passedArgs); 
		
		$divisions = $this->paginate();
		$this->set('divisions', $divisions);
		
		$orgs = $this->Division->Org->typeFormList();
		$adAccounts = $this->Division->DivisionDirector->typeFormList();
		$this->set(compact(array('orgs', 'adAccounts')));
	}
	
	public function org($org_id = false)
	{
		if (!$org_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Org')));
		}
		if (!$org =  $this->Division->Org->read(null, $org_id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Org')));
		}
		$this->set('org', $org);
		
		$conditions = array(
			'Division.org_id' => $org_id,
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Under the %s of %s', __('ORG/IC'), $org['Org']['shortname']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function empties()
	{	
		$conditions = array(
			'Division.id' => $this->Division->idsForEmpties(),
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('With no %s', __('Branches')));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function orphans()
	{	
		$conditions = array(
			'Division.org_id' => 0,
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Not associated with an %s', __('ORG/IC')));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function director($director_id = null)  
	{ 
		if (!$director_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Director')));
		}
		
		$director = $this->Division->DivisionDirector->read(null, $director_id);
		if (!$director) 
		{
			throw new NotFoundException(__('Invalid %s', __('Director')));
		}
		$this->set('director', $director);
		
		$conditions = array(
			'Division.director_id' => $director_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('with Director %s', $director['DivisionDirector']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function crm($crm_id = null)  
	{ 
		if (!$crm_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('CRM')));
		}
		
		$crm = $this->Division->DivisionCrm->read(null, $crm_id);
		if (!$crm) 
		{
			throw new NotFoundException(__('Invalid %s', __('CRM')));
		}
		$this->set('crm', $crm);
		
		$conditions = array(
			'Division.crm_id' => $crm_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('with CRM %s', $crm['DivisionCrm']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->Division->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions = array();
		
		$conditions[] = $this->Division->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Division');
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Tagged with %s', $tag['Tag']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function view($id = null) 
	{
		$this->Division->contain(array('Org', 'DivisionDirector', 'DivisionCrm'));
		if (!$division =  $this->Division->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		$this->set('division', $division);
	}
	
	public function add($id = null) 
	{
		if ($this->request->is('post')) 
		{
			$this->Division->create();
			if ($this->Division->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('Division')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Division')));
			}
		}
		
		// create a duplicate of the id to add as a new one
		elseif($id)
		{
			if($division = $this->Division->read(null, $id))
			{
				unset($division['Division']['id']);
				$this->request->data = $division;
			}
		}
		
		$orgs = $this->Division->Org->typeFormList();
		$adAccounts = $this->Division->DivisionDirector->typeFormList();
		$this->set(compact(array('orgs', 'adAccounts')));
	}
	
	public function edit($id = null) 
	{
		$this->Division->contain(array('Tag'));
		if(!$division = $this->Division->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		
		if ($this->request->is(array('post', 'put'))) 
		{
			if ($this->Division->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('Division')));
				return $this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Division')));
			}
		} 
		else 
		{
			$this->request->data = $division;
		}
		
		$orgs = $this->Division->Org->typeFormList();
		$adAccounts = $this->Division->DivisionDirector->typeFormList();
		$this->set(compact(array('orgs', 'adAccounts')));
	}
	
	public function admin_index()
	{
		return $this->redirect(array('action' => 'index', 'admin' => false));
	}
}
