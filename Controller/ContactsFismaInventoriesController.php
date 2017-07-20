<?php

App::uses('ContactsAppController', 'Contacts.Controller');

class ContactsFismaInventoriesController extends ContactsAppController
{
	public function beforeFilter()
	{
		if(isset($this->ContactsFismaInventory))
			$this->FismaInventory = &$this->ContactsFismaInventory;
		
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
				'FismaSystem', 'FismaSystem.OwnerContact', 'FismaSystem.OwnerContact.Sac', 
				'FismaSystem.OwnerContact.Sac.Branch', 'FismaSystem.OwnerContact.Sac.Branch.Division', 
				'FismaSystem.OwnerContact.Sac.Branch.Division.Org'
			], $this->paginate['contain']);
		}
		$this->paginate['conditions'] = $this->FismaInventory->conditions($conditions, $this->passedArgs);
		
		$records = $this->paginate();
		
		$this->set(compact(['records']));
	}
	
	public function fisma_system($id = false)
	{
		if(!$record = $this->FismaInventory->FismaSystem->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('FISMA Inventory')));
		}
		$this->set('record', $record);
		
		$this->conditions = [
			'FismaInventory.fisma_system_id' => $id,
		];
		return $this->index();
	}
	
	public function fisma_system_all($id = false)
	{
		$this->FismaInventory->FismaSystem->id = $id;
		$children_ids = $this->FismaInventory->FismaSystem->find('MyChildren', [
			'type' => 'list',
			'fields' => ['FismaSystem.id', 'FismaSystem.id'],
		]);
		
		if(!count($children_ids))
		{
			$children_ids = $id;
		}
		else
		{
			$children_ids[$id] = $id;
		}
		
		$this->conditions = [
			'FismaInventory.fisma_system_id' => $children_ids,
		];
		
		return $this->index();
	}
	
	public function fisma_system_children($id = false)
	{
		$this->FismaInventory->FismaSystem->id = $id;
		$children_ids = $this->FismaInventory->FismaSystem->find('MyChildren', [
			'type' => 'list',
			'fields' => ['FismaSystem.id', 'FismaSystem.id'],
		]);
		
		$this->conditions = [
			'FismaInventory.fisma_system_id' => $children_ids,
		];
		
		return $this->index();
	}
	
	public function view($id = false) 
	{
		$this->FismaInventory->contain([
			'FismaSystem', 'FismaSystem.OwnerContact', 'FismaSystem.OwnerContact.Sac', 
			'FismaSystem.OwnerContact.Sac.Branch', 'FismaSystem.OwnerContact.Sac.Branch.Division', 
			'FismaSystem.OwnerContact.Sac.Branch.Division.Org'
		]);
		if(!$record = $this->FismaInventory->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('FISMA Inventory')));
		}
		$this->set('record', $record);
	}
}