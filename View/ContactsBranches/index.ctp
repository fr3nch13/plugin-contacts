<?php 
// File: Plugin/Contacts/View/ContactsBranches/index.ctp

$page_title = (isset($page_title)?$page_title:__('Branches'));
$page_subtitle = (isset($page_subtitle)?$page_subtitle:false);
$_page_options = (isset($page_options)?$page_options:array());
$_page_options2 = (isset($page_options2)?$page_options2:array());
$search_placeholder = (isset($search_placeholder)?$search_placeholder:__('Branches'));
$search_model = (isset($search_model)?$search_model:'Branch');
$_th = (isset($th)?$th:array());
$_td = (isset($td)?$td:array());

// javascript for table sorting that don't use pagination
$use_jsordering = (isset($use_jsordering)?$use_jsordering:false);

$page_options = array();
if($this->Wrap->roleCheck(array('admin', 'saa')))
{
	$page_options['add'] = $this->Html->link(__('Add %s', __('Branch')), array('action' => 'add'));
}
$page_options = array_merge($page_options, $_page_options);

$page_options2 = array();
$page_options2 = array_merge($page_options2, $_page_options2);

// content
$th = array();
$th['Branch.path'] = array('content' => __('Path'));
$th['Branch.division_id'] = array('content' => __('Division'), 'options' => array('sort' => 'Division.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $divisions) ));
$th['Branch.shortname'] = array('content' => __('Short Name'), 'options' => array('sort' => 'Branch.shortname', 'editable' => array('type' => 'text') ));
$th['Branch.name'] = array('content' => __('Normal Name'), 'options' => array('sort' => 'Branch.name', 'editable' => array('type' => 'text') ));
$th['Branch.director_id'] = array('content' => __('Director'), 'options' => array('sort' => 'BranchDirector.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $adAccounts) ));
$th['Branch.crm_id'] = array('content' => __('CRM'), 'options' => array('sort' => 'BranchCrm.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $adAccounts) ));
// merge any other columns here
$th = array_merge($th, $_th);

$th['Sac.count'] = array('content' => __('# %s', __('SACs')));
$th['AdAccount.count'] = array('content' => __('# %s', __('AD Accounts')));
$th['AssocAccount.count'] = array('content' => __('# %s', __('Assoc Accounts')));


$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$filterLink = array('value' => false);
if(isset($passedArgs[0]))
	$filterLink = array($passedArgs[0], 'value' => false);

$td = $_td;
foreach ($branches as $i => $branch)
{
	$actions = array(
		'view' => $this->Html->link(__('View'), array('action' => 'view', $branch['Branch']['id'])),
	);
	
	if($this->Wrap->roleCheck(array('admin', 'saa')))
	{
		$actions['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $branch['Branch']['id']));
		$actions['add'] = $this->Html->link(__('Duplicate'), array('action' => 'add', $branch['Branch']['id']));
		$actions['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $branch['Branch']['id'], 'admin' => true), array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array();
	$td[$i]['Branch.path'] = $this->Contacts->makePath($branch);
	$td[$i]['Branch.division_id'] = array(
		$this->Html->link($branch['Division']['shortname'], array('controller' => 'divisions', 'action' => 'view', $branch['Division']['id'])),
		array('value' => $branch['Division']['id']),
	);
	$td[$i]['Branch.shortname'] = $this->Html->link($branch['Branch']['shortname'], array('action' => 'view', $branch['Branch']['id']));
	$td[$i]['Branch.name'] = $this->Html->link($branch['Branch']['name'], array('action' => 'view', $branch['Branch']['id']));
	$td[$i]['Branch.director_id'] = array(
		$this->Html->link($branch['BranchDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $branch['BranchDirector']['id'])),
		array('value' => $branch['BranchDirector']['id']),
	);
	$td[$i]['Branch.crm_id'] = array(
		$this->Html->link($branch['BranchCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $branch['BranchCrm']['id'])),
		array('value' => $branch['BranchCrm']['id']),
	);
	
	// merge any other columns here
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);

	$tabCount = $this->Contacts->findTabCount($td[$i]);
		
	$td[$i]['Sac.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'sacs', 'action' => 'branch', $branch['Branch']['id']), 
		'url' => array('action' => 'view', $branch['Branch']['id'], 'tab' => 'Sacs'),
	));
	$td[$i]['AdAccount.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'ad_accounts', 'action' => 'branch', $branch['Branch']['id']), 
		'url' => array('action' => 'view', $branch['Branch']['id'], 'tab' => 'AdAccounts'),
	));
	$td[$i]['AssocAccount.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'assoc_accounts', 'action' => 'branch', $branch['Branch']['id']), 
		'url' => array('action' => 'view', $branch['Branch']['id'], 'tab' => 'AssocAccounts'),
	));
	$td[$i]['edit_id'] = array(
		'Branch' => $branch['Branch']['id'],
	);
	$td[$i]['actions'] = array(
		implode("\n", $actions),
		array('class' => 'actions'),
	);
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
		
}

$use_gridedit = $use_griddelete = $user_gridadd = false;
if($this->Wrap->roleCheck(array('admin', 'saa')))
	$use_gridedit = $use_griddelete = $user_gridadd = true;

echo $this->element('Utilities.page_index', array(
	'page_title' => $page_title,
	'page_subtitle' => $page_subtitle,
	'page_options' => $page_options,
	'page_options2' => $page_options2,
	'search_placeholder' => $search_placeholder,
	'search_model' => $search_model,
	'th' => $th,
	'td' => $td,
	'use_jsordering' => $use_jsordering,
	'use_gridedit' => $use_gridedit,
));