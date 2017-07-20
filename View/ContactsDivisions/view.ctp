<?php 
// File: Plugin/Contacts/View/ContactsDivisions/view.ctp
$_page_options = (isset($page_options)?$page_options:array());
$_details = (isset($details)?$details:array());
$_stats = (isset($stats)?$stats:array());
$_tabs = (isset($tabs)?$tabs:array());

$page_options = array();

if($this->Wrap->roleCheck(array('admin', 'saa')))
{
	$page_options['edit'] = $this->Html->link(__('Edit'), array('action' => 'edit', $division['Division']['id']));
	$page_options['duplicate'] = $this->Html->link(__('Duplicate'), array('action' => 'add', $division['Division']['id']));
	$page_options['delete'] = $this->Html->link(__('Delete'), array('action' => 'delete', $division['Division']['id'], 'admin' => true), array('confirm' => 'Are you sure?'));
}
$page_options = array_merge($page_options, $_page_options);

$details = array(
	'Division.shortname' => array('name' => __('Short Name'), 'value' =>$division['Division']['shortname']),
	'Division.name' => array('name' => __('Full Name'), 'value' =>$division['Division']['name']),
	'DivisionDirector.name' => array('name' => __('Director'), 'value' => $this->Html->link($division['DivisionDirector']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $division['DivisionDirector']['id']))),
	'DivisionCrm.name' => array('name' => __('CRM'), 'value' => $this->Html->link($division['DivisionCrm']['name_username_email'], array('controller' => 'ad_accounts', 'action' => 'view', $division['DivisionCrm']['id']))),
	'Division.created' => array('name' => __('Created'), 'value' => $this->Wrap->niceTime($division['Division']['created'])),
);
$details = array_merge($details, $_details);

$stats = array();
$tabs = array();

$stats = array_merge($stats, $_stats);
$tabs = array_merge($tabs, $_tabs);

$stats['Branches'] = array(
	'id' => 'Branches',
	'name' => __('Branches'),
	'ajax_count_url' => array('controller' => 'branches', 'action' => 'division', $division['Division']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['Branches'] = array(
	'key' => 'Branches',
	'title' => __('Branches'),
	'url' => array('controller' => 'branches', 'action' => 'division', $division['Division']['id']),
);

$stats['Sacs'] = array(
	'id' => 'Sacs',
	'name' => __('Sacs'),
	'ajax_count_url' => array('controller' => 'sacs', 'action' => 'division', $division['Division']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['Sacs'] = array(
	'key' => 'Sacs',
	'title' => __('Sacs'),
	'url' => array('controller' => 'sacs', 'action' => 'division', $division['Division']['id']),
);

$stats['AdAccounts'] = array(
	'id' => 'AdAccounts',
	'name' => __('AD Accounts'),
	'tip' => __('%s assigned to them.', __('AD Accounts')),
	'ajax_count_url' => array('controller' => 'ad_accounts', 'action' => 'division', $division['Division']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['AdAccounts'] = array(
	'key' => 'AdAccounts',
	'title' => __('AD Accounts'),
	'url' => array('controller' => 'ad_accounts', 'action' => 'division', $division['Division']['id']),
);

$stats['AssocAccounts'] = array(
	'id' => 'AssocAccounts',
	'name' => __('Associated'),
	'tip' => __('Associated Accounts'),
	'ajax_count_url' => array('controller' => 'assoc_accounts', 'action' => 'division', $division['Division']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);
$tabs['AssocAccounts'] = array(
	'key' => 'AssocAccounts',
	'title' => __('Associated Accounts'),
	'url' => array('controller' => 'assoc_accounts', 'action' => 'division', $division['Division']['id']),
);

$stats['tags'] = array(
	'id' => 'tags',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'division', $division['Division']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs['tags'] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'division', $division['Division']['id']),
);

echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s: %s', __('Division'), $this->Contacts->makePath($division)),
	'page_options' => $page_options,
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));