<?php 
// File: Plugin/Contacts/View/ContactsAdAccounts/index.ctp
$alias = (isset($alias)?$alias:'AdAccount');
$adAccountAlias = (isset($adAccountAlias)?$adAccountAlias:__('AD Account'));
$adAccountAliases = (isset($adAccountAliases)?$adAccountAliases:__('AD Accounts'));

$page_title = (isset($page_title)?$page_title:$adAccountAliases);
$page_subtitle = (isset($page_subtitle)?$page_subtitle:false);
$_page_options = (isset($page_options)?$page_options:array());
$_page_options2 = (isset($page_options2)?$page_options2:array());

$use_gridedit = (isset($use_gridedit)?$use_gridedit:false);
$search_placeholder = (isset($search_placeholder)?$search_placeholder:$adAccountAliases);
$search_model = (isset($search_model)?$search_model:$this->Form->defaultModel);
$_th = (isset($th)?$th:array());
$_td = (isset($td)?$td:array());

// javascript for table sorting that don't use pagination
$use_jsordering = (isset($use_jsordering)?$use_jsordering:false);
$no_counts = (isset($no_counts)?$no_counts:false);

$page_options = array();
$page_options['add'] = $this->Html->link(__('Add %s', __('AD Account')), array('action' => 'add'));

$page_options = array_merge($page_options, $_page_options);

$page_options2 = array();
$page_options2 = array_merge($page_options2, $_page_options2);

$th = array();
$th[$alias.'.id'] = array('content' => __('ID'), 'options' => array('sort' => $alias.'.id' ));
$th[$alias.'.path'] = array('content' => __('Path'));
$th[$alias.'.sac_id'] = array('content' => __('SAC'), 'options' => array('sort' => 'Sac.shortname' ));
$th[$alias.'.name'] = array('content' => __('Name'), 'options' => array('sort' => $alias.'.name', 'editable' => array('type' => 'text')));
$th[$alias.'.username'] = array('content' => __('AD Username'), 'options' => array('sort' => $alias.'.username', 'editable' => array('type' => 'text')));
$th[$alias.'.userid'] = array('content' => __('NIH ID'), 'options' => array('sort' => $alias.'.userid', 'editable' => array('type' => 'text')));
$th[$alias.'.email'] = array('content' => __('Email'), 'options' => array('sort' => $alias.'.email', 'editable' => array('type' => 'text')));
$th[$alias.'.phone_number'] = array('content' => __('Phone Number'), 'options' => array('sort' => $alias.'.phone_number', 'editable' => array('type' => 'text')));
$th = array_merge($th, $_th);
if(!$no_counts) $th['AssocAccount.count'] = array('content' => __('# %s', __('Assoc Accounts')));
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = $_td;
$totals_row = array();
$totals = array();
foreach ($adAccounts as $i => $adAccount)
{
	$actions = array(
		$this->Html->link(__('View'), array('controller' => 'ad_accounts', 'action' => 'view', $adAccount[$alias]['id'])),
		$this->Html->link(__('Edit'), array('controller' => 'ad_accounts', 'action' => 'edit', $adAccount[$alias]['id'])),
	);
	
	if(AuthComponent::user('role') == 'admin')
	{
		$actions[] = $this->Html->link(__('Delete'), array('controller' => 'ad_accounts', 'action' => 'delete', $adAccount[$alias]['id'], 'admin' => true), array('confirm' => 'Are you sure?'));
	}
	
	$edit_id = array(
		$alias => $adAccount[$alias]['id'],
	);
	
	$td[$i] = array();
	$td[$i][$alias.'.id'] = $this->Html->link($adAccount[$alias]['id'], array('controller' => 'ad_accounts', 'action' => 'view', $adAccount[$alias]['id'], 'admin' => false));
	$td[$i][$alias.'.path'] = $this->Contacts->makePath($adAccount);
	$td[$i][$alias.'.sac_id'] = array(
		(isset($adAccount['Sac']['shortname'])?$this->Html->link($adAccount['Sac']['shortname'], array('controller' => 'sacs', 'action' => 'view', $adAccount['Sac']['id'], 'admin' => false)):'&nbsp;'),
		array('value' => $adAccount['Sac']['id']),
	);
	$td[$i][$alias.'.name'] = $this->Html->link($adAccount[$alias]['name'], array('controller' => 'ad_accounts', 'action' => 'view', $adAccount[$alias]['id'], 'admin' => false));
	$td[$i][$alias.'.username'] = $this->Html->link($adAccount[$alias]['username'], array('controller' => 'ad_accounts', 'action' => 'view', $adAccount[$alias]['id'], 'admin' => false));
	$td[$i][$alias.'.userid'] = $this->Html->userLink($adAccount[$alias]['userid']);
	$td[$i][$alias.'.email'] = $this->Html->link($adAccount[$alias]['email'], 'mailto:'.$adAccount[$alias]['email']);
	$td[$i][$alias.'.phone_number'] = $adAccount[$alias]['phone_number'];
	
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
	
	if(!$no_counts)
	{
		$tabCount = $this->Contacts->findTabCount($td[$i]);
		$td[$i]['AssocAccount.count'] = array('.', array(
			'ajax_count_url' => array('controller' => 'assoc_accounts', 'action' => 'ad_account', $adAccount[$alias]['id']), 
			'url' => array('action' => 'view', $adAccount[$alias]['id'], 'tab' => 'AssocAccounts'),
		));
	}
	
	$td[$i]['actions'] = array(
		implode("", $actions),
		array('class' => 'actions'),
	);
	$td[$i]['edit_id'] = $edit_id;
	if(isset($_td[$i]))
		$td[$i] = array_merge($td[$i], $_td[$i]);
}

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