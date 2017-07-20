<?php

App::uses('AppController', 'Controller');

class ContactsAppController extends AppController 
{
	public $components = array(
		// Common functions we would like to have all apps available to them
		'Utilities.Common',
	);
	public $helpers = array(
		'Contacts.Contacts',
	);
	
	public $conditions = array();
	
	public function beforeRender() 
	{
		$this->set('Model', $this->{$this->modelClass});
		parent::beforeRender();
	}
	
	public function duplicates()
	{	
		$conditions = $this->{$this->modelClass}->duplicateConditions();
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('With duplicate records.'));
		$this->conditions = $conditions;
		$this->index();
	}
	
	public function orgchart()
	{
		$orgList = $this->{$this->modelClass}->Contacts_orgChart();
		
		$this->set('modelNice', Inflector::humanize($this->{$this->modelClass}->alias));
		$this->set('orgList', $orgList);
		
		$this->view = 'Contacts./Elements/orgchart';
		return $this->render();
	}
}