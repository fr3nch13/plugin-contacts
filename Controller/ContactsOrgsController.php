<?php
App::uses('ContactsAppController', 'Contacts.Controller');

class ContactsOrgsController extends ContactsAppController
{
	public $uses = array('Org');
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
		
		$results = $this->Org->autocompleteLookup($query);
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
			$this->paginate['contain'] = array('OrgDirector', 'OrgCrm');
		$this->paginate['order'] = array('Org.shortname' => 'asc', 'Org.sac' => 'asc');
		$this->paginate['conditions'] = $this->Org->conditions($conditions, $this->passedArgs); 
		
		$orgs = $this->paginate();
		$this->set('orgs', $orgs);
		
		$adAccounts = $this->Org->OrgDirector->typeFormList();
		$this->set(compact(array('adAccounts')));
	}
	
	public function empties()
	{	
		$conditions = array(
			'Org.id' => $this->Org->idsForEmpties(),
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('With no %s', __('Divisions')));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function director($director_id = null)  
	{ 
		if (!$director_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Director')));
		}
		
		$director = $this->Org->OrgDirector->read(null, $director_id);
		if (!$director) 
		{
			throw new NotFoundException(__('Invalid %s', __('Director')));
		}
		$this->set('director', $director);
		
		$conditions = array(
			'Org.director_id' => $director_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('with Director %s', $director['OrgDirector']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function crm($crm_id = null)  
	{ 
		if (!$crm_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('CRM')));
		}
		
		$crm = $this->Org->OrgCrm->read(null, $crm_id);
		if (!$crm) 
		{
			throw new NotFoundException(__('Invalid %s', __('CRM')));
		}
		$this->set('crm', $crm);
		
		$conditions = array(
			'Org.crm_id' => $crm_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('with CRM %s', $crm['OrgCrm']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->Org->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions = array();
		
		$conditions[] = $this->Org->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Org');
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Tagged with %s', $tag['Tag']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function view($id = null) 
	{
		$this->Org->contain(array('OrgDirector', 'OrgCrm'));
		if(!$org = $this->Org->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('ORG/IC')));
		}
		$this->set('org', $org);
	}
	
	public function add($id = null) 
	{
		if ($this->request->is('post')) 
		{
			$this->Org->create();
			if ($this->Org->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('ORG/IC')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('ORG/IC')));
			}
		}
		
		// create a duplicate of the id to add as a new one
		elseif($id)
		{
			if($org = $this->Org->read(null, $id))
			{
				unset($org['Org']['id']);
				$this->request->data = $org;
			}
		}
		
		$adAccounts = $this->Org->OrgDirector->typeFormList();
		$this->set(compact(array(
			'adAccounts',
		)));
	}
	
	public function edit($id = null) 
	{
		$this->Org->contain(array('Tag'));
		if(!$org = $this->Org->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('ORG/IC')));
		}
		
		if ($this->request->is(array('post', 'put'))) 
		{
			if ($this->Org->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('ORG/IC')));
				return $this->redirect(array('action' => 'index'));
			} 
			else 
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('ORG/IC')));
			}
		} 
		else 
		{
			$this->request->data = $org;
		}
		
		$adAccounts = $this->Org->OrgDirector->typeFormList();
		$this->set(compact(array(
			'adAccounts',
		)));
	}
	
	public function admin_index()
	{
		return $this->redirect(array('action' => 'index', 'admin' => false));
	}
}
