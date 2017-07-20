<?php

App::uses('ContactsAppController', 'Contacts.Controller');

class ContactsAdAccountsController extends ContactsAppController
{
	public $uses = array('AdAccount');
	public $allowAdminDelete = true;

	public function autocomplete()
	{	
		$query = false;
		if(isset($this->request->query['query']))
			$query = $this->request->query['query'];
		
		$results = $this->AdAccount->autocompleteLookup($query);
		$this->set('results', $results);
		$this->layout = 'Utilities.ajax_nodebug';
		return $this->render('Utilities.Elements/autocomplete_response');
	}
	
	public function search_results()
	{
		return $this->index();
	}
	
	public function index($id = false)
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		$conditions = array_merge($conditions, $this->conditions);
		
		if(!isset($this->passedArgs['getcount']))
		{
			if(!isset($this->paginate['contain']))
				$this->paginate['contain'] = array();
			$this->paginate['contain'] = array_merge(array('Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org'), $this->paginate['contain']);
		}
		$this->paginate['conditions'] = $this->AdAccount->conditions($conditions, $this->passedArgs);
		
		$adAccounts = $this->paginate();
		$this->set('alias', 'AdAccount');
		$this->set('adAccountAlias', __('AD Account'));
		$this->set('adAccountAliases', __('AD Accounts'));
		$this->set('adAccounts', $adAccounts);
		
		
		$sacs = $this->AdAccount->Sac->typeFormList();
		$this->set(compact(array('sacs')));
	}
	
	public function org($org_id = null)  
	{
		if (!$org_id) 
		{
			throw new NotFoundException(__('Invalid %s 1', __('Org')));
		}
		
		$org = $this->AdAccount->Sac->Branch->Division->Org->read(null, $org_id);
		if (!$org) 
		{
			throw new NotFoundException(__('Invalid %s 2', __('Org')));
		}
		$this->set('org', $org);
		
		$divisionIds = $this->AdAccount->Sac->Branch->Division->find('list', array(
			'conditions' => array(
				'Division.org_id' => $org_id,
			),
			'fields' => array('Division.id', 'Division.id'),
		));
		
		$branchIds = $this->AdAccount->Sac->Branch->find('list', array(
			'conditions' => array(
				'Branch.division_id' => $divisionIds,
			),
			'fields' => array('Branch.id', 'Branch.id'),
		));
		
		$sacIds = $this->AdAccount->Sac->find('list', array(
			'conditions' => array(
				'Sac.branch_id' => $branchIds,
			),
			'fields' => array('Sac.id', 'Sac.id'),
		));
		
		$conditions = array(
			'AdAccount.sac_id' => $sacIds,
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
		
		$division = $this->AdAccount->Sac->Branch->Division->read(null, $division_id);
		if (!$division) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		$this->set('division', $division);
		
		$branchIds = $this->AdAccount->Sac->Branch->find('list', array(
			'conditions' => array(
				'Branch.division_id' => $division_id,
			),
			'fields' => array('Branch.id', 'Branch.id'),
		));
		
		$sacIds = $this->AdAccount->Sac->find('list', array(
			'conditions' => array(
				'Sac.branch_id' => $branchIds,
			),
			'fields' => array('Sac.id', 'Sac.id'),
		));
		
		$conditions = array(
			'AdAccount.sac_id' => $sacIds,
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
		
		$branch = $this->AdAccount->Sac->Branch->read(null, $branch_id);
		if (!$branch) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		$this->set('branch', $branch);
		
		$sacIds = $this->AdAccount->Sac->find('list', array(
			'conditions' => array(
				'Sac.branch_id' => $branch_id,
			),
			'fields' => array('Sac.id', 'Sac.id'),
		));
		
		$conditions = array(
			'AdAccount.sac_id' => $sacIds,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Under the %s of %s', __('Branch'), $branch['Branch']['shortname']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function sac($sac_id = null)  
	{
		if (!$sac_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Sac')));
		}
		
		$sac = $this->AdAccount->Sac->read(null, $sac_id);
		if (!$sac) 
		{
			throw new NotFoundException(__('Invalid %s', __('Sac')));
		}
		$this->set('sac', $sac);
		
		$conditions = array(
			'AdAccount.sac_id' => $sac_id,
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Under the %s of %s', __('SAC'), $sac['Sac']['shortname']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function empties()
	{	
		$conditions = array(
			'AdAccount.id' => $this->AdAccount->idsForEmpties(),
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('With no %s', __('Associated Accounts')));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function orphans()
	{	
		$conditions = array(
			'AdAccount.sac_id' => 0,
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Not associated with a %s', __('SAC')));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->AdAccount->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions = array();
		
		$conditions[] = $this->AdAccount->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'AdAccount');
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Tagged with %s', $tag['Tag']['name']));
		$this->conditions = $conditions;
		$this->index();
	}

	public function user_info()
	{
		$result = false;
		if($this->request->is('post') or $this->request->is('put'))
		{
			if(isset($this->request->data['username']))
			{
				$result = $this->AdAccount->getNedInfo($this->request->data['username']);
			}
			elseif(isset($this->request->data['email']))
			{
				$result = $this->AdAccount->getNedInfo(false, $this->request->data['email']);
			}
		}
		
		$this->set('_serialize', array('result'));
		$this->set('result', $result);
	}
	
	public function view($id = null) 
	{
		$this->AdAccount->contain(array('Sac', 'Sac.Branch', 'Sac.Branch.Division', 'Sac.Branch.Division.Org'));
		if(!$adAccount =  $this->AdAccount->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('AD Account')));
		}
		$this->set('adAccount', $adAccount);
	}
	
	public function add() 
	{
		$this->AdAccount->validator()
			->add('username', 'required', array('rule' => 'notBlank'))
			->add('username', 'unique', array('rule' => 'isUnique', 'required' => 'create', 'message' => __('This %s already exists.', __('AD Account'))));
		
		if ($this->request->is(array('post', 'put'))) 
		{
			if ($this->AdAccount->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('AD Account')));
				return $this->redirect(array('action' => 'view', $this->AdAccount->id));
			} 
			else 
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('AD Account')));
			}
		} 
		$sacs = $this->AdAccount->Sac->find('list', array(
			'fields' => array('Sac.id', 'Sac.shortname'),
			'order' => array('Sac.shortname' => 'ASC')
		));
		$this->set(compact('sacs'));
	}
	
	public function edit($id = null) 
	{
		$this->AdAccount->contain(array('Tag'));
		if (!$adAccount =  $this->AdAccount->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('AD Account')));
		}
		
		if ($this->request->is(array('post', 'put'))) 
		{
			if ($this->AdAccount->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('AD Account')));
				return $this->redirect(array('action' => 'view', $id));
			} 
			else 
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('AD Account')));
			}
		} 
		else 
		{
			$this->request->data = $adAccount;
		}
		$sacs = $this->AdAccount->Sac->find('list', array(
			'fields' => array('Sac.id', 'Sac.shortname'),
			'order' => array('Sac.shortname' => 'ASC')
		));
		$this->set(compact('sacs'));
	}
	
	public function userupdate($id = null)
	{
		$this->AdAccount->id = $id;
		if (!$this->AdAccount->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('AD Account')));
		}
		
		if($this->AdAccount->nedUpdate($id))
		{
			$this->Flash->success(__('The %s has been updated.', __('AD Account')));
		}
		else
		{
			$this->Flash->error($this->AdAccount->modelError);
		}
		
		return $this->redirect($this->referer());
	}
	
	public function admin_division($division_id = null)  
	{ 
		$this->division($division_id);
	}
	
	public function admin_index()
	{
		return $this->redirect(array('action' => 'index', 'admin' => false));
	}

}
