<?php 
// File: Plugin/Contacts/View/ContactsOrgs/index.ctp

$page_title = (isset($page_title)?$page_title:__('ORG/ICs'));
$page_subtitle = (isset($page_subtitle)?$page_subtitle:false);
$_page_options = (isset($page_options)?$page_options:array());
$_page_options2 = (isset($page_options2)?$page_options2:array());
$search_placeholder = (isset($search_placeholder)?$search_placeholder:__('ORG/ICs'));
$search_model = (isset($search_model)?$search_model:'Org');
$_th = (isset($th)?$th:array());
$_td = (isset($td)?$td:array());

// javascript for table sorting that don't use pagination
$use_jsordering = (isset($use_jsordering)?$use_jsordering:false);

$page_options = array();
if($this->Wrap->roleCheck(array('admin', 'saa')))
{
	$page_options['add'] = $this->Html->link(__('Add %s', __('ORG/IC')), array('action' => 'add'));
}
$page_options = array_merge($page_options, $_page_options);

$page_options2 = array();
$page_options2 = array_merge($page_options2, $_page_options2);

// content
$th = array();
$th['Org.path'] = array('content' => __('Path'));
$th['Org.shortname'] = array('content' => __('Short Name'), 'options' => array('sort' => 'Org.shortname', 'editable' => array('type' => 'text') ));
$th['Org.name'] = array('content' => __('Normal Name'), 'options' => array('sort' => 'Org.name', 'editable' => array('type' => 'text') ));
$th['Org.director_id'] = array('content' => __('Director'), 'options' => array('sort' => 'OrgDirector.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $adAccounts) ));
$th['Org.crm_id'] = array('content' => __('CRM'), 'options' => array('sort' => 'OrgCrm.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $adAccounts) ));

$th = array_merge($th, $_th);
$th['Division.count'] = array('content' => __('# %s', __('Divisions')));
$th['Branch.count'] = array('content' => __('# %s', __('Branches')));
$th['Sac.count'] = array('content' => __('# %s', __('SACs')));
$th['AdAccount.count'] = array('content' => __('# %s', __('AD Accounts')));
$th['AssocAccount.count'] = array('content' => __('# %s', __('Assoc Accounts')));

$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$filterLink = array('value' => false);
if(isset($passedArgs[0]))
	$filterLink = array($passedArgs[0], 'value' => false);

$td = $_td;
foreach ($orgs as $i => $org)
{
	$actions = array(
		'view' => $this->Html->link(__('View'), array('action' => 'view', $org['Org']['id'])),
	);
	
	if($this->Wrap->roleCheck(array('admin', 'saa')))
	{
		$actions['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $org['Org']['id']));
		$actions['add'] = $this->Html->link(__('Duplicate'), array('action' => 'add', $org['Org']['id']));
		$actions['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $org['Org']['id'], 'admin' => true), array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array();
	$td[$i]['Org.path'] = $this->Contacts->makePath($org);
	$td[$i]['Org.shortname'] = $this->Html->link($org['Org']['shortname'], array('action' => 'view', $org['Org']['id']));
	$td[$i]['Org.name'] = $this->Html->link($org['Org']['name'], array('action' => 'view', $org['Org']['id']));
	$td[$i]['Org.director_id'] = array(
		$this->Html->link($org['OrgDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $org['OrgDirector']['id'])),
		array('value' => $org['OrgDirector']['id']),
	);
	$td[$i]['Org.crm_id'] = array(
		$this->Html->link($org['OrgCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $org['OrgCrm']['id'])),
		array('value' => $org['OrgCrm']['id']),
	);
	
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
	$tabCount = $this->Contacts->findTabCount($td[$i]);
	
	$td[$i]['Division.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'divisions', 'action' => 'org', $org['Org']['id']), 
		'url' => array('action' => 'view', $org['Org']['id'], 'tab' => 'Divisions'),
	));
	$td[$i]['Branch.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'branches', 'action' => 'org', $org['Org']['id']), 
		'url' => array('action' => 'view', $org['Org']['id'], 'tab' => 'Branches'),
	));
	$td[$i]['Sac.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'sacs', 'action' => 'org', $org['Org']['id']), 
		'url' => array('action' => 'view', $org['Org']['id'], 'tab' => 'Sacs'),
	));
	$td[$i]['AdAccount.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'ad_accounts', 'action' => 'org', $org['Org']['id']), 
		'url' => array('action' => 'view', $org['Org']['id'], 'tab' => 'AdAccounts'),
	));
	$td[$i]['AssocAccount.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'assoc_accounts', 'action' => 'org', $org['Org']['id']), 
		'url' => array('action' => 'view', $org['Org']['id'], 'tab' => 'AssocAccounts'),
	));
	
	$td[$i]['edit_id'] = array(
		'Org' => $org['Org']['id'],
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