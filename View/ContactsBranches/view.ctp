<?php 
// File: Plugin/Contacts/View/ContactsBranches/view.ctp
$_page_options = (isset($page_options)?$page_options:array());
$_details = (isset($details)?$details:array());
$_stats = (isset($stats)?$stats:array());
$_tabs = (isset($tabs)?$tabs:array());

$page_options = array();

if($this->Wrap->roleCheck(array('admin', 'saa')))
{
	$page_options['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $branch['Branch']['id']));
	$page_options['duplicate'] = $this->Html->link(__('Duplicate'), array('action' => 'add', $branch['Branch']['id']));
	$page_options['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $branch['Branch']['id'], 'admin' => true), array('confirm' => 'Are you sure?'));
}
$page_options = array_merge($page_options, $_page_options);

$details = array(
	'Branch.shortname' => array('name' => __('Short Name'), 'value' =>$branch['Branch']['shortname']),
	'Branch.name' => array('name' => __('Full Name'), 'value' =>$branch['Branch']['name']),
	'BranchDirector.name' => array('name' => __('Director'), 'value' => $this->Html->link($branch['BranchDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $branch['BranchDirector']['id']))),
	'BranchCrm.name' => array('name' => __('CRM'), 'value' => $this->Html->link($branch['BranchCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $branch['BranchCrm']['id']))),
	'Branch.created' => array('name' => __('Created'), 'value' => $this->Wrap->niceTime($branch['Branch']['created'])),
);
$details = array_merge($details, $_details);

$stats = array();
$tabs = array();

$stats = array_merge($stats, $_stats);
$tabs = array_merge($tabs, $_tabs);

$stats['Sacs'] = array(
	'id' => 'Sacs',
	'name' => __('Sacs'),
	'ajax_count_url' => array('controller' => 'sacs', 'action' => 'branch', $branch['Branch']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['Sacs'] = array(
	'key' => 'Sacs',
	'title' => __('Sacs'),
	'url' => array('controller' => 'sacs', 'action' => 'branch', $branch['Branch']['id']),
);

$stats['AdAccounts'] = array(
	'id' => 'AdAccounts',
	'name' => __('AD Accounts'),
	'tip' => __('%s assigned to them.', __('AD Accounts')),
	'ajax_count_url' => array('controller' => 'ad_accounts', 'action' => 'branch', $branch['Branch']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['AdAccounts'] = array(
	'key' => 'AdAccounts',
	'title' => __('AD Accounts'),
	'url' => array('controller' => 'ad_accounts', 'action' => 'branch', $branch['Branch']['id']),
);

$stats['AssocAccounts'] = array(
	'id' => 'AssocAccounts',
	'name' => __('Associated'),
	'tip' => __('Associated Accounts'),
	'ajax_count_url' => array('controller' => 'assoc_accounts', 'action' => 'branch', $branch['Branch']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['AssocAccounts'] = array(
	'key' => 'AssocAccounts',
	'title' => __('Associated Accounts'),
	'url' => array('controller' => 'assoc_accounts', 'action' => 'branch', $branch['Branch']['id']),
);

$stats['tags'] = array(
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'branch', $branch['Branch']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs['tags'] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'branch', $branch['Branch']['id']),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s: %s', __('Branch'), $this->Contacts->makePath($branch)),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));