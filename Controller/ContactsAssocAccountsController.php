<?php
App::uses('ContactsAppController', 'Contacts.Controller');

class ContactsAssocAccountsController extends ContactsAppController
{
	public $uses = array('AssocAccount');
	public $allowAdminDelete = true;

	public function autocomplete()
	{
		$query = false;
		if(isset($this->request->query['query']))
			$query = $this->request->query['query'];
		
		$results = $this->AssocAccount->autocompleteLookup($query);
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
			$this->paginate['contain'] = array('AdAccount', 'AdAccount.Sac', 'AdAccount.Sac.Branch', 'AdAccount.Sac.Branch.Division', 'AdAccount.Sac.Branch.Division.Org');
		$this->paginate['order'] = array('AssocAccount.username' => 'asc');
		$this->paginate['conditions'] = $this->AssocAccount->conditions($conditions, $this->passedArgs); 

		$assocAccounts = $this->paginate();
		$this->set('assocAccounts', $assocAccounts);
		
		$adAccounts = $this->AssocAccount->AdAccount->typeFormList();
		$this->set(compact(array('adAccounts')));
	}
	
	public function org($org_id = null)  
	{
		if (!$org_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Org')));
		}
		
		$org = $this->AssocAccount->AdAccount->Sac->Branch->Division->Org->read(null, $org_id);
		if (!$org) 
		{
			throw new NotFoundException(__('Invalid %s', __('Org')));
		}
		$this->set('org', $org);
		
		$divisionIds = $this->AssocAccount->AdAccount->Sac->Branch->Division->find('list', array(
			'conditions' => array(
				'Division.org_id' => $org_id,
			),
			'fields' => array('Division.id', 'Division.id'),
		));
		
		$branchIds = $this->AssocAccount->AdAccount->Sac->Branch->find('list', array(
			'conditions' => array(
				'Branch.division_id' => $divisionIds,
			),
			'fields' => array('Branch.id', 'Branch.id'),
		));
		
		$sacIds = $this->AssocAccount->AdAccount->Sac->find('list', array(
			'conditions' => array(
				'Sac.branch_id' => $branchIds,
			),
			'fields' => array('Sac.id', 'Sac.id'),
		));
		
		$adAccountIds = $this->AssocAccount->AdAccount->find('list', array(
			'conditions' => array(
				'AdAccount.sac_id' => $sacIds,
			),
			'fields' => array('AdAccount.id', 'AdAccount.id'),
		));
		
		$conditions = array(
			'AssocAccount.ad_account_id' => $adAccountIds,
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
		
		$division = $this->AssocAccount->AdAccount->Sac->Branch->Division->read(null, $division_id);
		if (!$division) 
		{
			throw new NotFoundException(__('Invalid %s', __('Division')));
		}
		$this->set('division', $division);
		
		$branchIds = $this->AssocAccount->AdAccount->Sac->Branch->find('list', array(
			'conditions' => array(
				'Branch.division_id' => $division_id,
			),
			'fields' => array('Branch.id', 'Branch.id'),
		));
		
		$sacIds = $this->AssocAccount->AdAccount->Sac->find('list', array(
			'conditions' => array(
				'Sac.branch_id' => $branchIds,
			),
			'fields' => array('Sac.id', 'Sac.id'),
		));
		
		$adAccountIds = $this->AssocAccount->AdAccount->find('list', array(
			'conditions' => array(
				'AdAccount.sac_id' => $sacIds,
			),
			'fields' => array('AdAccount.id', 'AdAccount.id'),
		));
		
		$conditions = array(
			'AssocAccount.ad_account_id' => $adAccountIds,
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
		
		$branch = $this->AssocAccount->AdAccount->Sac->Branch->read(null, $branch_id);
		if (!$branch) 
		{
			throw new NotFoundException(__('Invalid %s', __('Branch')));
		}
		$this->set('branch', $branch);
		
		$sacIds = $this->AssocAccount->AdAccount->Sac->find('list', array(
			'conditions' => array(
				'Sac.branch_id' => $branch_id,
			),
			'fields' => array('Sac.id', 'Sac.id'),
		));
		
		$adAccountIds = $this->AssocAccount->AdAccount->find('list', array(
			'conditions' => array(
				'AdAccount.sac_id' => $sacIds,
			),
			'fields' => array('AdAccount.id', 'AdAccount.id'),
		));
		
		$conditions = array(
			'AssocAccount.ad_account_id' => $adAccountIds,
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
		
		$sac = $this->AssocAccount->AdAccount->Sac->read(null, $sac_id);
		if (!$sac) 
		{
			throw new NotFoundException(__('Invalid %s', __('Sac')));
		}
		$this->set('sac', $sac);
		
		$adAccountIds = $this->AssocAccount->AdAccount->find('list', array(
			'conditions' => array(
				'AdAccount.sac_id' => $sac_id,
			),
			'fields' => array('AdAccount.id', 'AdAccount.id'),
		));
		
		$conditions = array(
			'AssocAccount.ad_account_id' => $adAccountIds,
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Under the %s of %s', __('SAC'), $sac['Sac']['shortname']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function ad_account($ad_account_id = false) 
	{
		$conditions = array();
		$conditions = array_merge($conditions, $this->conditions);
		
		if (!$adAccount =  $this->AssocAccount->AdAccount->read(null, $ad_account_id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('AD Account')));
		}
		$this->set('adAccount', $adAccount);
		
		$conditions = array(
			'AssocAccount.ad_account_id' => $ad_account_id,
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->conditions = $conditions;
		
		$this->set('page_subtitle', __('Under the %s of %s', __('AD Account'), $adAccount['AdAccount']['username']));
		$this->set('ad_account_id', $ad_account_id);
		$this->index();
	}
	
	public function orphans()
	{	
		$conditions = array(
			'AssocAccount.ad_account_id' => 0,
		);
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Not associated with an %s', __('AD Account')));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->AssocAccount->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$conditions = array();
		
		$conditions[] = $this->AssocAccount->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'AssocAccount');
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Tagged with %s', $tag['Tag']['name']));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function view($id = null) 
	{
		$this->AssocAccount->contain(array('AdAccount', 'AdAccount.Sac', 'AdAccount.Sac.Branch', 'AdAccount.Sac.Branch.Division', 'AdAccount.Sac.Branch.Division.Org'));
		if (!$assocAccount =  $this->AssocAccount->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Associated Account')));
		}
		$this->set('assocAccount', $assocAccount);
	}
	
	public function add($ad_account_id = false) 
	{
		$this->AssocAccount->validator()
			->add('username', 'required', array('rule' => 'notBlank'));
		
		if ($this->request->is(array('post', 'put'))) 
		{
			if ($this->AssocAccount->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('Associated Account')));
				return $this->redirect(array('action' => 'view', $this->AssocAccount->id));
			} 
			else 
			{
				$this->Flash->fail(__('The %s could not be saved. Please, try again.', __('Associated Account')));
			}
		}
		
		if($ad_account_id)
		{
			$this->request->data['AssocAccount']['ad_account_id'] = $ad_account_id;
		}
		
		$adAccounts = $this->AssocAccount->AdAccount->typeFormList();
		$this->set(compact('adAccounts'));
	}
	
	public function edit($id = null) 
	{
		$this->AssocAccount->contain(array('Tag'));
		if (!$assocAccount =  $this->AssocAccount->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Associated Account')));
		}
		
		if ($this->request->is(array('post', 'put'))) 
		{
			if ($this->AssocAccount->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved.', __('Associated Account')));
				return $this->redirect(array('action' => 'view', $id));
			} 
			else 
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Associated Account')));
			}
		} 
		else 
		{
			$this->request->data = $assocAccount;
		}
		$adAccounts = $this->AssocAccount->AdAccount->typeFormList();
		$this->set(compact('adAccounts'));
	}
	
	public function admin_index()
	{
		return $this->redirect(array('action' => 'index', 'admin' => false));
	}
}
