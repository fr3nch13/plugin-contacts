<?php 
// File: Plugin/Contacts/View/ContactsDivisions/index.ctp

$page_title = (isset($page_title)?$page_title:__('Divisions'));
$page_subtitle = (isset($page_subtitle)?$page_subtitle:false);
$_page_options = (isset($page_options)?$page_options:array());
$_page_options2 = (isset($page_options2)?$page_options2:array());
$search_placeholder = (isset($search_placeholder)?$search_placeholder:__('Divisions'));
$search_model = (isset($search_model)?$search_model:'Division');
$_th = (isset($th)?$th:array());
$_td = (isset($td)?$td:array());

// javascript for table sorting that don't use pagination
$use_jsordering = (isset($use_jsordering)?$use_jsordering:false);

$page_options = array();
if($this->Wrap->roleCheck(array('admin', 'saa')))
{
	$page_options['add'] = $this->Html->link(__('Add %s', __('Division')), array('action' => 'add'));
}
$page_options = array_merge($page_options, $_page_options);

$page_options2 = array();
$page_options2 = array_merge($page_options2, $_page_options2);

// content
$th = array();
$th['Division.path'] = array('content' => __('Path'));
$th['Division.org_id'] = array('content' => __('ORG/IC'), 'options' => array('sort' => 'Org.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $orgs) ));
$th['Division.shortname'] = array('content' => __('Short Name'), 'options' => array('sort' => 'Division.shortname', 'editable' => array('type' => 'text') ));
$th['Division.name'] = array('content' => __('Normal Name'), 'options' => array('sort' => 'Division.name', 'editable' => array('type' => 'text') ));
$th['Division.director_id'] = array('content' => __('Director'), 'options' => array('sort' => 'DivisionDirector.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $adAccounts) ));
$th['Division.crm_id'] = array('content' => __('CRM'), 'options' => array('sort' => 'DivisionCrm.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $adAccounts) ));

$th = array_merge($th, $_th);
$th['Branch.count'] = array('content' => __('# %s', __('Branches')));
$th['Sac.count'] = array('content' => __('# %s', __('SACs')));
$th['AdAccount.count'] = array('content' => __('# %s', __('AD Accounts')));
$th['AssocAccount.count'] = array('content' => __('# %s', __('Assoc Accounts')));
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$filterLink = array('value' => false);
if(isset($passedArgs[0]))
	$filterLink = array($passedArgs[0], 'value' => false);

$td = $_td;
foreach ($divisions as $i => $division)
{
	$actions = array(
		'view' => $this->Html->link(__('View'), array('action' => 'view', $division['Division']['id'])),
		'edit' => $this->Html->link(__('Edit'), array('action' => 'edit', $division['Division']['id'])),
		'add' => $this->Html->link(__('Duplicate'), array('action' => 'add', $division['Division']['id'])),
	);
	
	if($this->Wrap->roleCheck(array('admin', 'saa')))
	{
		$actions['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $division['Division']['id'], 'admin' => true), array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array();
	$td[$i]['Division.path'] = $this->Contacts->makePath($division);
	$td[$i]['Division.org_id'] = array(
		$this->Html->link($division['Org']['shortname'], array('controller' => 'orgs', 'action' => 'view', $division['Org']['id'])),
		array('value' => $division['Org']['id']),
	);
	$td[$i]['Division.shortname'] = $this->Html->link($division['Division']['shortname'], array('action' => 'view', $division['Division']['id']));
	$td[$i]['Division.name'] = $this->Html->link($division['Division']['name'], array('action' => 'view', $division['Division']['id']));
	$td[$i]['Division.director_id'] = array(
		$this->Html->link($division['DivisionDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $division['DivisionDirector']['id'])),
		array('value' => $division['DivisionDirector']['id']),
	);
	$td[$i]['Division.crm_id'] = array(
		$this->Html->link($division['DivisionCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $division['DivisionCrm']['id'])),
		array('value' => $division['DivisionCrm']['id']),
	);
	
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
	$tabCount = $this->Contacts->findTabCount($td[$i]);
		
	$td[$i]['Branch.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'branches', 'action' => 'division', $division['Division']['id']), 
		'url' => array('action' => 'view', $division['Division']['id'], 'tab' => 'Branches'),
	));
	$td[$i]['Sac.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'sacs', 'action' => 'division', $division['Division']['id']), 
		'url' => array('action' => 'view', $division['Division']['id'], 'tab' => 'Sacs'),
	));
	$td[$i]['AdAccount.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'ad_accounts', 'action' => 'division', $division['Division']['id']), 
		'url' => array('action' => 'view', $division['Division']['id'], 'tab' => 'AdAccounts'),
	));
	$td[$i]['AssocAccount.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'assoc_accounts', 'action' => 'division', $division['Division']['id']), 
		'url' => array('action' => 'view', $division['Division']['id'], 'tab' => 'AssocAccounts'),
	));
	$td[$i]['edit_id'] = array(
		'Division' => $division['Division']['id'],
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