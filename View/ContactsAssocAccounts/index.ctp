<?php 
// File: Plugin/Contacts/View/ContactsAssocAccounts/index.ctp

$page_title = (isset($page_title)?$page_title:__('Assocociated Accounts'));
$page_subtitle = (isset($page_subtitle)?$page_subtitle:false);
$_page_options = (isset($page_options)?$page_options:array());
$_page_options2 = (isset($page_options2)?$page_options2:array());
$search_placeholder = (isset($search_placeholder)?$search_placeholder:__('Assocociated Accounts'));
$search_model = (isset($search_model)?$search_model:'AssocAccount');
$_th = (isset($th)?$th:array());
$_td = (isset($td)?$td:array());

// javascript for table sorting that don't use pagination
$use_jsordering = (isset($use_jsordering)?$use_jsordering:false);

$page_options = array();

$add_url = array('action' => 'add');
if(isset($ad_account_id))
	$add_url[] = $ad_account_id;

$page_options['add'] = $this->Html->link(__('Add %s', __('Associated Account')), $add_url);

$page_options = array_merge($page_options, $_page_options);

$page_options2 = array();
$page_options2 = array_merge($page_options2, $_page_options2);

$th = array();
$th['AssocAccount.id'] = array('content' => __('ID'), 'options' => array('sort' => 'AssocAccount.id'));
$th['AssocAccount.path'] = array('content' => __('Path'));
$th['AdAccount.name'] = array('content' => __('AD Account'), 'options' => array('sort' => 'AdAccount.name'));
$th['AssocAccount.username'] = array('content' => __('Username'), 'options' => array('sort' => 'AssocAccount.username'));
$th['AssocAccount.name'] = array('content' => __('Name'), 'options' => array('sort' => 'AssocAccount.name'));
$th['AssocAccount.email'] = array('content' => __('Email'), 'options' => array('sort' => 'AssocAccount.email'));
$th['AssocAccount.phone_number'] = array('content' => __('Phone Number'), 'options' => array('sort' => 'AdAccount.phone_number'));

$th = array_merge($th, $_th);
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = $_td;
foreach ($assocAccounts as $i => $assocAccount)
{
	$actions = array(
		$this->Html->link(__('View'), array('action' => 'view', $assocAccount['AssocAccount']['id'])),
		$this->Html->link(__('Edit'), array('action' => 'edit', $assocAccount['AssocAccount']['id'])),
	);
	
	$edit_id = array(
		'AssocAccount' => $assocAccount['AssocAccount']['id'],
		'AdAccount' => $assocAccount['AdAccount']['id'],
	);
	
	$td[$i] = array(
		'AssocAccount.id' => $this->Html->link($assocAccount['AssocAccount']['id'], array('action' => 'view', $assocAccount['AssocAccount']['id'])),
		'AssocAccount.path' =>  $this->Contacts->makePath($assocAccount),
		'AdAccount.username' => $this->Html->link($assocAccount['AdAccount']['username'], array('controller' => 'ad_accounts', 'action' => 'view', $assocAccount['AdAccount']['id'])),
		'AssocAccount.username' => $this->Html->link($assocAccount['AssocAccount']['username'], array('action' => 'view', $assocAccount['AssocAccount']['id'])),
		'AssocAccount.name' => $assocAccount['AssocAccount']['name'],
		'AssocAccount.email' => $assocAccount['AssocAccount']['email'],
		'AssocAccount.phone_number' => $assocAccount['AssocAccount']['phone_number'],
	);
	
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
	$td[$i]['actions'] = array(
		implode("", $actions),
		array('class' => 'actions'),
	);
	$td[$i]['edit_id'] = $edit_id;
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
}

$use_gridedit = false;
if($this->Wrap->roleCheck(array('admin', 'saa')))
{
	$use_gridedit = true;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => $page_title,
	'page_subtitle' => $page_subtitle,
	'page_options' => $page_options,
	'page_options2' => $page_options2,
	'search_placeholder' => $search_placeholder,
	'search_model' => $search_model,
	'th' => $th,
	'td' => $td,
	'use_gridedit' => $use_gridedit,
));