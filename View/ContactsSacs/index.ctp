<?php 
// File: Plugin/Contacts/View/ContactsSacs/index.ctp

$page_title = (isset($page_title)?$page_title:__('SACs'));
$page_subtitle = (isset($page_subtitle)?$page_subtitle:false);
$_page_options = (isset($page_options)?$page_options:array());
$_page_options2 = (isset($page_options2)?$page_options2:array());
$search_placeholder = (isset($search_placeholder)?$search_placeholder:__('SACs'));
$search_model = (isset($search_model)?$search_model:'Sac');
$_th = (isset($th)?$th:array());
$_td = (isset($td)?$td:array());

// javascript for table sorting that don't use pagination
$use_jsordering = (isset($use_jsordering)?$use_jsordering:false);

$page_options = array();
if($this->Wrap->roleCheck(array('admin', 'saa')))
{
	$page_options['add'] = $this->Html->link(__('Add %s', __('SAC')), array('action' => 'add'));
}
$page_options = array_merge($page_options, $_page_options);

$page_options2 = array();
$page_options2 = array_merge($page_options2, $_page_options2);

// content
$th = array();
$th['Sac.path'] = array('content' => __('Path'));
$th['Sac.branch_id'] = array('content' => __('Branch'), 'options' => array('sort' => 'Org.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $branches) ));
$th['Sac.shortname'] = array('content' => __('Short Name'), 'options' => array('sort' => 'Sac.shortname', 'editable' => array('type' => 'text') ));
$th['Sac.name'] = array('content' => __('Normal Name'), 'options' => array('sort' => 'Sac.name', 'editable' => array('type' => 'text') ));
$th['Sac.director_id'] = array('content' => __('Director'), 'options' => array('sort' => 'SacDirector.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $adAccounts) ));
$th['Sac.crm_id'] = array('content' => __('CRM'), 'options' => array('sort' => 'SacCrm.name', 'editable' => array('type' => 'select', 'searchable' => true, 'options' => $adAccounts) ));

$th = array_merge($th, $_th);
$th['AdAccount.count'] = array('content' => __('# %s', __('AD Accounts')));
$th['AssocAccount.count'] = array('content' => __('# %s', __('Assoc Accounts')));

$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$filterLink = array('value' => false);
if(isset($passedArgs[0]))
	$filterLink = array($passedArgs[0], 'value' => false);

$td = $_td;
foreach ($sacs as $i => $sac)
{
	$actions = array(
		'view' => $this->Html->link(__('View'), array('action' => 'view', $sac['Sac']['id'])),
		'edit' => $this->Html->link(__('Edit'), array('action' => 'edit', $sac['Sac']['id'])),
		'add' => $this->Html->link(__('Duplicate'), array('action' => 'add', $sac['Sac']['id'])),
	);
	
	if($this->Wrap->roleCheck(array('admin', 'saa')))
	{
		$actions['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $sac['Sac']['id'], 'admin' => true), array('confirm' => 'Are you sure?'));
	}
	
	$td[$i] = array();
	$td[$i]['Sac.path'] = $this->Contacts->makePath($sac);
	$td[$i]['Sac.branch_id'] = array(
		$this->Html->link($sac['Branch']['shortname'], array('controller' => 'branches', 'action' => 'view', $sac['Branch']['id'])),
		array('value' => $sac['Branch']['id']),
	);
	$td[$i]['Sac.shortname'] = $this->Html->link($sac['Sac']['shortname'], array('action' => 'view', $sac['Sac']['id']));
	$td[$i]['Sac.name'] = $this->Html->link($sac['Sac']['name'], array('action' => 'view', $sac['Sac']['id']));
	$td[$i]['Sac.director_id'] = array(
		$this->Html->link($sac['SacDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['SacDirector']['id'])),
		array('value' => $sac['SacDirector']['id']),
	);
	$td[$i]['Sac.crm_id'] = array(
		$this->Html->link($sac['SacCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $sac['SacCrm']['id'])),
		array('value' => $sac['SacCrm']['id']),
	);
	
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
	$tabCount = $this->Contacts->findTabCount($td[$i]);
	
	$td[$i]['AdAccount.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'ad_accounts', 'action' => 'sac', $sac['Sac']['id']), 
		'url' => array('action' => 'view', $sac['Sac']['id'], 'tab' => 'AdAccounts'),
	));
	$td[$i]['AssocAccount.count'] = array('.', array(
		'ajax_count_url' => array('controller' => 'assoc_accounts', 'action' => 'sac', $sac['Sac']['id']), 
		'url' => array('action' => 'view', $sac['Sac']['id'], 'tab' => 'AssocAccounts'),
	));
	$td[$i]['edit_id'] = array(
		'Sac' => $sac['Sac']['id'],
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